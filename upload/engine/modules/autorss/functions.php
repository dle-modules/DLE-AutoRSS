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


/**
 * Обработка строк, чтобы по сто раз одно и тоже не писать, для удобаства в общем))
 * @param $data - входящая строка
 *
 * @return обработанная строка
 */
function safeParse($data) {
    $data = str_replace("'", '&#039;', str_replace(array("\t", "\n", "\r"), "", trim($data)));

    return $data;
}

/**
 * [entryDecode description]
 *
 * @param  [type] $string [description]
 *
 * @return [type]         [description]
 */
function entryDecode($string) {
    global $config;

    return html_entity_decode($string, ENT_QUOTES, $config['charset']);
}

/**
 * Получение диапазона между двумя цифрами, и не только
 * @param string $diapasone
 *
 * @return string
 * @author Elkhan I. Isaev <elhan.isaev@gmail.com>
 */

function getDiapazone($diapazone = false) {
    if ($diapazone !== false) {
        $diapazone = str_replace(" ", "", $diapazone);

        if (strpos($diapazone, ',') !== false) {
            $diapazoneArray = explode(',', $diapazone);
            $diapazoneArray = array_diff($diapazoneArray, array(null));

            foreach ($diapazoneArray as $v) {
                if (strpos($v, '-') !== false) {
                    preg_match("#(\d+)-(\d+)#i", $v, $test);

                    $diapazone = !empty($diapazone) && is_array($diapazone) ? array_merge($diapazone, (!empty ($test) ? range($test[1], $test[2]) : array())) : (!empty ($test) ? range($test[1], $test[2]) : array());

                }
                else {
                    $diapazone = !empty($diapazone) && is_array($diapazone) ? array_merge($diapazone, (!empty ($v) ? array((int)$v) : array())) : (!empty ($v) ? array((int)$v) : array());
                }
            }

        }
        elseif (strpos($diapazone, '-') !== false) {

            preg_match("#(\d+)-(\d+)#i", $diapazone, $test);
            $diapazone = !empty ($test) ? range($test[1], $test[2]) : array();

        }
        else {
            $diapazone = array((int)$diapazone);
        }

        $diapazone = !empty ($diapazone) ? array_unique($diapazone) : array();
        $diapazone = implode(',', $diapazone);
    }

    return $diapazone;
}


/**
 * Генерация хэша - используется для создания пароля нового пользователя))
 * @return пароль
 */

function generateHash($leight = 5) {
    $x   = '';
    $str = "qwertyuiopasdfghjklzxcvbnm123456789";
    for ($i = 0; $i < $leight; $i++) {
        $x .= substr($str, mt_rand(0, strlen($str) - 1), 1);
    }

    return $x;
}


/**
 * Обрезка текста до логического конца слова.
 * @param $data     - текст для обрезки
 * @param $count    - на сколько обрезать (по умолчанию 500)
 * @param $showDots - показывать точки в конце
 *
 * @return обрезанный текст
 */

function textLimit($data, $count = '500', $showDots = true) {
    global $config;

    $hellip = ($showDots) ? '&hellip;' : '';
    $data   = stripslashes(trim(strip_tags_smart($data, array('<br>'))));
    $data   = trim(str_replace(array('<br>', '<br />', '<br />', '&nbsp;'), ' ', $data));

    if ($count && dle_strlen($data, $config['charset']) > $count) {
        $data = dle_substr($data, 0, $count, $config['charset']);

        if ($word_pos = dle_strrpos($data, ' ', $config['charset'])) {
            $data = dle_substr($data, 0, $word_pos, $config['charset']);
        }

        $lastchar = substr($data, -1, 1);
        if ($lastchar == '.' || $lastchar == '!' || $lastchar == '?') $hellip = '';

        $data = $data . $hellip;

    }

    return $data;
}


/**
 * Преобразование в транслит
 * @param $string - входные данные
 *
 * @return translit
 */

