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

$thistimeComplete = date("d.m.Y H:i", time() + ($config['date_adjust'] * 60));
$user_id          = '1';
$subject          = 'Добавлены новости (' . $thistimeComplete . ')';
$from             = 'autoRSS';

$text = '<h2>Обновления от ' . $thistimeComplete . '</h2>';
$text .= '<p><b>Общая информация:</b></p>';
$text .= '<p>Запросов в БД: ' . $info['queries'] . '</p>';
$text .= '<p>Добавлено новостей: ' . $info['addindex'] . '</p>';
$text .= '<p>Пропущено: ' . $info['addbadindex'] . '</p>';
$text .= '<p>Добавлено пользователей: ' . $info['userindex'] . '</p>';
$text .= '<p>Общее время выполнения: ' . $info['microtime'] . '</p>';
$text .= '<ol>';
foreach ($summary as $key => $summ) {
	if (isset($summ['items'])) {
		$text .= '<li>';
		$text .= '<h3>' . $summ['feedName'] . ' <small>[<a href="' . $summ['feedEditLink'] . '" target="_blank" title="Редактировать настройки ленты">редактировать</a>]</small></h3>';
		$text .= '<p>Затраты памяти: ' . $summ['feedMemory'] . '</p>';
		$text .= '<ul>';
		foreach ($summ['items'] as $item) {
			$text .= '<li>';
			if (isset($item['badTitle'])) {
				$text .= $item['badTitle'];
			}
			else {
				$text .= '<a href="' . $item['link'] . '" target="_blank">' . $item['title'] . '</a>';
			}
			$text .= '</li>';
		}
		$text .= '</ul>';
		$text .= '</li>';
	}
}
$text .= '</ol>';

if ($cfg['sendpm']) {
	$is_send_pm = sendPM($user_id, $subject, $text, $from);
}
if ($cfg['sendmail']) {
	include_once ENGINE_DIR . '/classes/mail.class.php';
	$mail    = new dle_mail($config, true);
	$is_send = $mail->send($mail->from, 'Отчёт AutoRss', $text);
}

?>