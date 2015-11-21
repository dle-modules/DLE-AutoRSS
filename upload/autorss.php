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

/**
 * Подключаем всё, что нужно для работы модуля
 */
@error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
@ini_set('error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE);

define('ROOT_DIR', dirname(__FILE__));
define('ENGINE_DIR', ROOT_DIR . '/engine');
define('AUTORSS_DIR', ENGINE_DIR . '/modules/autorss');

require_once(ENGINE_DIR . '/api/api.class.php');
require_once(ENGINE_DIR . '/modules/functions.php');

/**
 * Подключаем конфиг скрипта
 */
include_once(AUTORSS_DIR . '/auto_rss_config.php');

// setlocale(LC_ALL, 'russian');


if (empty($_REQUEST['pass']) || $_REQUEST['pass'] != $cfg['pass']) {
	die('Что-то не так, скорее всего вы (как всегда) не читали мануал, где написано, что без пароля скрипт не работает.');
}

// Разные переменные  для подсчёта всякой полезной информации
$i = 0;
$qi = 0;
$addindex = 0;
$addbadindex = 0;
$userindex = 0;
$summary = array();
$start = microtime(true);

include_once(AUTORSS_DIR . '/functions.php');

header('Content-type:text/html; charset=' . $config['charset']);

/**
 * Запрашиваем список лент RSS из БД
 */
$listWhere = array();
$ids = ($cfg['id']) ? getDiapazone($cfg['id']) : false;

$listWhere[] = 'offline !=1';
if ($cfg['id']) {
	$listWhere[] = 'id regexp "[[:<:]](' . str_replace(',', '|', $ids) . ')[[:>:]]"';
}

$limit = ($cfg['limit']) ? 'LIMIT ' . str_replace('x', ', ', $cfg['limit']) : '';

$rssWheres = implode(' AND ', $listWhere);


$rssList = $db->super_query("SELECT * FROM " . PREFIX . "_auto_rss WHERE " . $rssWheres . $limit, true);
$qi++;

require_once(AUTORSS_DIR . '/autoloader.php');


foreach ($rssList as $rssItem) {

	// создаём новый экземпляр класса
	$feed = new SimplePie();

	// Скармливаем ему URL
	$feed->set_feed_url($rssItem['url']);

	// Отключаем кеширование
	$feed->enable_cache(false);

	// Определяем папку, куда будет складываться кеш
	// $feed->set_cache_location(ROOT_DIR . '/uploads/');

	// Отключаем сортировку по дате
	$feed->enable_order_by_date(false);

	// Назначаем нужную кодировку на выходе
	$feed->set_output_encoding($config['charset']);

	// Назначаем useragent (оказывается этого не нужно делать, скрипт некоторые ленты не читает из-за этого)
	// $feed->set_useragent('Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.107 Safari/537.36 OPR/19.0.1326.63');

	// Удаляем html-комментарии
	$feed->strip_comments(true);

	// Удаляем атрибуты у html-тегов
	// $feed->strip_attributes(array_merge($feed->strip_attributes, array('width', 'height', 'border')));
	$feed->strip_attributes($feed->strip_attributes);

	// $feed->encode_instead_of_strip(true);

	// Удаляем не нужные html-теги
	// $feed->strip_htmltags(array_merge($feed->strip_htmltags, array('h1', 'a')));

	// Получаем кодировку фида
	$feed->get_encoding();

	$feed->handle_content_type();
	$feed->init();
	$items = $feed->get_items(0, $rssItem['max_news']);

	$summary[$i]['id']           = $rssItem['id'];
	$summary[$i]['feedName']     = $rssItem['name'];
	$summary[$i]['feedEditLink'] = $config['http_home_url'] . $config['admin_path'] . '?mod=auto_rss&action=edit&id=' . $rssItem['id'];
	$summary[$i]['error']        = $feed->error();

	// Массив с будущей новостью.
	$newsItem = array();
	foreach ($items as $key => $item) {

		// Текущее время с поправкой на зону (из настроек DLE).
		$thistime = date("Y-m-d H:i:s", time() + ($config['date_adjust'] * 60));

		// Определяем последнюю дату доступа к каналу и откидываем те новости, которые уже были обработаны ранее
		$itemDate = (strtotime($item->get_date()) + ($config['date_adjust'] * 60));
		if ($itemDate <= $rssItem['lastdate']) {
			continue;
		}


		// Автор новости
		$_fia = $item->get_author();
		if ($_fia) {
			$feedItemAutor = (entryDecode($_fia->get_name()) != '') ? entryDecode($_fia->get_name()) : $rssItem['authorLogin'];
		}
		else {
			$feedItemAutor = $rssItem['authorLogin'];
		}
		$newsItem['autor'] = ($rssItem['allowNewUsers'] == '1') ? $feedItemAutor : $rssItem['authorLogin'];
		$newsItem['autor'] = str_replace(array("`", "'"), '', $newsItem['autor']);

		// Дата новости
		$newsItem['date'] = ($rssItem['date'] == '1') ? $thistime : $item->get_date('Y-m-d H:i:s');

		// Заголовок новости
		$feedItemTitle = $item->get_title();

		$feedItemTitle     = strip_tags_smart(entryDecode($feedItemTitle));
		$feedItemTitle     = htmlspecialchars($feedItemTitle, ENT_QUOTES, $config['charset']);
		$newsItem['title'] = $feedItemTitle;

		// Краткая новость
		$shortStory = entryDecode($item->get_content());

		$shortStory = safeParse($shortStory);

		// Работаем с картинкой, если это не запрещено
		if ($rssItem['dasableImages'] == 0) {
			// Задаём папку для картинок
			$dir_prefix   = $rssItem['imgSize'] . '/' . date("Y-m") . '/';
			$dir          = ROOT_DIR . '/uploads/rss/' . $dir_prefix;
			$dirThumbs    = ROOT_DIR . '/uploads/rss/' . $dir_prefix . 'thumbs/';
			$noimage      = $rssItem['noimage'];
			$imageUrl     = $noimage;
			$imgForResize = '';
			$imgNameOut   = $noimage;

			// Вылавливаем URL первой картинки
			if (preg_match_all('/<img(?:\\s[^<>]*?)?\\bsrc\\s*=\\s*(?|"([^"]*)"|\'([^\']*)\'|([^<>\'"\\s]*))[^<>]*>/i', $shortStory, $m)) {
				// Адрес первой нормальной картинки в новости
				$imageUrl = false;
				foreach ($m[1] as $imgItem) {
					$_bl = false;
					foreach ($cfg['mediaBlacklist'] as $blItem) {
						if (($imgItem == null) || (strpos($imgItem, $blItem) !== false)) {
							$_bl = true;
							break;
						}
					}
					if ($_bl) {
						continue;
					}
					else {
						$imageUrl = $imgItem;
						break;
					}

				}
			}

			// Если есть картинка из источника - пытаемся её обработать
			if ($imageUrl != $noimage) {
				$imgNameOut    = $imageUrl;
				$imgNameOutBig = $imageUrl;
				// Если включен граббинг картинок - пытаемся их тянуть
				if ($rssItem['grabImages'] == 1) {
					// Если нет нужной папки - создаём её и устанавливаем нужные права
					makeAutoRssDir($dir);
					if ($rssItem['saveOriginalImages'] == 1) {
						makeAutoRssDir($dirThumbs);
					}

					// подрубаем класс для ресайза (и граббинга) картинок
					include_once AUTORSS_DIR . '/resize_class.php';

					// Разделяем высоту и ширину
					$imgSizes = explode('x', $rssItem['imgSize']);

					// Если указана только одна величина - присваиваем второй первую, будет квадрат для exact, auto и crop, иначе класс ресайза жестоко тупит, ожидая вторую переменную.
					if (count($imgSizes) == '1') $imgSizes[1] = $imgSizes[0];
					$imgWidth  = intval($imgSizes[0]);
					$imgHeight = intval($imgSizes[1]);
					// Назначаем переменной новое значение.
					$imgForResize = $imgNameOut;
					// Определяем имя будующей картинки.
					$imgName          = date("md") . substr(basename($imgNameOut), -24);
					$dirSmall         = $dir;
					$dir_prefix_small = $dir_prefix;

					// Если резрешено тянуть оригинальные картинки
					if ($rssItem['saveOriginalImages'] == 1) {
						$dirSmall         = $dirThumbs;
						$dir_prefix_small = $dir_prefix . 'thumbs/';
					}

					// Если картинки не существует - создаём её.
					$resizeType = trim($rssItem['resizeType']);
					if (!file_exists($dirSmall . $imgName)) {
						$resizeImg = new resize($imgForResize);
						$resizeImg->resizeImage($imgWidth, $imgHeight, $resizeType);
						$resizeImg->saveImage($dirSmall . $imgName, 75);

						// Если резрешено тянуть оригинальные картинки
						if ($rssItem['saveOriginalImages'] == 1) {
							$resizeImgBig = new resize($imgForResize);
							$resizeImgBig->resizeImage(0, 0, $resizeType);
							$resizeImgBig->saveImage($dir . $imgName, 75);
						}
					}
					// И передаём дальше уже адрес отресайзенной картинки.
					$imgNameOut = '/uploads/rss/' . $dir_prefix_small . $imgName;
					// Если резрешено тянуть оригинальные картинки
					if ($rssItem['saveOriginalImages'] == 1) {
						$imgNameOutBig = '/uploads/rss/' . $dir_prefix . $imgName;
					}
				}

			}
		}

		// Формируем картинку для вставки в новость.
		$imageTag = ($rssItem['dasableImages'] == 0) ? '<img class="post-image" src="' . $imgNameOut . '" alt="' . $newsItem['title'] . '" /><br /> ' : '';
		if ($rssItem['dasableImages'] == 0 && $rssItem['saveOriginalImages'] == 1) {
			$imageTagBig = '
			<!--TBegin:' . $imgNameOutBig . '|' . $newsItem['title'] . '--><a href="' . $imgNameOutBig . '" rel="highslide" class="highslide "><img src="' . $imgNameOut . '" alt="' . $newsItem['title'] . '" title="' . $newsItem['title'] . '"></a><!--TEnd--> <br />';
		}
		else {
			$imageTagBig = $imageTag;
		}

		// Формируем короткую новость
		$newsItem['short_story'] = $imageTag . textLimit($shortStory, $rssItem['textLimit']);

		// URL источника
		$_permalink = explode('?utm_source', $item->get_permalink());


		$repmalinkFormated = ($rssItem['pseudoLinks'] == 1) ? $rssItem['sourseTextName'] . ' <span class="pseudolink" title="Источник публикации" data-target-' . $rssItem['sourceTarget'] . '="' . $_permalink[0] . '">' . $rssItem['name'] . '</span>' : $rssItem['sourseTextName'] . ' <!--noindex--> <a href="' . $_permalink[0] . '" rel="nofollow" target="_' . $rssItem['sourceTarget'] . '">' . $rssItem['name'] . '</a> <!--/noindex-->';

		// Полная новость
		$fullStoryTags = str_replace(array(', ', ',', ' ,'), ',', $rssItem['fullStoryTags']);
		$fullStoryTags = explode(',', $fullStoryTags);

		switch ($rssItem['fullStoryType']) {
			case '1':
				$newsItem['full_story'] = strip_tags_smart($shortStory, $fullStoryTags);
				break;

			case '0':
				$newsItem['full_story'] = $imageTagBig . textLimit($shortStory, 0, false);
				break;
		}
		$newsItem['full_story'] .= '<p class="source-link-wrapper">' . $repmalinkFormated . '</p>';

		if ($rssItem['allow_br'] == '1') {
			$newsItem['short_story'] = str_replace("\r\n", '<br />', $newsItem['short_story']);
			$newsItem['full_story'] = str_replace("\r\n", '<br />', $newsItem['full_story']);
		}

		// Description & Keywords
		$metatags = createMeta($shortStory);

		$newsItem['descr']    = $metatags['description'];
		$newsItem['keywords'] = $metatags['keywords'];

		// Категория
		$newsItem['category'] = $rssItem['category'];

		// ЧПУ
		$newsItem['alt_name'] = chpuCut($feedItemTitle, $rssItem['chpuCut']);

		// Разрешить комментарии
		$newsItem['allow_comm'] = $rssItem['allow_comm'];

		// Разрешить автоперенос строк
		$newsItem['allow_br'] = $rssItem['allow_br'];

		// Разрешить на главной
		$newsItem['allow_main'] = $rssItem['allow_main'];

		// Категории источника (они же теги)
		$tags    = $item->get_category();
		$rssTags = false;
		if ($tags) {
			$rssTags = entryDecode($tags->get_term());
		}
		$_rsst  = explode(', ', $rssTags);
		$_itags = explode(', ', $rssItem['tags']);

		$_rsst  = (trim($_rsst[0]) != '' && $rssItem['allowRssTags'] == 1) ? $_rsst : array();
		$_itags = (trim($_itags[0]) != '') ? $_itags : array();
		$_atags = array_merge($_itags, $_rsst);

		$mergedRssTags = implode(', ', $_atags);

		$newsItem['tags'] = $mergedRssTags;

		// Символьный код
		$newsItem['symbol'] = dle_substr(translit($feedItemTitle), 0, 1, $config['charset']);

		// Разрешить рейтинг
		$newsItem['allow_rate'] = $rssItem['allow_rating'];

		// Публиковать без модерации
		$newsItem['approve'] = 1;


		// Проверяем пользователя на существование
		if ($rssItem['allowNewUsers'] == 1 && $newsItem['autor'] != '' && $cfg['test'] == false) {
			$curUser = getUserByName($newsItem['autor'], 'name');
			$qi++;
			$newsAuthor = $curUser['name'];

			if (!$curUser) {
				$newUser      = $db->safesql(str_replace("'", '', $newsItem['autor']));
				$translAuthor = translit($newUser);
				$password     = textLimit($translAuthor, 3, false) . '_' . generateHash();
				$email        = trim($translAuthor . '@' . str_replace(array('http://', '/'), '', $config['http_home_url']));
				$addNewUser   = newUserRegister($newUser, $password, $email, $rssItem['newUserGroup']);
				$newsAuthor   = ($addNewUser == '1') ? $newUser : $newsAuthor;
				$qi++;
				$userindex++;
			}
			else {
				userUpdateLastdate($curUser);
				$qi++;
			}
			if ($newsAuthor == '') {
				$newsAuthor = $newsItem['autor'];
			}
		}
		else {
			$newsAuthor = $newsItem['autor'];
		}
		$existTitle = 0;
		if ($rssItem['checkDouble'] == 1) {
			$existTitle = $db->super_query("SELECT COUNT(title) as count FROM " . PREFIX . "_post WHERE title = '" . $newsItem['title'] . "'");
			$existTitle = $existTitle['count'];
			$qi++;
		}

		/**
		 * Пишем в БД
		 */
		if ($newsItem['title'] != "" && trim($newsItem['short_story']) != "" && $cfg['test'] == false && $existTitle == 0) {
			$db->query("INSERT INTO " . PREFIX . "_post
				(
					date,
					autor,
					short_story,
					full_story,
					xfields,
					title,
					descr,
					keywords,
					category,
					alt_name,
					allow_comm,
					approve,
					allow_main,
					allow_br,
					tags
				)
				values (
					'" . $newsItem['date'] . "',
					'$newsAuthor',
					'" . $newsItem['short_story'] . "',
					'" . $newsItem['full_story'] . "',
					'',
					'" . $newsItem['title'] . "',
					'" . $newsItem['descr'] . "',
					'" . $newsItem['keywords'] . "',
					'" . $newsItem['category'] . "',
					'" . $newsItem['alt_name'] . "',
					'" . $newsItem['allow_comm'] . "',
					'" . $newsItem['approve'] . "',
					'" . $newsItem['allow_main'] . "',
					'" . $newsItem['allow_br'] . "',
					'" . $newsItem['tags'] . "'
				)");
			$row['id'] = $db->insert_id();

			$db->query("INSERT INTO " . PREFIX . "_post_extras (news_id, allow_rate, votes, disable_index, access) VALUES('{$row['id']}', '" . $newsItem['allow_rate'] . "', '0', '0', '')");
			$db->query("UPDATE " . USERPREFIX . "_users set news_num=news_num+1 where name='{$newsAuthor}'");

			$summary[$i]['items'][$key]['title'] = $newsItem['title'];
			$summary[$i]['items'][$key]['link']  = $config['http_home_url'] . '?newsid=' . $row['id'];

			$qi++;
			$qi++;
			$qi++;
			$qi++;

			if ($newsItem['tags'] != "") {

				$tags_arr = array();
				$tags     = explode(",", $newsItem['tags']);

				foreach ($tags as $value) {
					$tags_arr[] = "('" . $row['id'] . "', '" . trim($value) . "')";
				}

				$tags_arr = implode(", ", $tags_arr);
				$db->query("INSERT INTO " . PREFIX . "_tags (news_id, tag) VALUES " . $tags_arr);
				$qi++;

			}

			if (isset($tags)) {
				unset($tags);
			}

			$addindex++;
		}
		else {
			$addbadindex++;
			if ($existTitle >= '1') {
				$summary[$i]['items'][$key]['badTitle'] = '<span style="color: #f96">[дубль] ' . $newsItem['title'] . '</span>';
			}
			else {
				$summary[$i]['items'][$key]['badTitle'] = '<span style="color: #333">[не добавлено] ' . $newsItem['title'] . '</span>';
			}
		}

		if ($cfg['test'] == true || $cfg['fulldebug'] == true) {
			$summary[$i]['items'][$key]['short_story'] = $newsItem['short_story'];
			$summary[$i]['items'][$key]['full_story']  = $newsItem['full_story'];
			$summary[$i]['items'][$key]['newsAuthor']  = $newsAuthor;
			$summary[$i]['items'][$key]['tags']        = $newsItem['tags'];

			if ($cfg['fulldebug'] == true) {
				$summary[$i]['items'][$key]['debug'] = $newsItem;
			}
		}

		unset($item);
	}


	if ($rssItem['id'] && !$cfg['test']) {
		$_lastdate = time() + ($config['date_adjust'] * 60);
		$db->query("UPDATE " . PREFIX . "_auto_rss SET lastdate='$_lastdate' WHERE id='" . $rssItem['id'] . "'");
		$qi++;
	}

	$feed->__destruct();
	unset($items);
	unset($feed);
	clear_cache();

	$summary[$i]['feedMemory'] = round(memory_get_usage() / (1024 * 1024), 2) . 'Мб';
	$i++;

}
$info['queries'] = $qi;
$info['addindex'] = $addindex;
$info['addbadindex'] = $addbadindex;
$info['userindex'] = $userindex;
$info['microtime'] = round((microtime(true) - $start), 6) . 'c';


include_once(AUTORSS_DIR . '/send.php');

if ($cfg['fulldebug']) {
	include_once(AUTORSS_DIR . '/debug.php');
}

?>

