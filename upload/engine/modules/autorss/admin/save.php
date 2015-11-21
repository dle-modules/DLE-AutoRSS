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

$elementId          = (int)$_REQUEST['id'];
$name               = $db->safesql(trim($_REQUEST['name']));
$url                = $db->safesql(trim($_REQUEST['url']));
$tags               = $db->safesql(trim($_REQUEST['tags']));
$cookie             = $db->safesql(trim($_REQUEST['cookie']));
$category           = $db->safesql(trim($_REQUEST['category']));
$noimage            = $db->safesql(trim($_REQUEST['noimage']));
$authorLogin        = $db->safesql(trim($_REQUEST['authorLogin']));
$sourseTextName     = $db->safesql(trim($_REQUEST['sourseTextName']));
$sourceTarget       = $db->safesql(trim($_REQUEST['sourceTarget']));
$resizeType         = $db->safesql(trim($_REQUEST['resizeType']));
$imgSize            = $db->safesql(trim($_REQUEST['imgSize']));
$fullStoryTags      = $db->safesql(trim($_REQUEST['fullStoryTags']));
$allowRssTags       = (int)$_REQUEST['allowRssTags'];
$offline            = (int)$_REQUEST['offline'];
$allow_main         = (int)$_REQUEST['allow_main'];
$allow_rating       = (int)$_REQUEST['allow_rating'];
$allow_comm         = (int)$_REQUEST['allow_comm'];
$allow_br           = (int)$_REQUEST['allow_br'];
$date               = (int)$_REQUEST['date'];
$max_news           = (int)$_REQUEST['max_news'];
$checkDouble        = (int)$_REQUEST['checkDouble'];
$textLimit          = (int)$_REQUEST['textLimit'];
$chpuCut            = (int)$_REQUEST['chpuCut'];
$allowNewUsers      = (int)$_REQUEST['allowNewUsers'];
$newUserGroup       = (int)$_REQUEST['newUserGroup'];
$pseudoLinks        = (int)$_REQUEST['pseudoLinks'];
$dasableImages      = (int)$_REQUEST['dasableImages'];
$grabImages         = (int)$_REQUEST['grabImages'];
$saveOriginalImages = (int)$_REQUEST['saveOriginalImages'];
$fullStoryType      = (int)$_REQUEST['fullStoryType'];

if ($_REQUEST['action'] == 'add') {
	if ($name == '') {
		$output = <<<HTML
			<div class="decr">
				<h2 class="red">Заголовок не может быть пустым.</h2>
				<a class="btn" href="{$config['admin_path']}?mod={$cfg['moduleName']}&action=add">попробовать снова</a>
			</div>
HTML;
	}
	elseif ($url == '') {
		$output = <<<HTML
			<div class="decr">
				<h2 class="red">Адрес канала где?</h2>
				<a class="btn" href="{$config['admin_path']}?mod={$cfg['moduleName']}&action=add">попробовать снова</a>
			</div>
HTML;
	}
	else {

		$addQuery = $db->query("INSERT INTO " . PREFIX . "_auto_rss (name, url, tags, cookie, category, noimage, authorLogin, sourseTextName, sourceTarget, resizeType, imgSize, allowRssTags, offline, allow_main, allow_rating, allow_comm, allow_br, date, max_news, checkDouble, textLimit, fullStoryType, fullStoryTags, chpuCut, allowNewUsers, newUserGroup, pseudoLinks, dasableImages, grabImages, saveOriginalImages) values ('$name', '$url', '$tags', '$cookie', '$category', '$noimage', '$authorLogin', '$sourseTextName', '$sourceTarget', '$resizeType', '$imgSize', '$allowRssTags', '$offline', '$allow_main', '$allow_rating', '$allow_comm', '$allow_br', '$date', '$max_news', '$checkDouble', '$textLimit', '$fullStoryType', '$fullStoryTags', '$chpuCut', '$allowNewUsers', '$newUserGroup', '$pseudoLinks', '$dasableImages', '$grabImages', '$saveOriginalImages')");
		if ($addQuery == 1) {
			$output = <<<HTML
				<div class="decr">
					<h2 class="green">Новый канал успешно добавлен!</h2>
					<a class="btn" href="{$config['admin_path']}?mod={$cfg['moduleName']}">Вернуться к списку каналов</a>
				</div>
HTML;
		}
		else {
			$output = <<<HTML
				<div class="decr">
					<h2 class="red">Ошибка при добавлении. Канал не добавлен.</h2>
					<a class="btn" href="{$config['admin_path']}?mod={$cfg['moduleName']}&action=add">попробовать снова</a>
				</div>
HTML;
		}
	}
}
elseif ($elementId != 0) {

	$query         = "SELECT id FROM " . PREFIX . "_auto_rss WHERE id=" . $elementId;
	$elementToSave = $db->super_query($query);

	if (($elementId <= 0) || ($elementToSave['id'] != $elementId)) {
		$output = <<<HTML
			<div class="decr">
				<h2 class="red">Не выбрана лента</h2>
				<a class="btn active" href="{$config['admin_path']}?mod={$cfg['moduleName']}">Вернуться назад</a>
			</div>
HTML;
	}
	else {

		$save_query = $db->query("UPDATE " . PREFIX . "_auto_rss set 
			name               = '$name',
			url                = '$url',
			tags               = '$tags',
			cookie             = '$cookie',
			category           = '$category',
			noimage            = '$noimage',
			authorLogin        = '$authorLogin',
			sourseTextName     = '$sourseTextName',
			sourceTarget       = '$sourceTarget',
			resizeType         = '$resizeType',
			imgSize            = '$imgSize',
			allowRssTags       = '$allowRssTags',
			offline            = '$offline',
			allow_main         = '$allow_main',
			allow_rating       = '$allow_rating',
			allow_comm         = '$allow_comm',
			allow_br           = '$allow_br',
			date               = '$date',
			max_news           = '$max_news',
			checkDouble        = '$checkDouble',
			textLimit          = '$textLimit',
			fullStoryType      = '$fullStoryType',
			fullStoryTags      = '$fullStoryTags',
			chpuCut            = '$chpuCut',
			allowNewUsers      = '$allowNewUsers',
			newUserGroup       = '$newUserGroup',
			pseudoLinks        = '$pseudoLinks',
			dasableImages      = '$dasableImages',
			grabImages         = '$grabImages', 
			saveOriginalImages = '$saveOriginalImages'  
			WHERE id = " . $elementToSave['id']);

		if ($save_query == 1) {
			$output = <<<HTML
				<div class="decr">
					<h2 class="green">Настройки канала успешно сохранены</h2>
					<a class="btn" href="{$config['admin_path']}?mod={$cfg['moduleName']}">Вернуться к списку каналов</a>
				</div>
HTML;

		}
		else {
			$output = <<<HTML
				<div class="decr">
					<h2 class="red">Ошибка при сохранении. Данные не сохранены.</h2>
					<a class="btn active" href="{$config['admin_path']}?mod={$cfg['moduleName']}&action=edit&id={$elementId}">Вернуться назад</a>
				</div>
HTML;
		}


	}

}

?>
