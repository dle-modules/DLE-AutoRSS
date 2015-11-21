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

$start_from    = (intval($_REQUEST['start_from']) > 0) ? intval($_REQUEST['start_from']) : 0;
$i             = $start_from;
$item_per_page = 20;

$query           = "SELECT * FROM " . PREFIX . "_auto_rss LIMIT " . $start_from . ", " . $item_per_page;
$query_count     = "SELECT COUNT(*) as count FROM " . PREFIX . "_auto_rss";
$result_count    = $db->super_query($query_count);
$all_count_items = $result_count['count'];
$lents           = $db->super_query($query, true);

if ($all_count_items > 0) {
	$output = <<<HTML
	<form id="show-list" method="POST" action="{$_SERVER["PHP_SELF"]}?mod={$cfg['moduleName']}">
		<input type="hidden" name="start_from" id="start_from" value="{$start_from}">
		<input type="hidden" name="user_hash" value="{$dle_login_hash}"> 
		<table>
			<tr>
				<th>id</th>
				<th>Название</th>
				<th>Категория</th>
				<th>Активность</th>
				<th class="ta-right">Действия <input type="checkbox" id="el-0" class="main-checkbox checkbox" data-checkboxes=".children-checkbox" title="Отметить всё"><label for="el-0" title="Отметить всё"><span></span></label></th>
			</tr>
HTML;
	foreach ($lents as $item) {
		$i++;
		$itemActive = ($item['offline'] == 1) ? '<span class="red">Нет</span>' : '<b>Да</b>';
		$itemCat    = ($item['category'] > 0) ? $item['category'] : ' ';

		$output .= '<tr>
		<td>' . $item['id'] . '</td>
		<td>' . $item['name'] . '</td>
		<td>' . $itemCat . '</td>
		<td>' . $itemActive . '</td>
		<td class="ta-right">
			<a href="/autorss.php?pass=' . $cfg['pass'] . '&id=' . $item['id'] . '&test=1&fulldebug=1" class="btn btn-small" title="Тестовый запуск парсера с полным дебагом" target="_blank">тест</a>
			<a href="' . $_SERVER["PHP_SELF"] . '?mod=' . $cfg['moduleName'] . '&action=edit&id=' . $item['id'] . '" class="btn btn-small">правка</a>
			<a href="' . $_SERVER["PHP_SELF"] . '?mod=' . $cfg['moduleName'] . '&action=delete&id=' . $item['id'] . '" class="btn active btn-small">&times;</a>
			<input type="checkbox" value="' . $item['id'] . '" name="items[]" id="el-' . $item['id'] . '" class="checkbox children-checkbox"><label for="el-' . $item['id'] . '"><span></span></label>
		</td>
	</tr>';
	}
	$output .= '	</table></form>';
}
else {
	$output .= '<h2 id="result-header" class="red">RSS-ленты не добавлены</h2><hr />';
}


// pagination	

$npp_nav = "<div class=\"news_navigation fleft\" >";
if ($start_from > 0) {
	$previous = $start_from - $item_per_page;
	$npp_nav .= "<a class='btn btn-small' onClick=\"javascript:list_submit($previous); return(false)\" href=#> &lt;&lt; </a>&nbsp;";
}
if ($all_count_items > $item_per_page) {
	$enpages_count      = @ceil($all_count_items / $item_per_page);
	$enpages_start_from = 0;
	$enpages            = "";
	if ($enpages_count <= 10) {
		for ($j = 1; $j <= $enpages_count; $j++) {
			if ($enpages_start_from != $start_from) {
				$enpages .= "<a class='btn btn-small' onclick=\"javascript:list_submit($enpages_start_from); return(false);\" href=\"#\">$j</a> ";
			}
			else {
				$enpages .= "<span class='btn btn-small active'>$j</span> ";
			}
			$enpages_start_from += $item_per_page;
		}
		$npp_nav .= $enpages;
	}
	else {
		$start = 1;
		$end   = 10;
		if ($start_from > 0) {
			if (($start_from / $item_per_page) > 4) {
				$start = @ceil($start_from / $item_per_page) - 3;
				$end   = $start + 9;

				if ($end > $enpages_count) {
					$start = $enpages_count - 10;
					$end   = $enpages_count - 1;
				}
				$enpages_start_from = ($start - 1) * $item_per_page;
			}
		}

		if ($start > 2) {
			$enpages .= "<a class='btn btn-small' onclick=\"javascript:list_submit(0); return(false);\" href=\"#\">1</a> ... ";
		}

		for ($j = $start; $j <= $end; $j++) {
			if ($enpages_start_from != $start_from) {
				$enpages .= "<a class='btn btn-small' onclick=\"javascript:list_submit($enpages_start_from); return(false);\" href=\"#\">$j</a> ";
			}
			else {
				$enpages .= "<span class='btn btn-small active'>$j</span> ";
			}
			$enpages_start_from += $item_per_page;
		}

		$enpages_start_from = ($enpages_count - 1) * $item_per_page;
		$enpages .= "... <a class='btn btn-small' onclick=\"javascript:list_submit($enpages_start_from); return(false);\" href=\"#\">$enpages_count</a> ";

		$npp_nav .= $enpages;
	}
}


if ($all_count_items > $i) {
	$how_next = $all_count_items - $i;
	if ($how_next > $item_per_page) {
		$how_next = $item_per_page;
	}
	$npp_nav .= "<a class='btn btn-small' onclick=\"javascript:list_submit($i); return(false)\" href=#> &gt;&gt; </a> ";
}
$npp_nav .= "</div>";
$output .= $npp_nav;

$output .= '<div class="fright">
	<select class="styler" name="mass_action" id="mass_action">
		<option>-- выберите действие --</option>
		<option value="test">Тестировать выбранные</option>
		<option value="delete">Удалить выбранные</option>
	</select>
</div>';
$output .= '<div class="clr"></div>';
// pagination
?>