function translit($string) {
    $replace = array('а' => 'a', 'л' => 'l', 'у' => 'u', 'б' => 'b', 'м' => 'm', 'т' => 't', 'в' => 'v', 'н' => 'n', 'ы' => 'y', 'г' => 'g', 'о' => 'o', 'ф' => 'f', 'д' => 'd', 'п' => 'p', 'и' => 'i', 'р' => 'r', 'А' => 'A', 'Л' => 'L', 'У' => 'U', 'Б' => 'B', 'М' => 'M', 'Т' => 'T', 'В' => 'V', 'Н' => 'N', 'Ы' => 'Y', 'Г' => 'G', 'О' => 'O', 'Ф' => 'F', 'Д' => 'D', 'П' => 'P', 'И' => 'I', 'Р' => 'R', 'з' => 'z', 'ц' => 'c', 'к' => 'k', 'ж' => 'zh', 'ч' => 'ch', 'х' => 'kh', 'е' => 'e', 'с' => 's', 'ё' => 'jo', 'э' => 'eh', 'ш' => 'sh', 'й' => 'jj', 'щ' => 'shh', 'ю' => 'ju', 'я' => 'ja', 'З' => 'Z', 'Ц' => 'C', 'К' => 'K', 'Ж' => 'ZH', 'Ч' => 'CH', 'Х' => 'KH', 'Е' => 'E', 'С' => 'S', 'Ё' => 'JO', 'Э' => 'EH', 'Ш' => 'SH', 'Й' => 'JJ', 'Щ' => 'SHH', 'Ю' => 'JU', 'Я' => 'JA', 'Ь' => "", 'Ъ' => "", 'ъ' => "", 'ь' => "", "_" => "-", "'" => "", "`" => "", "^" => "", " " => "-", '.' => '', ',' => '', ':' => '', '"' => '', "'" => '', '<' => '', '>' => '', '«' => '', '»' => '', ' ' => '-', "&" => "-", "/" => "-", "[" => "", "]" => "", "|" => "", "#" => "", "?" => "", "(" => "", ")" => "", "$" => "", "--" => "-", "–" => "-", "%" => "", "+" => "");

    $_r = strtr($string, $replace);

    return str_replace(array('---', '--'), '-', $_r);
}


/**
 * Функция создания текста ЧПУ
 * @param  string $string  текст
 * @param  int    $count   количество символов
 *
 * @return string        транслитерированный текст, обрезанный до нужной длинны.
 */

function chpuCut($string, $count = false) {
    $string = strtolower($string);
    $_r     = textLimit($string, $count, false);
    $_r     = translit($_r);

    return $_r;

}


/**
 * Добавление в базу нового пользователя
 * @param $login    - Имя пользователя
 * @param $password - Пароль
 * @param $email    - почта
 * @param $group    - группа, в которую регистрировать юзера
 *
 * @return -1 провал или 1 успех.
 */

function newUserRegister($login, $password, $email, $group) {
    global $db, $config;
    if (preg_match("/[\||\'|\<|\>|\[|\]|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\{\+]/", $login)) return -1;

    $password = md5(md5($password));
    $group    = intval($group);
    $now      = time() + ($config['date_adjust'] * 60);
    $q        = $db->query("INSERT into " . USERPREFIX . "_users (email, password, name, user_group, lastdate, reg_date) VALUES ('$email', '$password', '$login', '$group', '$now', '$now')");

    return 1;
}

/**
 * Получение информации о пользователе по его имени
 * @param $name        string - Имя пользователя
 * @param $select_list string - Перечень полей с информации или * для всех
 *
 * @return Массив с данными в случае успеха и false если пользователь не найден
 */
function getUserByName($name, $select_list = "*") {
    global $db;
    $name = $db->safesql($name);
    if ($name == '') return false;
    $userinfo = $db->super_query("SELECT " . $select_list . " FROM " . USERPREFIX . "_users WHERE name = '" . $name . "'");
    if (count($userinfo) == 0) return false;
    else {
        return $userinfo;
    }
}


/**
 * Обновление даты последнего посещения пользователя
 * @param  str $user имя пользователя
 *
 * @return -1 провал или 1 успех.
 */
function userUpdateLastdate($user) {
    global $db, $config;
    $now = time() + ($config['date_adjust'] * 60);
    $q   = $db->query("UPDATE " . USERPREFIX . "_users SET lastdate='$now' WHERE name='{$user}'");

    return 1;
}


