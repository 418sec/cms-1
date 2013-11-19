<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

 /*
* три колонки/рубрики

[unit]
file = 3col-cats-list.php
cats = 1,2,3
limit = 5
[/unit]

*/

# используем кэширование
$home_cache_time = (int) mso_get_option('home_cache_time', 'templates', 0);
$home_cache_key = getinfo('template') . '-' .  __FILE__ . '-' . mso_current_paged();

if ($home_cache_time > 0 and $k = mso_get_cache($home_cache_key) ) echo $k; // да есть в кэше
else
{
	ob_start();
	
	// pr($UNIT); // все данные юнита
	
	// дефолтные рубрики (ID)
	$cat1 = 1;
	$cat2 = 2;
	$cat3 = 3;
	
	if (isset($UNIT['cats']))
	{
		$cats = mso_explode($UNIT['cats']);
		if(isset($cats[0])) $cat1 = $cats[0];
		if(isset($cats[1])) $cat2 = $cats[1];
		if(isset($cats[2])) $cat3 = $cats[2];
	}
	
	// кол-во записей в одной колонке
	$limit = isset($UNIT['limit']) ? (int) $UNIT['limit'] : 3;
	
	
	
	echo '<div class="onerow clearfix">';
	
		// первая колонка
		
		// данные рубрики
		$cat = mso_get_cat_from_id($cat1);
		
		$b = new Block_pages( array (
				'limit' => $limit,
				'pagination' => false,
				'cat_id' => $cat1,
			));
		
		if ($b->go)	
		{
			$b->output(	array (
				'block_start' => '<div class="col w1-3"><h2>' . $cat['category_name'] . '</h2><ul>',
				'block_end' => '</ul><p class="all-cat"><a href="' . mso_page_url($cat['category_slug'], 'category') . '">Посмотреть все записи</a></p></div>',
				'thumb' => false,
				'line1' => '[title]',
				'line1_start' => '<li>',
				'line1_end' => '</li>',
				'content' => false,
				'line2' => '',
				'line3' => '',
				'title_start' => '',
				'title_end' => '',
			));
		}
		
		// вторая колонка
		$cat = mso_get_cat_from_id($cat2);
		
		$b = new Block_pages( array (
				'limit' => $limit,
				'pagination' => false,
				'cat_id' => $cat2,
			));
		
		if ($b->go)	
		{
			$b->output(	array (
				'block_start' => '<div class="col w1-3"><h2>' . $cat['category_name'] . '</h2><ul>',
				'block_end' => '</ul><p class="all-cat"><a href="' . getinfo('siteurl') . 'category/' . $cat['category_slug'] . '">Посмотреть все записи</a></p></div>',
				'thumb' => false,
				'line1' => '[title]',
				'line1_start' => '<li>',
				'line1_end' => '</li>',
				'content' => false,
				'line2' => '',
				'line3' => '',
				'title_start' => '',
				'title_end' => '',
			));
		}
		
		// вторая колонка
		$cat = mso_get_cat_from_id($cat3);
		
		$b = new Block_pages( array (
				'limit' => $limit,
				'pagination' => false,
				'cat_id' => $cat3,
			));
		
		if ($b->go)	
		{
			$b->output(	array (
				'block_start' => '<div class="col w1-3"><h2>' . $cat['category_name'] . '</h2><ul>',
				'block_end' => '</ul><p class="all-cat"><a href="' . getinfo('siteurl') . 'category/' . $cat['category_slug'] . '">Посмотреть все записи</a></p></div>',
				'thumb' => false,
				'line1' => '[title]',
				'line1_start' => '<li>',
				'line1_end' => '</li>',
				'content' => false,
				'line2' => '',
				'line3' => '',
				'title_start' => '',
				'title_end' => '',
			));
		}
		
	echo '</div>';
	
	
	mso_add_cache($home_cache_key, ob_get_flush(), $home_cache_time * 60);

}

# end file