<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// Все проверки.
if (!is_login()) die('no login');


if (isset($_SERVER['HTTP_X_REQUESTED_FILENAME']))
	$fn = $_SERVER['HTTP_X_REQUESTED_FILENAME'];
else 
	die('no file');

if (isset($_SERVER['HTTP_X_REQUESTED_FILEUPDIR']))
	$page_id = $_SERVER['HTTP_X_REQUESTED_FILEUPDIR'];
else 
	die('no updir');

if (!is_numeric($page_id)) die('wrong updir');

mso_checkreferer();

$ext = strtolower(substr(strrchr($fn, '.'), 1));

$allowed_ext = explode('|', mso_get_option('allowed_types', 'general', 'mp3|gif|jpg|jpeg|png|zip|txt|rar|doc|rtf|pdf|html|htm|css|xml|odt|avi|wmv|flv|swf|wav|xls|7z|gz|bz2|tgz'));

if (!in_array($ext, $allowed_ext)) die('not allowed');

// Закончили проверки.

// Полный путь к каталогу.
$up_dir =  getinfo('uploads_dir') . '_pages/' . $page_id . '/';

// Сама загрузка файла и создание миниатюр.
_upload($up_dir, $fn);


/**
* Основная функция загрузки 
*/
function _upload($up_dir, $fn, $r = array())
{
	$fn = _slug($fn);
	$ext = strtolower(substr(strrchr($fn, '.'), 1));
	$name = substr($fn, 0, strlen($fn) - strlen($ext) - 1);

	// Если имя файла пустое, только расширение.
	if ($fn == '.' . $ext) $fn = '1' . $fn;

	// Если файл уже существует.
	if (file_exists($up_dir . $fn))
	{
		for ($i = 1; $i < 100; $i++)
		{
			$fn = $name . '-' . $i . '.' . $ext;
			if (!file_exists($up_dir . $fn)) break;
		}
	}
	
	file_put_contents( $up_dir . $fn, file_get_contents('php://input') );

	if (!in_array($ext, array('jpg', 'jpeg', 'png', 'gif')))
	{
		// Не картинка, загрузили, больше ничего не надо.
		echo ' DONE! <b>' . $fn . '</b>';
		return;
	}

	$resize_images = (isset($_SERVER['HTTP_X_REQUESTED_RESIZEIMAGES'])) ? $_SERVER['HTTP_X_REQUESTED_RESIZEIMAGES'] : mso_get_option('resize_images',   'general', '600');
	
	$resize_images_type = (isset($_SERVER['HTTP_X_REQUESTED_RESIZEIMAGESTYPE'])) ? $_SERVER['HTTP_X_REQUESTED_RESIZEIMAGESTYPE'] : 'width';
	
	$size_image_mini_w = (isset($_SERVER['HTTP_X_REQUESTED_SIZEIMAGEMINIW'])) ? $_SERVER['HTTP_X_REQUESTED_SIZEIMAGEMINIW'] : mso_get_option('size_image_mini', 'general', '150');
	
	$size_image_mini_h = (isset($_SERVER['HTTP_X_REQUESTED_SIZEIMAGEMINIH'])) ? $_SERVER['HTTP_X_REQUESTED_SIZEIMAGEMINIH'] : mso_get_option('size_image_mini', 'general', '150');
	
	// !!! тип по-умолчанию ставим resize_full_crop_center, поскольку опция image_mini_type пока еще старая в MaxSite CMS 0.94
	$image_mini_type = (isset($_SERVER['HTTP_X_REQUESTED_TYPERESIZE'])) ? $_SERVER['HTTP_X_REQUESTED_TYPERESIZE'] : 'resize_full_crop_center';
	
	require(getinfo('shared_dir') . 'stock/thumb/thumb.php');

	// У нас есть uploads_dir, а нужен url
	$url = str_replace(getinfo('uploads_dir'), getinfo('uploads_url'), $up_dir);

	// читаем exif на предмет ориентации
	$rotation = mso_exif_rotate($up_dir . $fn);
	
	// если нужно повернуть
	// если большая картинка то это может грузить сервер, зато сразу нужная ориентация
	if ($rotation)
	{
		echo ' ROTATE... ';
		thumb_rotate($up_dir . $fn, $rotation);
	}
	
	// текущие размеры
	$size = $new_size = getimagesize($up_dir . $fn);
	$width = $new_width = $size[0];
	$height = $new_height = $size[1];
	
	if ($resize_images_type == 'width')
	{
		if ($width > $resize_images) 
		{
			echo ' RESIZE (width) ... ';
			$new_width = $resize_images;
			$new_height = round($height / ($width/$new_width));
			thumb_generate($url . $fn, $new_width, $new_height, false, 'resize', true, '', false);
		}
		
	}
	elseif ($resize_images_type == 'height')
	{
		if ($height > $resize_images) 
		{
			echo ' RESIZE (height) ... ';
			$new_height = $resize_images;
			$new_width = round($width / ($height/$new_height));
			thumb_generate($url . $fn, $new_width, $new_height, false, 'resize', true, '', false);
		}
	}
	elseif ($resize_images_type == 'max') // автоматом вычисляем максимальную сторону
	{
		if ($width > $resize_images or $height > $resize_images)
		{
			echo ' RESIZE (max) ... ';
			
			if ($width > $height)
			{
				$new_width = $resize_images;
				$new_height = round($height / ($width/$new_width));
			}
			else
			{
				$new_height = $resize_images;
				$new_width = round($width / ($height/$new_height));
			}
			
			thumb_generate($url . $fn, $new_width, $new_height, false, 'resize', true, '', false);
		}
	}

	/*
	if ($width > $resize_images or $height > $resize_images)
	{
		echo ' RESIZE... ';
		
		if ($width > $height)
		{
			$new_width = $resize_images;
			$new_height = round($height / ($width/$new_width));
		}
		else
		{
			$new_height = $resize_images;
			$new_width = round($width / ($height/$new_height));
		}
		
		thumb_generate($url . $fn, $new_width, $new_height, false, 'resize', true, '', false);
	}
	
	*/
	
	// if ($rotation)
	// {
	// 	echo ' ROTATE... ';
	// 	thumb_rotate($up_dir . $fn, $rotation);
	// }
	
	// Создание ватермарки, если такая опция и есть нужный файл
	// TODO: ватермарку можно и текстом выводить... 
	$use_watermark = mso_get_option('use_watermark',   'general', '0');
	
	if ($use_watermark and file_exists(getinfo('uploads_dir') . 'watermark.png'))
	{
		echo ' WATERMARK... ';
		
		$watermark_type = mso_get_option('watermark_type',  'general', '1');
		
		thumb_watermark($up_dir . $fn, getinfo('uploads_dir') . 'watermark.png', $watermark_type);
	}

	// echo 'mini ';
	echo ' MINI... ';
	
	// миниатюру делаем стандартно
	thumb_generate($url . $fn, $size_image_mini_w, $size_image_mini_h, false, $image_mini_type, true, 'mini', false);
	
	echo ' THUMB... ';
	
	// 100x100 — превью в _mso_i с тем же типом кропа
	thumb_generate($url . $fn, 100, 100, false, $image_mini_type, true, '_mso_i', false);
	// thumb_generate($url . $fn, 100, 100, false, 'resize_full_crop_center', true, '_mso_i', false);
	
	echo ' DONE! <b>' . $fn . '</b>';
}