/**
 * Создаём метатеги (keywords и description)
 *
 * @param string $story
 *
 * @return string
 */
function createMeta($story) {
    global $config;
    $keyword_count = 20;
    $newarr        = array();
    $headers       = array();
    $quotes        = array("\x22", "\x60", "\t", "\n", "\r", ",", ".", "/", "¬", "#", ";", ":", "@", "~", "[", "]", "{", "}", "=", "-", "+", ")", "(", "*", "^", "%", "$", "<", ">", "?", "!", '"');
    $fastquotes    = array("\x22", "\x60", "\t", "\n", "\r", '"', "\\", '\r', '\n', "/", "{", "}", "[", "]");

    $story = textLimit($story, false);

    $story                  = str_replace($fastquotes, "", $story);
    $headers['description'] = textLimit($story, 190);

    $story = str_replace($quotes, "", $story);
    $arr   = explode(" ", $story);

    foreach ($arr as $word) {
        if (dle_strlen($word, $config['charset']) > 4) $newarr[] = $word;
    }

    $arr = array_count_values($newarr);
    arsort($arr);
    $arr    = array_keys($arr);
    $offset = 0;
    $arr    = array_slice($arr, $offset, $keyword_count);

    $headers['keywords'] = implode(", ", $arr);

    return $headers;
}


/**
 * Отправка пользователю персонального сообщения
 * @param $user_id int - ID получателя
 * @param $subject string - тема сообщения
 * @param $text    string - текст сообщения
 * @param $from    string - имя отправителя
 *
 * @return int - код
 *        -1: получатель не существует
 *         0: операция неудалась
 *         1: операция прошла успешно
 */
function sendPM($user_id, $subject, $text, $from) {
    global $db;
    $user_id   = (int)$user_id;
    $count_arr = $db->super_query("SELECT COUNT(user_id) as count FROM " . USERPREFIX . "_users WHERE user_id = '$user_id'");
    if ($count_arr['count'] == 0) {
        return -1;
    }
    $subject = $db->safesql($subject);
    $text    = $db->safesql($text);
    $from    = $db->safesql($from);
    $now     = time();
    $q       = $db->query("INSERT into " . PREFIX . "_pm (subj, text, user, user_from, date, pm_read, folder) VALUES ('$subject', '$text', '$user_id', '$from', '$now', '0', 'inbox')");
    if (!$q) {
        return 0;
    }
    $db->query("UPDATE " . USERPREFIX . "_users set pm_unread = pm_unread + 1, pm_all = pm_all+1  where user_id = '$user_id'");

    return 1;

}


/**
 * Отлавливаем данные о кодировке файла (utf-8 или windows-1251);
 *
 * @param  string $string - строка (или массив), в которой требуется определить кодировку.
 *
 * @return array          - возвращает массив с определением конфликта кодировки строки и сайта, а так же саму кодировку строки.
 */
