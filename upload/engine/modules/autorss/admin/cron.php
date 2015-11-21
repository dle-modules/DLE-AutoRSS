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
$output = <<<HTML
<div class="descr">
	<h2>Команды для выполнения через cron <small>(Скопируйте одну и вставьте в планировщик)</small>:</h2>
	<div class="form-field form-field-large clearfix">
		<div class="lebel">Через wget</div>
		<div class="control">
			<input type="text" class="input" onclick="this.select();" value="/usr/bin/wget -O - -q &quot;{$config['http_home_url']}autorss.php?pass={$cfg['pass']}&quot;">
		</div>
	</div>
	<div class="form-field form-field-large clearfix">
		<div class="lebel">Через php</div>
		<div class="control">
			<input type="text" class="input" onclick="this.select();" value="/usr/bin/php -f {$_SERVER["DOCUMENT_ROOT"]}/autorss.php?pass={$cfg['pass']}"><p></p>
			<small class="red">Путь к php или wget у вас может отличаться!</small> <br><small class="gray">Проверьте корректность пути (можно уточнить в ТП хостинга).</small>
		</div>
	</div>
</div>

<h2>Параметры для настройки выполнения команды:</h2>
<table class="big-padding">
	<tr>
		<th>Параметр</th>
		<th>Описание параметра</th>
	</tr>
	<tr>
		<td>
		 	<code>&test=1</code>
		 </td>
		<td>
		Если прописать с адресной строке  &test=1 - будет запущен тестовый режим, без занесения данных в БД.
		</td>
	</tr>
	<tr>
		<td>
		 	<code>&fulldebug=1</code>
		 </td>
		<td>
		Если прописать с адресной строке &fulldebug=1 - будет показываться полный дебаг, всё, что попадёт в новость.
		</td>
	</tr>
	<tr>
		<td>
		 	<code>&id=1-5,8-15</code>
		 </td>
		<td>
		Если прописать с адресной строке &id=1,2,3 - будут обрабатываться только каналы с соответсствующим id, можно перечислять каналы через запятую и тире.
		</td>
	</tr>
	<tr>
		<td>
		 	<code>&limit=0x10</code>
		 </td>
		<td>
		Если прописать с адресной строке &limit=0x10 - будут взяты только первые 10 каналов (можно написать &limit=10x10 - будут взяты 10 каналов начиная с 10го по счёту)
		</td>
	</tr>
	<tr>
		<td>
		 	<code>&sendpm=1</code>
		 </td>
		<td>
		Отправлять PM админу с информацией о работе скрипта.
		</td>
	</tr>
	<tr>
		<td>
		 	<code>&sendmail=1</code>
		 </td>
		<td>
		Отправлять Email админу с информацией о работе скрипта.
		</td>
	</tr>
</table>
HTML;
?>