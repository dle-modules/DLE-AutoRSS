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

$start_from      = (intval($_REQUEST['start_from']) > 0) ? intval($_REQUEST['start_from']) : 0;
$i               = $start_from;
$item_per_page   = 20;
$query           = "SELECT * FROM " . PREFIX . "_rss LIMIT " . $start_from . ", " . $item_per_page;
$query_count     = "SELECT COUNT(*) as count FROM " . PREFIX . "_rss";
$result_count    = $db->super_query($query_count);
$all_count_items = $result_count['count'];
$lents           = $db->super_query($query, true);

if (is_array($_REQUEST['items']) || $_REQUEST['import_all']) {
	if ($_REQUEST['import_all']) {
		$items = $db->super_query("SELECT id FROM " . PREFIX . "_rss", true);
	} else {
		$items  = $_REQUEST['items'];
	}
	$output = '<div class="decr"><h2>Импорт RSS-каналов:</h2>';
	foreach ($items as $item) {
		$importQuery    = 0;
		$id             = ($_REQUEST['import_all']) ? (int)$item['id'] : (int)$item;
		$_s             = $db->super_query("SELECT * FROM " . PREFIX . "_rss WHERE id=" . $id);
		$_cn            = explode(', ', $_s['description']);
		$channelName    = trim($_cn[0]);
		$_t             = array_slice($_cn, 1);
		$_tags          = implode(', ', $_t);
		$offline        = $cfg['channel']['offline'];
		$url            = $_s['url'];
		$name           = $channelName;
		$tags           = $_tags;
		$category       = $_s['category'];
		$cookie         = $_s['cookie'];
		$allow_main     = ($_s['allow_main']) ? $_s['allow_main'] : $cfg['channel']['allow_main'];
		$allow_rating   = ($_s['allow_rating']) ? $_s['allow_rating'] : $cfg['channel']['allow_rating'];
		$allow_comm     = ($_s['allow_comm']) ? $_s['allow_comm'] : $cfg['channel']['allow_comm'];
		$date           = ($_s['date']) ? $_s['date'] : $cfg['channel']['date'];
		$max_news       = ($_s['max_news']) ? $_s['max_news'] : $cfg['channel']['max_news'];
		$lastdate       = ($_s['lastdate']) ? $_s['lastdate'] : $cfg['channel']['lastdate'];
		$allowRssTags   = $cfg['channel']['allowRssTags'];
		$noimage        = $cfg['channel']['noimage'];
		$checkDouble    = $cfg['channel']['checkDouble'];
		$textLimit      = $cfg['channel']['textLimit'];
		$chpuCut        = $cfg['channel']['chpuCut'];
		$authorLogin    = $cfg['channel']['authorLogin'];
		$allowNewUsers  = $cfg['channel']['allowNewUsers'];
		$newUserGroup   = $cfg['channel']['newUserGroup'];
		$sourseTextName = $cfg['channel']['sourseTextName'];
		$pseudoLinks    = $cfg['channel']['pseudoLinks'];
		$sourceTarget   = $cfg['channel']['sourceTarget'];
		$dasableImages  = $cfg['channel']['dasableImages'];
		$grabImages     = $cfg['channel']['grabImages'];
		$imgSize        = $cfg['channel']['imgSize'];
		$resizeType     = $cfg['channel']['resizeType'];

		$importQuery = $db->query("INSERT INTO " . PREFIX . "_auto_rss (offline, url, name, tags, category, cookie, allow_main, allow_rating, allow_comm, date, max_news, lastdate, allowRssTags, noimage, checkDouble, textLimit, chpuCut, authorLogin, allowNewUsers, newUserGroup, sourseTextName, pseudoLinks, sourceTarget, dasableImages, grabImages, imgSize, resizeType) values ('$offline', '$url', '$name', '$tags', '$category', '$cookie', '$allow_main', '$allow_rating', '$allow_comm', '$date', '$max_news', '$lastdate', '$allowRssTags', '$noimage', '$checkDouble', '$textLimit', '$chpuCut', '$authorLogin', '$allowNewUsers', '$newUserGroup', '$sourseTextName', '$pseudoLinks', '$sourceTarget', '$dasableImages', '$grabImages', '$imgSize', '$resizeType' )");

		if ($_REQUEST['del-imports'] == 'yes' && $importQuery == 1) {
			$delSource = $db->query("DELETE FROM " . PREFIX . "_rss WHERE id = '$id'");
			$addLog    = $db->query("INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('" . $db->safesql($member_id['name']) . "', '{$_TIME}', '{$_IP}', '50', '{$id}')");
		}
		$done = ($importQuery == 1) ? '<b class="green">ок</b>' : '<b class="red">фэйл</b>';
		$del  = ($delSource == 1) ? '<b class="gray"> (канал удалён)</b>' : '';

		$output .= '<p>' . $name . ' - ' . $done . $del . '</p>';
	}

	$output .= '<a class="btn" href="' . $config['admin_path'] . '?mod=' . $cfg['moduleName'] . '">Перейти к списку каналов</a></div>';

}
else {

	if ($all_count_items > 0) {

		$output = <<<HTML
		<form id="show-list" method="POST" action="{$_SERVER["PHP_SELF"]}?mod={$cfg['moduleName']}">
			<input type="hidden" name="start_from" id="start_from" value="{$start_from}">
			<input type="hidden" name="user_hash" value="{$dle_login_hash}"> 
			<input type="hidden" name="action" value="import">
			<input type="hidden" name="import" value="yes">

			<table>
				<tr>
					<th>id</th>
					<th>URL</th>
					<th>Название</th>
					<th class="ta-right"><input type="checkbox" id="el-0" class="main-checkbox checkbox" data-checkboxes=".children-checkbox" title="Отметить всё"><label for="el-0" title="Отметить всё"><span></span></label></th>
				</tr>
HTML;
		foreach ($lents as $item) {
			$i++;
			$output .= '<tr>
			<td>' . $item['id'] . '</td>
			<td>' . $item['url'] . '</td>
			<td>' . $item['description'] . '</td>
			<td class="ta-right">
				<input type="checkbox" value="' . $item['id'] . '" name="items[]" id="el-' . $item['id'] . '" class="checkbox children-checkbox"><label for="el-' . $item['id'] . '"><span></span></label>
			</td>
		</tr>';
		}
		$output .= '	</table>';
	}
	else {
		$output .= '<h2 id="result-header" class="red">Нет RSS-лент для импорта.</h2><hr />';
	}


	// pagination	

	$npp_nav = "<div class=\"news_navigation\" >";
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

	// pagination

	if ($all_count_items > 0) {
		$output .= '<div class="ta-right"><input type="checkbox" value="yes" name="del-imports" id="del-imports" class="checkbox children-checkbox"><label for="del-imports"><span></span> удалять импортированные каналы</label> &nbsp;&nbsp; <button class="btn active" type="submit" name="import_all" value="Y">Импортировать всё</button> &nbsp;&nbsp;<button class="btn" type="submit">Импортировать выбранные каналы</button></div></form>';
	}
}
?>