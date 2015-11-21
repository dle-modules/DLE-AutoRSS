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

$debug = '<div class="descr">';
$debug .= '<div><b>Общая информация:</b></div>';
$debug .= '<div>Запросов в БД: ' . $info['queries'] . '</div>';
$debug .= '<div>Добавлено новостей: ' . $info['addindex'] . '</div>';
$debug .= '<div>Пропущено: ' . $info['addbadindex'] . '</div>';
$debug .= '<div>Добавлено пользователей: ' . $info['userindex'] . '</div>';
$debug .= '<div>Общее время выполнения: ' . $info['microtime'] . '</div>';
$debug .= '</div>';
$debug .= '<ol>';

foreach ($summary as $key => $summ) {
	if (isset($summ['items'])) {
		$debug .= '<li>';
		$debug .= '<h2>' . $summ['feedName'] . ' <small>[<a href="' . $summ['feedEditLink'] . '" target="_blank" title="Редактировать настройки ленты">редактировать</a>]</small></h2>';
		$debug .= '<p>Затраты памяти: <span class="red">' . $summ['feedMemory'] . '</span> <span class="btn btn-small expand-channel" data-expand="свернуть" data-collapse="развернуть">свернуть</span></p>';
		$debug .= '<ul class="unstyled">';
		foreach ($summ['items'] as $item) {
			$debug .= '<li><h3>';

			if (isset($item['badTitle'])) {
				$debug .= $item['badTitle'];
			}
			else {
				$debug .= '<a href="' . $item['link'] . '" target="_blank">' . $item['title'] . '</a>';
			}
			$debug .= '</h3>';
			// $debug .= print_r($item['debug']);

			$debug .= '<div class="debug-item">';
			$debug .= '<p>Автор: ' . $item['debug']['autor'] . '</p>';
			$debug .= '<p>Дата : ' . $item['debug']['date'] . '</p>';
			$debug .= '<p>Краткая новость: <span class="btn btn-small show-item">показать визуально</span></p>';
			$debug .= '<div class="show-item-block">';
			$debug .= '<textarea readonly>' .htmlspecialchars($item['debug']['short_story']) . '</textarea>';
			$debug .= '<div class="hide">' . $item['debug']['short_story'] . '</div>';
			$debug .= '</div>';
			$debug .= '<p>Полная новость: <span class="btn btn-small show-item">показать визуально</span></p>';
			$debug .= '<div class="show-item-block">';
			$debug .= '<textarea readonly>' .htmlspecialchars($item['debug']['full_story']) . '</textarea>';
			$debug .= '<div class="hide">' . $item['debug']['full_story'] . '</div>';
			$debug .= '</div>';
			
			$debug .= '</div><hr />';

			$debug .= '</li>';
		}
		$debug .= '</ul>';
		$debug .= '</li>';
	}
}
$debug .= '</ol>';

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="<?= $config['charset'] ?>">
	<title><?= $cfg['moduleTitle'] ?> - Отладка</title>
	<meta name="viewport" content="width=device-width">
	<link href="http://fonts.googleapis.com/css?family=Ubuntu+Condensed&subset=latin,cyrillic" rel="stylesheet">
	<link rel="stylesheet" href="/engine/modules/autorss/css/main.css">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="http://cdnjs.cloudflare.com/ajax/libs/autosize.js/1.18.1/jquery.autosize.min.js"></script>
	<script src="/engine/modules/autorss/js/jquery.formstyler.min.js"></script>
	<script src="/engine/modules/autorss/js/main.js"></script>
</head>
<body>

<section>
<h1 class="red ta-center">Отладка AutoRSS</h1>
<?=$debug;?>
</section>
</body>
</html>