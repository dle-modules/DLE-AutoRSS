<?php
/*
=============================================================================
AutoRSS для DLE - автоматический парсинг и импорт RSS-лент в DLE.
=============================================================================
Автор:   ПафНутиЙ 
URL:     http://pafnuty.name/
twitter: https://twitter.com/pafnuty_name
google+: http://gplus.to/pafnuty
email:   pafnuty10@gmail.com
=============================================================================
*/
if (!defined('DATALIFEENGINE')) die("Go fuck yourself!");

$elementId = (!empty($_REQUEST['id'])) ? (int)$_REQUEST['id'] : 0;

if ($elementId > 0) {
	if ($_REQUEST['save'] == 'yes') {
		include(AUTORSS_DIR . '/admin/save.php');
	}
	else {
		include(AUTORSS_DIR . '/admin/form.php');
	}
}
else {
	$output = <<<HTML
	<div class="decr">
		<h2 class="red">Не выбрана лента</h2>
		<a class="btn active" href="{$config['admin_path']}?mod={$cfg['moduleName']}">Вернуться назад</a>
	</div>
HTML;
}


?>