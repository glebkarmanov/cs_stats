<?php

namespace App\Router;

// router.php
if (php_sapi_name() === 'cli-server') {
    $file = __DIR__ . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false; // отдаём реальный файл
    }
}
// иначе подключаем index.php
require __DIR__ . '/public/index.php';
