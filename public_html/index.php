<?php
    //session_start();
    $DS = DIRECTORY_SEPARATOR;
    define('PATH_ROOT', dirname(__FILE__));
    define('PATH_BASE', dirname(dirname(__FILE__)) . $DS . 'base');
    define('PATH_VENDOR', dirname(dirname(__FILE__)) . $DS . 'vendor');
    define('PATH_TEXT', dirname(dirname(__FILE__)) . $DS . 'gettext' . $DS . 'i18n');

    // require_once PATH_BASE . $DS . 'error_handler.php';
    // set_error_handler('myErrorHandler');

    $infoPrefix = '';

    function InfoPrefix($txt) {
        global $infoPrefix;
        $t = $txt;
        if(strpos($txt, '.') !== FALSE) {
            $t = basename($txt, '.php');
        }
        $infoPrefix = "{$t}: ";
    }

    function Info($txt, $suffix = PHP_EOL) {
        global $infoPrefix;
        $out = $infoPrefix . $txt;
        syslog(LOG_WARNING, $out);
        echo $out . $suffix;
    }

    require_once PATH_BASE . $DS . 'config.php';
    require_once PATH_BASE . $DS . 'autoload.php';
    require_once PATH_VENDOR . $DS . 'autoload.php';

    $DB = null;
    $url = isset($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL'] : '';

    $pm = new PageManager(filter_var($url, FILTER_SANITIZE_SPECIAL_CHARS));

    $pm->output();
