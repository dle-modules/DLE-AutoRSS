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

if ($_REQUEST['action'] != 'add') {
	$query       = "SELECT * FROM " . PREFIX . "_auto_rss WHERE id=" . $elementId;
	$elementItem = $db->super_query($query);
}


$name                 = ($elementItem['name']) ? $elementItem['name'] : '';
$url                  = ($elementItem['url']) ? $elementItem['url'] : '';
$tags                 = ($elementItem['tags']) ? $elementItem['tags'] : '';
$allowRssTags_checked = ($elementItem['allowRssTags'] == 1) ? 'checked' : '';
$offline_checked      = ($elementItem['offline'] == 1) ? 'checked' : '';
$allow_main_checked   = ($elementItem['allow_main'] == 1) ? 'checked' : '';
$allow_rating_checked = ($elementItem['allow_rating'] == 1) ? 'checked' : '';
$allow_comm_checked   = ($elementItem['allow_comm'] == 1) ? 'checked' : '';
$allow_br_checked     = ($elementItem['allow_br'] == 1) ? 'checked' : '';
if ($elementItem['date'] == '1') {
	$date_1 = 'selected';
	$date_0 = '';
}
else {
	$date_1 = '';
	$date_0 = 'selected';
}
if ($elementItem['fullStoryType'] == '1') {
	$fullStoryType_1 = 'selected';
	$fullStoryType_0 = '';
}
else {
	$fullStoryType_1 = '';
	$fullStoryType_0 = 'selected';
}
$max_news              = ($elementItem['max_news']) ? $elementItem['max_news'] : $cfg['channel']['max_news'];
$cookie                = ($elementItem['cookie']) ? $elementItem['cookie'] : '';
$category_list         = CategoryNewsSelection($elementItem['category'], 0);
$noimage               = ($elementItem['noimage']) ? $elementItem['noimage'] : $cfg['channel']['noimage'];
$checkDouble_checked   = ($elementItem['checkDouble'] == 1) ? 'checked' : '';
$textLimit             = ($elementItem['textLimit']) ? $elementItem['textLimit'] : $cfg['channel']['textLimit'];
$fullStoryTags         = ($elementItem['fullStoryTags']) ? $elementItem['fullStoryTags'] : $cfg['channel']['fullStoryTags'];
$chpuCut               = ($elementItem['chpuCut']) ? $elementItem['chpuCut'] : $cfg['channel']['chpuCut'];
$authorLogin           = ($elementItem['authorLogin']) ? $elementItem['authorLogin'] : $cfg['channel']['authorLogin'];
$allowNewUsers_checked = ($elementItem['allowNewUsers'] == 1) ? 'checked' : '';
$newUserGroup          = ($elementItem['newUserGroup']) ? $elementItem['newUserGroup'] : $cfg['channel']['newUserGroup'];
$user_groups           = get_groups($newUserGroup);
$sourseTextName        = ($elementItem['sourseTextName']) ? $elementItem['sourseTextName'] : $cfg['channel']['sourseTextName'];
$pseudoLinks_checked   = ($elementItem['pseudoLinks'] == 1) ? 'checked' : '';

if ($elementItem['sourceTarget'] == 'blank') {
	$sourceTarget_blank = 'selected';
	$sourceTarget_self  = '';
}
else {
	$sourceTarget_blank = '';
	$sourceTarget_self  = 'selected';
}

switch ($elementItem['resizeType']) {
	case 'crop':
		$resize_crop = 'selected';
		break;
	case 'exact':
		$resize_exact = 'selected';
		break;
	case 'portrait':
		$resize_portrait = 'selected';
		break;
	case 'landscape':
		$resize_landscape = 'selected';
		break;
	default:
		$resize_auto = 'selected';
		break;
}

$dasableImages_checked      = ($elementItem['dasableImages'] == 1) ? 'checked' : '';
$grabImages_checked         = ($elementItem['grabImages'] == 1) ? 'checked' : '';
$saveOriginalImages_checked = ($elementItem['saveOriginalImages'] == 1) ? 'checked' : '';
$imgSize                    = ($elementItem['imgSize']) ? $elementItem['imgSize'] : $cfg['channel']['imgSize'];

$hidden_fields = <<<HTML
	<input type="hidden" name="action" value="edit" >
	<input type="hidden" name="save" value="yes" >
	<input type="hidden" name="id" value="{$elementItem['id']}" >
HTML;

