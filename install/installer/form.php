<?php if (!defined('INSTALLER')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

if (!$showForm) return;

?>
<h2 class="mar40-t t-gray700 mar5-b"><?= t('f1') ?></h2>
<p class="mar0-t italic t-gray500"><?= t('f2') ?></p>

<form class="mso-form t-gray600" method="post">

	<label class="flex flex-vcenter mar20-b">
		<div class="flex-grow5 w400px-max"><input type="text" class="w100" name="username" value="<?= htmlspecialchars($PV['username'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="<?= t('f3') ?>" required></div>
		<div class="flex-grow1 mar10-l"><?= t('f4') ?></div>
	</label>

	<label class="flex flex-vcenter mar20-b">
		<div class="flex-grow5 w400px-max"><input type="text" class="w100" name="password" value="<?= htmlspecialchars($PV['password'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="<?= t('f5') ?>" required></div>
		<div class="flex-grow1 mar10-l"><?= t('f6') ?></div>
	</label>

	<label class="flex flex-vcenter mar20-b">
		<div class="flex-grow5 w400px-max"><input type="email" class="w100" name="email" value="<?= htmlspecialchars($PV['email'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="<?= t('f7') ?>" required></div>
		<div class="flex-grow1 mar10-l"><?= t('f8') ?></div>
	</label>

	<label class="flex flex-vcenter">
		<div class="flex-grow5 w400px-max"><input type="text" class="w100" name="site_name" value="<?= htmlspecialchars($PV['site_name'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="<?= t('f9') ?>" required></div>
		<div class="flex-grow1 mar10-l"><?= t('f10') ?></div>
	</label>

	<h5 class="bold mar30-t mar5-b"><?= t('f11') ?></h5>
	<p class="mar0-t italic t-gray500"><?= t('f12') ?></p>

	<label class="flex flex-vcenter mar20-b">
		<div class="flex-grow5 w400px-max"><input type="text" class="w100" name="db_hostname" value="<?=htmlspecialchars($PV['db_hostname'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="<?= t('f13') ?>" required></div>
		<div class="flex-grow1 mar10-l"><?= t('f14') ?></div>
	</label>

	<label class="flex flex-vcenter mar20-b">
		<div class="flex-grow5 w400px-max"><input type="text" class="w100" name="db_username" value="<?= htmlspecialchars($PV['db_username'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="<?= t('f15') ?>" required></div>
		<div class="flex-grow1 mar10-l"><?= t('f16') ?></div>
	</label>

	<label class="flex flex-vcenter mar20-b">
		<div class="flex-grow5 w400px-max"><input type="text" class="w100" name="db_password" value="<?= htmlspecialchars($PV['db_password'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="<?= t('f17') ?>" required></div>
		<div class="flex-grow1 mar10-l"><?= t('f18') ?></div>
	</label>

	<label class="flex flex-vcenter mar20-b">
		<div class="flex-grow5 w400px-max"><input type="text" class="w100" name="db_database" value="<?= htmlspecialchars($PV['db_database'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="<?= t('f19') ?>" required></div>
		<div class="flex-grow1 mar10-l"><?= t('f20') ?></div>
	</label>

	<label class="flex flex-vcenter">
		<div class="flex-grow5 w400px-max"><input type="text" class="w100" name="db_dbprefix" value="<?=htmlspecialchars($PV['db_dbprefix'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="<?= t('f21') ?>" required></div>
		<div class="flex-grow1 mar10-l"><?= t('f22') ?></div>
	</label>

	<p class="mar30-t"><button type="submit" class="button"><?= t('f23') ?></button></p>
</form>