<?php

$DS = DIRECTORY_SEPARATOR;
define('PATH_INC',  PATH_BASE . $DS . 'include');

function PizzaClassLoader($className) {
    global $DS;
    switch($className) {
        case 'PHPMailer\PHPMailer\PHPMailer':
        case 'PHPMailer\PHPMailer\Exception':
        case 'PHPMailer\PHPMailer\OAuth':
        case 'PHPMailer\PHPMailer\SMTP':
        case 'PHPMailer\PHPMailer\POP3':
            $arr = explode('\\', $className);
            $className = $arr[2];
        case 'PHPExcel' :
            $filePath = PATH_INC . DIRECTORY_SEPARATOR . $className . '.php';
            if (file_exists($filePath)) { require_once($filePath); return; }
            break;

        case 'Firebase\\JWT\\JWT':
        case 'Firebase\JWT\JWT':
            // PageManager::debug($className, 'JWT_className');
            $filePath = PATH_VENDOR . $DS . 'firebase' . $DS . 'php-jwt' . $DS . 'src' . $DS . 'JWT.php';
            if (file_exists($filePath)) {
                require_once($filePath);
                return;
            }
            break; 
    }

    // Try by Name
    $filePath = PATH_INC . DIRECTORY_SEPARATOR . strtolower($className) . '.class.php';
    if (file_exists($filePath)) {
        require_once($filePath);
        return;
    }

    // Try SplitName by capitals
    $classParts = array('');
    $cur_part   = 0;
    $len        = strlen($className);
    for($i = 0; $i < $len; $i++) {
        $j = strlen($classParts[$cur_part]);
        if($j > 1 && ord($className[$i]) < 91) {
            $cur_part++;
            $classParts[] = '';
        }
        $classParts[$cur_part] .= $className[$i];
    }

    $fl = '';
    if(is_dir(PATH_INC . DIRECTORY_SEPARATOR . $classParts[0])) {
        $fl = array_shift($classParts) . DIRECTORY_SEPARATOR;
    }
    $filePath = PATH_INC . DIRECTORY_SEPARATOR . $fl . strtolower(implode('_', $classParts)) . '.class.php';

    if (file_exists($filePath)) {
        require_once($filePath);
    }
}


spl_autoload_register('PizzaClassLoader');