if ($_REQUEST['action'] == 'add') {
	$allowRssTags_checked       = ($cfg['channel']['allowRssTags'] == 1) ? 'checked' : '';
	$offline_checked            = ($cfg['channel']['offline'] == 1) ? 'checked' : '';
	$allow_main_checked         = ($cfg['channel']['allow_main'] == 1) ? 'checked' : '';
	$allow_rating_checked       = ($cfg['channel']['allow_rating'] == 1) ? 'checked' : '';
	$allow_comm_checked         = ($cfg['channel']['allow_comm'] == 1) ? 'checked' : '';
	$allow_br_checked           = ($cfg['channel']['allow_br'] == 1) ? 'checked' : '';
	$checkDouble_checked        = ($cfg['channel']['checkDouble'] == 1) ? 'checked' : '';
	$allowNewUsers_checked      = ($cfg['channel']['allowNewUsers'] == 1) ? 'checked' : '';
	$pseudoLinks_checked        = ($cfg['channel']['pseudoLinks'] == 1) ? 'checked' : '';
	$dasableImages_checked      = ($cfg['channel']['dasableImages'] == 1) ? 'checked' : '';
	$grabImages_checked         = ($cfg['channel']['grabImages'] == 1) ? 'checked' : '';
	$saveOriginalImages_checked = ($cfg['channel']['saveOriginalImages'] == 1) ? 'checked' : '';
	$hidden_fields              = <<<HTML
	<input type="hidden" name="action" value="add" >
	<input type="hidden" name="save" value="yes" >
HTML;

}