function _slug($slug)
{
	$repl = array(
	"А"=>"a", "Б"=>"b",  "В"=>"v",  "Г"=>"g",   "Д"=>"d",
	"Е"=>"e", "Ё"=>"jo", "Ж"=>"zh",
	"З"=>"z", "И"=>"i",  "Й"=>"j",  "К"=>"k",   "Л"=>"l",
	"М"=>"m", "Н"=>"n",  "О"=>"o",  "П"=>"p",   "Р"=>"r",
	"С"=>"s", "Т"=>"t",  "У"=>"u",  "Ф"=>"f",   "Х"=>"h",
	"Ц"=>"c", "Ч"=>"ch", "Ш"=>"sh", "Щ"=>"shh", "Ъ"=>"",
	"Ы"=>"y", "Ь"=>"",   "Э"=>"e",  "Ю"=>"ju", "Я"=>"ja",

	"а"=>"a", "б"=>"b",  "в"=>"v",  "г"=>"g",   "д"=>"d",
	"е"=>"e", "ё"=>"jo", "ж"=>"zh",
	"з"=>"z", "и"=>"i",  "й"=>"j",  "к"=>"k",   "л"=>"l",
	"м"=>"m", "н"=>"n",  "о"=>"o",  "п"=>"p",   "р"=>"r",
	"с"=>"s", "т"=>"t",  "у"=>"u",  "ф"=>"f",   "х"=>"h",
	"ц"=>"c", "ч"=>"ch", "ш"=>"sh", "щ"=>"shh", "ъ"=>"",
	"ы"=>"y", "ь"=>"",   "э"=>"e",  "ю"=>"ju",  "я"=>"ja",

	# украина
	"Є" => "ye", "є" => "ye", "І" => "i", "і" => "i",
	"Ї" => "yi", "ї" => "yi", "Ґ" => "g", "ґ" => "g",
	
	# беларусь
	"Ў"=>"u", "ў"=>"u", "'"=>"",
	
	# румынский
	"ă"=>'a', "î"=>'i', "ş"=>'sh', "ţ"=>'ts', "â"=>'a',
	
	"«"=>"", "»"=>"", "—"=>"-", "`"=>"", " "=>"-",
	"["=>"", "]"=>"", "{"=>"", "}"=>"", "<"=>"", ">"=>"",

	"?"=>"", ","=>"", "*"=>"", "%"=>"", "$"=>"",

	"@"=>"", "!"=>"", ";"=>"", ":"=>"", "^"=>"", "\""=>"",
	"&"=>"", "="=>"", "№"=>"", "\\"=>"", "/"=>"", "#"=>"",
	"("=>"", ")"=>"", "~"=>"", "|"=>"", "+"=>"", "”"=>"", "“"=>"",
	"'"=>"",

	"’"=>"",
	"—"=>"-", // mdash (длинное тире)
	"–"=>"-", // ndash (короткое тире)
	"™"=>"tm", // tm (торговая марка)
	"©"=>"c", // (c) (копирайт)
	"®"=>"r", // (R) (зарегистрированная марка)
	"…"=>"", // (многоточие)
	"“"=>"",
	"”"=>"",
	"„"=>"",
	
	" "=>"-",
	);
		
	$slug = strtr(trim($slug), $repl);
	$slug = htmlentities($slug); // если есть что-то из юникода
	$slug = strtr(trim($slug), $repl);
	$slug = strtolower($slug);
	
	return $slug;
}

return;

# end of file