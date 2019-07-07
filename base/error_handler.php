<?php
function myErrorHandler($errno, $errstr, $errfile, $errline)
{
    $out = 'UNK';

    if($errno     & 0x1155) $out = 'FATAL';
    elseif($errno & 0x02A2) $out = 'WARN';
    elseif($errno & 0x0408) $out = 'NOTICE';
    elseif($errno & 0x0800) $out = 'STRICT';
    elseif($errno & 0x6000) $out = 'DEPRECATED';

    $out = "$out($errno):$errstr [$errfile line $errline]";

    PageManager::$dbg[] = $out;
    /* Не запускаем внутренний обработчик ошибок PHP */
    return true;
}