$output = <<<HTML
	<form id="show-list" method="POST" action="{$_SERVER["PHP_SELF"]}?mod={$cfg['moduleName']}">
		{$hidden_fields}
		<div class="descr">
			<div class="form-field clearfix">
				<div class="lebel">&nbsp;</div>
				<div class="control">
					<input type="checkbox" value="1" name="offline" id="offline" class="checkbox" {$offline_checked}><label for="offline"><span></span> Деактивировать канал</label>
				</div>
			</div>
			<div class="form-field clearfix">
				<div class="lebel">URL канала</div>
				<div class="control">
					<input type="text" value="{$url}" name="url" id="url" class="input" >
				</div>
			</div>
			<div class="form-field clearfix">
				<div class="lebel">Название канала</div>
				<div class="control">
					<input type="text" value="{$name}" name="name" id="name" class="input" >
				</div>
			</div>
			<div class="form-field clearfix">
				<div class="lebel">Теги для облака</div>
				<div class="control">
					<input type="text" value="{$tags}" name="tags" id="tags" class="input" > <span class="ttp mini" title="Указываются теги через запятую. Так же сюда будут добавлены теги из канала при парсинге, если они есть и если разрешено их добавление.">?</span>
				</div>
			</div>
			<div class="form-field clearfix">
				<div class="lebel">&nbsp;</div>
				<div class="control">
					<input type="checkbox" value="1" name="allowRssTags" id="allowRssTags" class="checkbox" {$allowRssTags_checked}><label for="allowRssTags"><span></span> Брать теги из канала</label> <span class="ttp mini" title="Разрешить автоматическое добавление тегов из RSS канала (если есть). В качестве тегов берутся категории записи из канала.">?</span>
				</div>
			</div>
			<div class="form-field clearfix">
				<div class="lebel">&nbsp;</div>
				<div class="control">
					<input type="checkbox" value="1" name="checkDouble" id="checkDouble" class="checkbox" {$checkDouble_checked}><label for="checkDouble"><span></span> Проверять дубли</label> <span class="ttp mini" title="Настройка добавит 1 лёгкий запрос на каждую новость.">?</span>
				</div>
			</div>
			<div class="form-field clearfix">
				<div class="lebel">&nbsp;</div>
				<div class="control">
					<input type="checkbox" value="1" name="allow_main" id="allow_main" class="checkbox" {$allow_main_checked}><label for="allow_main"><span></span> Публиковать на главной</label> <!-- <span class="ttp mini" title="Новости из канала будут публиковаться на главной странице.">?</span> -->
				</div>
			</div>
			<div class="form-field clearfix">
				<div class="lebel">&nbsp;</div>
				<div class="control">
					<input type="checkbox" value="1" name="allow_rating" id="allow_rating" class="checkbox" {$allow_rating_checked}><label for="allow_rating"><span></span> Разрешить рейтинг</label>
				</div>
			</div>
			<div class="form-field clearfix">
				<div class="lebel">&nbsp;</div>
				<div class="control">
					<input type="checkbox" value="1" name="allow_comm" id="allow_comm" class="checkbox" {$allow_comm_checked}><label for="allow_comm"><span></span> Разрешить комментарии</label>
				</div>
			</div>
			<div class="form-field clearfix">
				<div class="lebel">&nbsp;</div>
				<div class="control">
					<input type="checkbox" value="1" name="allow_br" id="allow_br" class="checkbox" {$allow_br_checked}><label for="allow_br"><span></span> Разрешить автоперенос строк</label>
				</div>
			</div>
			<div class="form-field clearfix">
				<div class="lebel">Дата новости</div>
				<div class="control">
					<select name="date" id="date" class="styler">
						<option value="1" {$date_1}>Установить текущую</option>
						<option value="0" {$date_0}>Установить из канала</option>
					</select>
				</div>
			</div>
			<div class="form-field clearfix">
				<div class="lebel">Количество новостей</div>
				<div class="control">
					<input type="text" value="{$max_news}" name="max_news" id="max_news" class="input" style="width: 60px" > <span class="ttp mini" title="Как правило канал отдаёт не более 10 элементов, так что ставить цифру больше не имеет смысла. К тому же импорт - довольно сложный процесс, который грузит сервер, имейте это ввиду.">?</span>
				</div>
			</div>
			<!--<div class="form-field clearfix">
				<div class="lebel">Cookies сайта</div>
				<div class="control">
					<textarea name="cookie" id="cookie" class="input">{$cookie}</textarea> <span class="ttp mini" title="Иногда для получения полной информации с сайта необходима авторизация на сайте. Вы можете задать cookies которые использует сайт для авторизации, например для сайтов на DataLife Engine необходимо ввести<br /><br /><b>dle_user_id=id</b><br /><b>dle_password=71820d7c524</b><br /><br />На каждой новой строке задается новое значение cookies.">?</span>
				</div>
			</div>-->
			<div class="form-field clearfix">
				<div class="lebel">Категория</div>
				<div class="control">
					<select name="category" id="category" class="styler">
						{$category_list}
					</select>
				</div>
			</div>
			<div class="form-field clearfix">
				<div class="lebel">Картинка-заглушка</div>
				<div class="control">
					<input type="text" value="{$noimage}" name="noimage" id="noimage" class="input"> <span class="ttp mini" title="Путь к картинке-заглушке на случай, если она отсутствует у новости.">?</span>
				</div>
			</div>
			<div class="form-field clearfix">
				<div class="lebel">Ограничение символов</div>
				<div class="control">
					<input type="text" value="{$textLimit}" name="textLimit" id="textLimit" class="input" style="width: 60px;"> <span class="ttp mini" title="Количество символов в краткой новости. Обрезка происходит до логического конца слова.">?</span>
				</div>
			</div>
			<div class="form-field clearfix">
				<div class="lebel">Тип полной новости</div>
				<div class="control">
					<select name="fullStoryType" id="fullStoryType" class="styler">
						<option value="0" {$fullStoryType_0}>Первая картинка и текст</option>
						<option value="1" {$fullStoryType_1}>Текст и все картинки</option>
					</select>
					<span class="ttp mini" title="<b>Первая картинка и текст</b> - будет взята первая картинка из новости (согласно общим настройкам) и текст, очищенный от html-тегов. <br><b>Текст и все картинки</b> - в текста будут оставлены теги p, h2, h3, b, img (по умолчанию)">?</span>
				</div>
			</div>
			<div class="form-field clearfix">
				<div class="lebel">Теги, которые не будут обрабатываться</div>
				<div class="control">
					<input type="text" value="{$fullStoryTags}" name="fullStoryTags" id="fullStoryTags" class="input"> <span class="ttp mini" title="Перечисляем через запятую теги, которые не будут обработаны парсером при обработке полной новости. Работает в паре с типом полной новости = Текст и все картинки.">?</span>
				</div>
			</div>
			<div class="form-field clearfix">
				<div class="lebel">Ограничение символов в ЧПУ</div>
				<div class="control">
					<input type="text" value="{$chpuCut}" name="chpuCut" id="chpuCut" class="input" style="width: 60px;"> <span class="ttp mini" title="Количество символов в ЧПУ новости. Обрезка происходит до логического конца слова.">?</span>
				</div>
			</div>
			<div class="form-field clearfix">
				<div class="lebel">Логин автора новости</div>
				<div class="control">
					<input type="text" value="{$authorLogin}" name="authorLogin" id="authorLogin" class="input"> <span class="ttp mini" title="Логин автора новости, если автора нет в канале или запрещено авторегистривание пользователей.">?</span>
				</div>
			</div>
			<div class="form-field clearfix">
				<div class="lebel">&nbsp;</div>
				<div class="control">
					<input type="checkbox" value="1" name="allowNewUsers" id="allowNewUsers" class="checkbox" {$allowNewUsers_checked}><label for="allowNewUsers"><span></span> Разрешить авторегистрацию юзеров</label> <span class="ttp mini" title="Автоматически регистрировать авторов из канала. При этом для автора будет создан email по маске [транслит логина]@[адрес сайта] и пароль из восьми случайных цифр и букв.">?</span>
				</div>
			</div>
			<div class="form-field clearfix">
				<div class="lebel">Группа для новых пользователей</div>
				<div class="control">
					<select name="newUserGroup" id="newUserGroup" class="styler">
						{$user_groups}
					</select>
				</div>
			</div>
			<div class="form-field clearfix">
				<div class="lebel">Текст перед ссылкой на источник</div>
				<div class="control">
					<input type="text" value="{$sourseTextName}" name="sourseTextName" id="sourseTextName" class="input"> <span class="ttp mini" title="В конец полной новости добавляется ссылка на источник, тут можно задать текст, который добавится перед ссылкой.">?</span>
				</div>
			</div>
			<div class="form-field clearfix">
				<div class="lebel">&nbsp;</div>
				<div class="control">
					<input type="checkbox" value="1" name="pseudoLinks" id="pseudoLinks" class="checkbox" {$pseudoLinks_checked}><label for="pseudoLinks"><span></span> Использовать "псевдоссылку" на источник</label> <span class="ttp mini" title="Если отметить чекбокс - то ссылка будет заменена на span, а &quot;открытие&quot; ссылки будет обрабатываться через JS. Подобная &quot;ссылка&quot; не видна поисковикам.">?</span>
				</div>
			</div>
			<div class="form-field clearfix">
				<div class="lebel">Открывать ссылку</div>
				<div class="control">
					<select name="sourceTarget" id="sourceTarget" class="styler">
						<option value="blank" {$sourceTarget_blank}>в новой вкладке</option>
						<option value="self" {$sourceTarget_self}>в текущей вкладке</option>
					</select>
				</div>
			</div>
			<div class="form-field clearfix">
				<div class="lebel">&nbsp;</div>
				<div class="control">
					<input type="checkbox" value="1" name="dasableImages" id="dasableImages" class="checkbox" {$dasableImages_checked}><label for="dasableImages"><span></span> Отключить парсинг картинок</label> <span class="ttp mini" title="Если отметить чекбокс - то картинки из канала не будут обрабатываться.">?</span>
				</div>
			</div>
			<div class="form-field clearfix">
				<div class="lebel">&nbsp;</div>
				<div class="control">
					<input type="checkbox" value="1" name="grabImages" id="grabImages" class="checkbox" {$grabImages_checked}><label for="grabImages"><span></span> Тянуть картинки к себе</label> <span class="ttp mini" title="Если отметить чекбокс - то картинки из канала будут скачиваться на сайт.">?</span>
				</div>
			</div>
			<div class="form-field clearfix">
				<div class="lebel">&nbsp;</div>
				<div class="control">
					<input type="checkbox" value="1" name="saveOriginalImages" id="saveOriginalImages" class="checkbox" {$saveOriginalImages_checked}><label for="saveOriginalImages"><span></span> Сохранять оригиналы</label> <span class="ttp mini" title="Если отметить чекбокс - то картинки из канала будут скачиваться на сайт и сохраняться ещё и в <b>оригинальном размере</b>, а в полную новость будут вставляться в виде миниатюр с увеличением по клику.">?</span>
				</div>
			</div>
			<div class="form-field clearfix">
				<div class="lebel">Размер картинок</div>
				<div class="control">
					<input type="text" value="{$imgSize}" name="imgSize" id="imgSize" class="input"> <span class="ttp mini" title="Размер уменьшеных картинок, можно задавать как 200x150 (ширина x высота), так и просто 250">?</span>
				</div>
			</div>
			<div class="form-field clearfix">
				<div class="lebel">Тип ресайза картинок</div>
				<div class="control">
					<select name="resizeType" id="resizeType" class="styler">
						<option value="auto" {$resize_auto}>вписать в рамки (авто)</option>
						<option value="exact" {$resize_exact}>точный размер (без учёта пропорций)</option>
						<option value="landscape" {$resize_landscape}>уменьшение по ширине</option>
						<option value="portrait" {$resize_portrait}>уменьшение по высоте</option>
						<option value="crop" {$resize_crop}>crop (уменьшение и обрезка лишнего)</option>
					</select>
				</div>
			</div>
			<hr>
			<div class="form-field clearfix">
				<div class="lebel">&nbsp;</div>
				<div class="control">
					<button type="submit" class="btn">Сохранить</button>
					<a class="btn active" href="{$config['admin_path']}?mod={$cfg['moduleName']}">Отменить</a>
				</div>
			</div>
		</div>
	</form>
HTML;
?>