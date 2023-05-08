<?php

include_once "./vendor/autoload.php";

// Полезная функция
function dump( ...$arData ){
    foreach ( $arData as $data ){
        echo "<pre>";
        var_export($data);
        echo "</pre>";
    }
}

// region Установка значений переменных окружения
$arEnv = parse_ini_file(".env");
foreach ( $arEnv as $mixKey => $mixVal ){
    putenv("{$mixKey}={$mixVal}");
}
// endregion

$obMain = new \App\Main();
$obMain->init();
$obMain->start();