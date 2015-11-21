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


if (!defined('DATALIFEENGINE') OR !defined('LOGGED_IN')) {
	die("Hacking attempt!");
}
if ($member_id['user_group'] != '1') {
	msg("error", $lang['index_denied'], $lang['index_denied']);
}

// Первым делом подключаем DLE_API как это ни странно, но в данном случаи это упрощает жизнь разработчика.
include(ENGINE_DIR . '/api/api.class.php');
define('AUTORSS_DIR', ENGINE_DIR . '/modules/autorss/');

/**
 * Конфиг модуля
 */

include_once(AUTORSS_DIR . '/auto_rss_config.php');

/**
 * Основная функция модуля
 * @return string - результат работы модуля.
 */
function autoRSS() {
	global $config, $dle_api, $cfg, $db, $user_group, $member_id, $_TIME, $_IP;

	$output = '';
	if (empty($_REQUEST['action'])) {
		include(AUTORSS_DIR . '/admin/main.php');
	}

	if (!empty($_REQUEST['action'])) {
		if ($_REQUEST['action'] == 'edit') {
			include(AUTORSS_DIR . '/admin/edit.php');
		}
		if ($_REQUEST['action'] == 'delete') {
			include(AUTORSS_DIR . '/admin/delete.php');
		}
		if ($_REQUEST['action'] == 'add') {
			include(AUTORSS_DIR . '/admin/add.php');
		}
		if ($_REQUEST['action'] == 'import') {
			include(AUTORSS_DIR . '/admin/import.php');
		}
		if ($_REQUEST['action'] == 'cron') {
			include(AUTORSS_DIR . '/admin/cron.php');
		}

	}


	return $output;

}


/**
 * Подсчитываем общее кол-во RSS-лент в БД
 *
 * @param  boolean $nomOnly Возвратить только кол-во.
 *
 * @return string - блок с кол-вом RSS-лент
 */
function allChannelCount($nomOnly = false) {
	global $config, $db, $cfg;
	$_aUC        = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_auto_rss");
	$_aUCActive  = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_auto_rss WHERE offline !=1");
	$uCountNum   = $_aUC['count'];
	$activeCount = $_aUCActive['count'];
	$uCount      = ($nomOnly) ? $uCountNum : '<div class="fright">' . wordSpan($activeCount, 'Активн|а|о|о') . ' <b>' . $activeCount . '</b> ' . wordSpan($activeCount, 'лен|та|ты|т') . ' из <b>' . $uCountNum . '</b>.</div>';

	return $uCount;
}

/**
 * Функция для установки правильного окончания слов
 * @param int    $n     - число, для которого будет расчитано окончание
 * @param string $words - варианты окончаний для (1 комментарий, 2 комментария, 100 комментариев)
 *
 * @return string - слово с правильным окончанием
 */
function wordSpan($n = 0, $words) {
	$words = explode('|', $words);
	$n     = intval($n);

	return $n % 10 == 1 && $n % 100 != 11 ? $words[0] . $words[1] : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? $words[0] . $words[2] : $words[0] . $words[3]);
}


?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="<?= $config['charset'] ?>">
	<title><?= $cfg['moduleTitle'] ?> - Управление модулем</title>
	<meta name="viewport" content="width=device-width">
	<link href="http://fonts.googleapis.com/css?family=Ubuntu+Condensed&subset=latin,cyrillic" rel="stylesheet">
	<link rel="stylesheet" href="/engine/skins/autorss/main.css">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="http://cdnjs.cloudflare.com/ajax/libs/autosize.js/1.18.1/jquery.autosize.min.js"></script>
	<script src="/engine/skins/autorss/jquery.formstyler.min.js"></script>
	<script src="/engine/skins/autorss/main.js"></script>
</head>
<body>
<header>
	<div class="clearfix">
		<div class="fleft">
			<a href="<?= $PHP_SELF ?>?mod=main" class="btn btn-small"><?= $lang['skin_main'] ?></a>
			<a class="btn btn-small" href="<?= $PHP_SELF ?>?mod=options&amp;action=options"
			   title="Список всех разделов">Список всех разделов</a>
			<a href="<?= $config['http_home_url'] ?>" target="_blank"
			   class="btn btn-small"><?= $lang['skin_view'] ?></a>
		</div>
		<div class="fright">
			<?= $lang['skin_name'] . ' ' . $member_id['name'] . ' <small>(' . $user_group[$member_id['user_group']]['group_name'] . ')</small> ' ?>
			<a href="<?= $PHP_SELF ?>?action=logout" class="btn btn-small"><?= $lang['skin_logout'] ?></a>
		</div>
	</div>
	<hr>
	<h1 class="ta-center"><big class="red"><?= $cfg['moduleTitle'] ?></big> v.<?= $cfg['moduleVersion'] ?>
		от <?= $cfg['moduleDate'] ?></h1>
</header>
<section>

	<h2 class="gray ta-center"><?= $cfg['moduleDescr'] ?></h2>
	<hr>
	<div class="mb20 clearfix">
		<div class="fleft">
			<a href="<?= $config['admin_path'] . '?mod=' . $cfg['moduleName'] ?>" class="btn btn-small">Список
				каналов</a>
			<a href="<?= $config['admin_path'] . '?mod=' . $cfg['moduleName'] ?>&action=add" class="btn btn-small">Добавить
				новый канал</a>
			<a href="<?= $config['admin_path'] . '?mod=' . $cfg['moduleName'] . '&action=import' ?>"
			   class="btn btn-small ttp" title="Импортировать стандартные каналы DLE">Импорт</a>
			<a href="<?= $config['admin_path'] . '?mod=' . $cfg['moduleName'] . '&action=cron' ?>"
			   class="btn btn-small ttp" title="Примеры команд и параметров для вставки в планировщик">Доки</a>

		</div>
		<?php
		$allChannelCount = allChannelCount();
		echo $allChannelCount;
		?>
	</div>
	<?php
	if ($cfg['pass'] == '123') {
		echo '<div class="descr"><div class="alert clearfix">Смените пароль доступа к модулю! Сделать это можно в файле engine/modules/auto_rss_config.php</div></div>';
	}
	?>

	<?php
	$output = autoRSS();
	echo $output;
	?>
	<hr>
	<div>
		Информация об авторе: <br>
		<a href="http://pafnuty.name/" target="_blank" title="Сайт автора">ПафНутиЙ</a> <br>
		<a href="https://twitter.com/pafnuty_name" target="_blank" title="Twitter">@pafnuty_name</a> <br>
		<a href="http://gplus.to/pafnuty" target="_blank" title="google+">+Павел</a> <br>
		<a href="mailto:pafnuty10@gmail.com" title="email автора">pafnuty10@gmail.com</a>
	</div>
</section>
</body>
</html>