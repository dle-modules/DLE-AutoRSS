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

$query = "SELECT id FROM " . PREFIX . "_auto_rss WHERE id=" . $elementId;
$elementToDel = $db->super_query($query);


if ($elementToDel['id'] != $elementId) {
	$output = <<<HTML
		<div class="decr">
			<h2 class="red">Не выбрана лента</h2>
			<a class="btn active" href="{$config['admin_path']}?mod={$cfg['moduleName']}">Вернуться назад</a>
		</div>
HTML;

}
else {
	if ($_REQUEST['delete'] == 'yes') {
		if (isset($_REQUEST['items']) && $_REQUEST['items'] > 0) {
			$_itms = explode(',', $_REQUEST['items']);
			foreach ($_itms as $id) {
				$db->query("DELETE FROM " . PREFIX . "_auto_rss WHERE id = '" . $id . "'");
			}

		} else {
			$db->query("DELETE FROM " . PREFIX . "_auto_rss WHERE id = '" . $elementToDel['id'] . "'");
		}

		$output = <<<HTML
			<div class="decr">
				<h2 class="green">Успешно удалено!</h2>
				<a class="btn" href="{$config['admin_path']}?mod={$cfg['moduleName']}">Вернуться к списку каналов</a>
			</div>
HTML;
	}
	else {
		$_delitms = implode(',', $_REQUEST['items']); 
		$output = <<<HTML
			<div class="decr">
				<h2 class="red">Вы уверены, что хотите удалить канал(ы)?</h2>
				<form class="form-inline" method="POST" action="{$_SERVER["PHP_SELF"]}?mod={$cfg['moduleName']}">
					<input type="hidden" name="action" value="delete">
					<input type="hidden" name="id" value="{$elementToDel['id']}">
					<input type="hidden" name="delete" value="yes">
					<input type="hidden" name="items" value="{$_delitms}">
					<button class="btn" type="submit">Да</button>
				</form>
				<a class="btn active" href="{$config['admin_path']}?mod={$cfg['moduleName']}">Нет, вернуться к списку каналов</a>
			</div>
HTML;
	}

}

?>
