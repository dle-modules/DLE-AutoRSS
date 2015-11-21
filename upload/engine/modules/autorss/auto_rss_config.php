<?php
// Конфиг AutoRSS

$cfg = array( // Пароль доступа к модулю
	'pass'           => '123',

	// Если прописать с адресной строке  &test - будет запущен тестовый режим
	'test'           => (!empty($_REQUEST['test'])) ? true : false,

	// Если прописать с адресной строке &fulldebug - будет показываться полны дебаг (исходые данные канала)
	'fulldebug'      => (!empty($_REQUEST['fulldebug'])) ? true : false,

	// Если прописать с адресной строке &id=1,2,3 - будут обрабатываться только каналы с соответсствующим id
	'id'             => (!empty($_REQUEST['id'])) ? $_REQUEST['id'] : false,

	// Если прописать с адресной строке &limit=0x10 - будут взяты только первые 10 каналов (можно написать &limit=10x10 - будут взяты 10 каналов начиная с 10го по счёту)
	'limit'          => (!empty($_REQUEST['limit'])) ? $_REQUEST['limit'] : false,

	// Отправлять PM админу с информацией о работе скрипта.
	'sendpm'         => (!empty($_REQUEST['sendpm'])) ? true : false,

	// Отправлять email админу с информацией о работе скрипта.
	'sendmail'       => (!empty($_REQUEST['sendmail'])) ? true : false,

	// Чёрный список медиаресурсов (счётчики и всякая хрень)
	'mediaBlacklist' => array('feeds.feedburner.com', 'share.feedsportal.com', 'da.feedsportal.com', 'rss.feedsportal.com', 'res.feedsportal.com', 'res1.feedsportal.com', 'res2.feedsportal.com', 'res3.feedsportal.com', 'pi.feedsportal.com', 'rss.nytimes.com', 'feeds.wordpress.com', 'stats.wordpress.com', 'rss.cnn.com', 'twitter.com/home?status=', 'twitter.com/share', 'twitter_icon_large.png', 'www.facebook.com/sharer.php', 'facebook_icon_large.png', 'plus.google.com/share', 'www.gstatic.com/images/icons/gplus-16.png', 'www.gstatic.com/images/icons/gplus-32.png', 'www.gstatic.com/images/icons/gplus-64.png', 'data/emoticons', 'dleimages/', 'smiles/',),

	// Настройки для каналов по умолчанию (Это для начальной настройки канала)
	'channel'        => array( // Если задать переменную - канал не будет обрабатываться
		'offline'            => 0,

		// Публиковать на главной 
		'allow_main'         => 1,

		// Разрешить рейтинг
		'allow_rating'       => 1,

		// Резрешить комментарии
		'allow_comm'         => 1,

		// Автоматический перенос строк
		'allow_br'           => 1,

		// Формат даты (текущая дата (1) или из канала(0))
		'date'               => 0,

		// Максимальное кол-во новостей
		'max_news'           => 10,

		// Брать тги из канала
		'allowRssTags'       => 1,

		// Картинка-заглушка
		'noimage'            => '/templates/' . $config['skin'] . '/images/noimage.png',

		// Проверка на дубли (добавляет +1 запрос на каждую новость), чтобы отключить - нужно вписать false;
		'checkDouble'        => 1,

		// Кол-во символов в краткой новости
		'textLimit'          => '500',

		// Тип обработки полной новости
		// 0 - оставлять только текст и первую картинку
		// 1 - оставлять текст и картинки из источника
		'fullStoryType'      => '0',

		// Теги, которые не будут вырезаться из полной новости, если fullStoryType == 1
		'fullStoryTags'      => 'p, h2, h3, b, img, a',

		// Кол-во символов в ЧПУ новости
		'chpuCut'            => '30',

		// Имя пользователя под которым будут опубликованы новости из RSS
		'authorLogin'        => 'autoRSS',

		// Разрешать добавление новых пользователей в БД, если их имя есть в rss-канале
		'allowNewUsers'      => 1,

		// Группа, в которую будут регистрироваться новые пользователи
		'newUserGroup'       => 3,

		// Надпись "Источник"
		'sourseTextName'     => 'Источник:',

		// Использовать вместо настоящей ссылки на источник псевдоссылку (атрибут data-target-).
		'pseudoLinks'        => 1,

		// Атрибут будет добавлен к data-* если используются псевдоссылки или к target="_*" если используются настоящие ссылки
		'sourceTarget'       => 'blank',

		// Отключить обаботку и парсинг картинок из канала?
		'dasableImages'      => 0,

		// Тянуть картинки себе на сайт
		'grabImages'         => 1,

		// Сохранять оригиналы изображений сбе на сайт?
		'saveOriginalImages' => 0,

		// Размер уменьшеных картинок, можно задавать как 200x150, так и просто 250
		'imgSize'            => '500x300',

		// Тип создания уменьшенных изображений (exact, portrait, landscape, auto, crop)
		'resizeType'         => 'auto',),


	// 
	// 
	// ЭТИ НАСТРОЙКИ ТРОГАТЬ НЕ НУЖНО, иначе можно сломать модуль.
	// 
	// 
	// Идентификатор модуля (для внедения в админпанель и назначение имени иконки с расширением .png)
	'moduleName'     => 'auto_rss',

	// Название модуля - показывается как в установщике, так и в админке.
	'moduleTitle'    => 'AutoRSS',

	// Описание модуля, для установщика и админки.
	'moduleDescr'    => 'Модуль для автоматического парсинга и импорта RSS-лент.',

	// Версия модуля, для установщика
	'moduleVersion'  => '0.7',

	// Дата выпуска модуля, для установщика
	'moduleDate'     => '17.03.2014',

	// Версии DLE, поддержваемые модулем, для установщика
	'dleVersion'     => '9.8-10.x',

	// ID групп, для которых доступно управление модулем в админке.
	'allowGroups'    => '1',

	// Массив с запросами, которые будут выполняться при установке
	'queries'        => array("DROP TABLE IF EXISTS " . PREFIX . "_auto_rss", "CREATE TABLE " . PREFIX . "_auto_rss (
			`id` smallint(6) NOT NULL AUTO_INCREMENT,
			`url` varchar(255) NOT NULL,
			`name` text NOT NULL,
			`tags` varchar(255) NOT NULL,
  			`allowRssTags` tinyint(1) NOT NULL DEFAULT '1',
			`allow_main` tinyint(1) NOT NULL DEFAULT '1',
			`allow_rating` tinyint(1) NOT NULL DEFAULT '1',
			`allow_comm` tinyint(1) NOT NULL DEFAULT '1',
			`allow_br` tinyint(1) NOT NULL DEFAULT '1',
			`date` tinyint(1) NOT NULL DEFAULT '0',
			`max_news` tinyint(4) NOT NULL DEFAULT '10',
			`cookie` text NOT NULL,
			`category` smallint(5) NOT NULL DEFAULT '0',
			`lastdate` int(11) NOT NULL,
			`offline` tinyint(4) NOT NULL DEFAULT '0',
			`noimage` varchar(255) NOT NULL,
			`checkDouble` tinyint(1) NOT NULL DEFAULT '1',
			`textLimit` smallint(6) NOT NULL DEFAULT '500',
			`fullStoryType` tinyint(1) NOT NULL DEFAULT '0',
			`fullStoryTags` varchar(50) NOT NULL DEFAULT 'p, h2, h3, b, img, a',
			`chpuCut` smallint(3) NOT NULL DEFAULT '30',
			`authorLogin` varchar(50) NOT NULL,
			`allowNewUsers` tinyint(1) NOT NULL DEFAULT '1',
			`newUserGroup` tinyint(4) NOT NULL DEFAULT '3',
			`sourseTextName` varchar(50) NOT NULL,
			`pseudoLinks` tinyint(1) NOT NULL DEFAULT '1',
			`sourceTarget` varchar(20) NOT NULL DEFAULT 'blank',
			`dasableImages` tinyint(4) NOT NULL DEFAULT '0',
			`grabImages` tinyint(1) NOT NULL DEFAULT '1',
			`saveOriginalImages` tinyint(1) NOT NULL DEFAULT '0',
			`imgSize` char(20) NOT NULL DEFAULT '500x300',
			`resizeType` char(20) NOT NULL DEFAULT 'auto',
			PRIMARY KEY (`id`)
			) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */",),

	// Устанавливать админку (true/false). Включает показ кнопки установки и удаления админки.
	'installAdmin'   => true,

	// Отображать шаги утановки модуля
	'steps'          => false

);
?>