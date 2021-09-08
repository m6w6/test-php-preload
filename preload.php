<?php

spl_autoload_register(function ($c) {
    echo "autoload($c) -> require $c.php\n";
    return require_once "$c.php";
});

$op = getenv("require") ? fn($f) => require_once $f : "opcache_compile_file";

$op("a.php");
$op("b.php");
$op("c.php");