function chasetConflict($string) {
    global $config;
    if (is_array($string)) {
        $string = implode(' ', $string);
    }
    $detect             = preg_match('%(?:
        [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
        |\xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
        |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
        |\xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
        |\xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
        |[\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
        |\xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
        )+%xs', $string);
    $stringCharset      = ($detect == '1') ? 'utf-8' : 'windows-1251';
    $config['charset']  = strtolower($config['charset']);
    $return             = array();
    $return['conflict'] = ($stringCharset == $config['charset']) ? false : true;
    $return['charset']  = $stringCharset;

    return $return;
}

/**
 * Создание папки для картинок.
 * @param  str $dir путь к папке
 */
function makeAutoRssDir($dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
        @chmod($dir, 0755);
    }
    if (!chmod($dir, 0755)) {
        @chmod($dir, 0755);
    }
}

/**
 * Более продвинутый аналог strip_tags() для корректного вырезания тагов из html кода.
 * Функция strip_tags(), в зависимости от контекста, может работать не корректно.
 * Возможности:
 *   - корректно обрабатываются вхождения типа "a < b > c"
 *   - корректно обрабатывается "грязный" html, когда в значениях атрибутов тагов могут встречаться символы < >
 *   - корректно обрабатывается разбитый html
 *   - вырезаются комментарии, скрипты, стили, PHP, Perl, ASP код, MS Word таги, CDATA
 *   - автоматически форматируется текст, если он содержит html код
 *   - защита от подделок типа: "<<fake>script>alert('hi')</</fake>script>"
 *
 * @param   string $s
 * @param   array  $allowable_tags     Массив тагов, которые не будут вырезаны
 *                                      Пример: 'b' -- таг останется с атрибутами, '<b>' -- таг останется без атрибутов
 * @param   bool   $is_format_spaces   Форматировать пробелы и переносы строк?
 *                                      Вид текста на выходе (plain) максимально приближеется виду текста в браузере на входе.
 *                                      Другими словами, грамотно преобразует text/html в text/plain.
 *                                      Текст форматируется только в том случае, если были вырезаны какие-либо таги.
 * @param   array  $pair_tags          массив имён парных тагов, которые будут удалены вместе с содержимым
 *                               см. значения по умолчанию
 * @param   array  $para_tags          массив имён парных тагов, которые будут восприниматься как параграфы (если $is_format_spaces = true)
 *                               см. значения по умолчанию
 *
 * @return  string
 *
 * @license  http://creativecommons.org/licenses/by-sa/3.0/
 * @author   Nasibullin Rinat, http://orangetie.ru/
 * @charset  ANSI
 * @version  4.0.14
 */
function strip_tags_smart(/*string*/
    $s, array $allowable_tags = null, /*boolean*/
    $is_format_spaces = true, array $pair_tags = array('script', 'style', 'map', 'iframe', 'frameset', 'object', 'applet', 'comment', 'button', 'textarea', 'select'), array $para_tags = array('p', 'td', 'th', 'li', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'form', 'title', 'pre')) {
    //return strip_tags($s);
    static $_callback_type = false;
    static $_allowable_tags = array();
    static $_para_tags = array();
    #regular expression for tag attributes
    #correct processes dirty and broken HTML in a singlebyte or multibyte UTF-8 charset!
    static $re_attrs_fast_safe = '(?![a-zA-Z\d])  #statement, which follows after a tag
                                   #correct attributes
                                   (?>
                                       [^>"\']+
                                     | (?<=[\=\x20\r\n\t]|\xc2\xa0) "[^"]*"
                                     | (?<=[\=\x20\r\n\t]|\xc2\xa0) \'[^\']*\'
                                   )*
                                   #incorrect attributes
                                   [^>]*+';

    if (is_array($s)) {
        if ($_callback_type === 'strip_tags') {
            $tag = strtolower($s[1]);
            if ($_allowable_tags) {
                #tag with attributes
                if (array_key_exists($tag, $_allowable_tags)) return $s[0];

                #tag without attributes
                if (array_key_exists('<' . $tag . '>', $_allowable_tags)) {
                    if (substr($s[0], 0, 2) === '</') return '</' . $tag . '>';
                    if (substr($s[0], -2) === '/>') return '<' . $tag . ' />';

                    return '<' . $tag . '>';
                }
            }
            if ($tag === 'br') return "\r\n";
            if ($_para_tags && array_key_exists($tag, $_para_tags)) return "\r\n\r\n";

            return '';
        }
        trigger_error('Unknown callback type "' . $_callback_type . '"!', E_USER_ERROR);
    }

    if (($pos = strpos($s, '<')) === false || strpos($s, '>', $pos) === false) #speed improve
    {
        #tags are not found
        return $s;
    }

    $length = strlen($s);

    #unpaired tags (opening, closing, !DOCTYPE, MS Word namespace)
    $re_tags = '~  <[/!]?+
                   (
                       [a-zA-Z][a-zA-Z\d]*+
                       (?>:[a-zA-Z][a-zA-Z\d]*+)?
                   ) #1
                   ' . $re_attrs_fast_safe . '
                   >
                ~sxSX';

    $patterns = array('/<([\?\%]) .*? \\1>/sxSX', #встроенный PHP, Perl, ASP код
        '/<\!\[CDATA\[ .*? \]\]>/sxSX', #блоки CDATA
        #'/<\!\[  [\x20\r\n\t]* [a-zA-Z] .*?  \]>/sxSX',  #:DEPRECATED: MS Word таги типа <![if! vml]>...<![endif]>

        '/<\!--.*?-->/sSX', #комментарии

        #MS Word таги типа "<![if! vml]>...<![endif]>",
        #условное выполнение кода для IE типа "<!--[if expression]> HTML <![endif]-->"
        #условное выполнение кода для IE типа "<![if expression]> HTML <![endif]>"
        #см. http://www.tigir.com/comments.htm
        '/ <\! (?:--)?+
               \[
               (?> [^\]"\']+ | "[^"]*" | \'[^\']*\' )*
               \]
               (?:--)?+
           >
         /sxSX',);
    if ($pair_tags) {
        #парные таги вместе с содержимым:
        foreach ($pair_tags as $k => $v) $pair_tags[$k] = preg_quote($v, '/');
        $patterns[] = '/ <((?i:' . implode('|', $pair_tags) . '))' . $re_attrs_fast_safe . '(?<!\/)>
                         .*?
                         <\/(?i:\\1)' . $re_attrs_fast_safe . '>
                       /sxSX';
    }
    #d($patterns);

    $i   = 0; #защита от зацикливания
    $max = 99;
    while ($i < $max) {
        $s2 = preg_replace($patterns, '', $s);
        if (preg_last_error() !== PREG_NO_ERROR) {
            $i = 999;
            break;
        }

        if ($i == 0) {
            $is_html = ($s2 != $s || preg_match($re_tags, $s2));
            if (preg_last_error() !== PREG_NO_ERROR) {
                $i = 999;
                break;
            }
            if ($is_html) {
                if ($is_format_spaces) {
                    /*
                    В библиотеке PCRE для PHP \s - это любой пробельный символ, а именно класс символов [\x09\x0a\x0c\x0d\x20\xa0] или, по другому, [\t\n\f\r \xa0]
                    Если \s используется с модификатором /u, то \s трактуется как [\x09\x0a\x0c\x0d\x20]
                    Браузер не делает различия между пробельными символами, друг за другом подряд идущие символы воспринимаются как один
                    */
                    #$s2 = str_replace(array("\r", "\n", "\t"), ' ', $s2);
                    #$s2 = strtr($s2, "\x09\x0a\x0c\x0d", '    ');
                    $s2 = preg_replace('/  [\x09\x0a\x0c\x0d]++
                                         | <((?i:pre|textarea))' . $re_attrs_fast_safe . '(?<!\/)>
                                           .+?
                                           <\/(?i:\\1)' . $re_attrs_fast_safe . '>
                                           \K
                                        /sxSX', ' ', $s2);
                    if (preg_last_error() !== PREG_NO_ERROR) {
                        $i = 999;
                        break;
                    }
                }

                #массив тагов, которые не будут вырезаны
                if ($allowable_tags) $_allowable_tags = array_flip($allowable_tags);

                #парные таги, которые будут восприниматься как параграфы
                if ($para_tags) $_para_tags = array_flip($para_tags);
            }
        }
        #if

        #tags processing
        if ($is_html) {
            $_callback_type = 'strip_tags';
            $s2             = preg_replace_callback($re_tags, __FUNCTION__, $s2);
            $_callback_type = false;
            if (preg_last_error() !== PREG_NO_ERROR) {
                $i = 999;
                break;
            }
        }

        if ($s === $s2) break;
        $s = $s2;
        $i++;
    }
    #while
    if ($i >= $max) $s = strip_tags($s); #too many cycles for replace...

    if ($is_format_spaces && strlen($s) !== $length) {
        #remove a duplicate spaces
        $s = preg_replace('/\x20\x20++/sSX', ' ', trim($s));
        #remove a spaces before and after new lines
        $s = str_replace(array("\r\n\x20", "\x20\r\n"), "\r\n", $s);
        #replace 3 and more new lines to 2 new lines
        $s = preg_replace('/[\r\n]{3,}+/sSX', "\r\n\r\n", $s);
    }

    return $s;
}

?>