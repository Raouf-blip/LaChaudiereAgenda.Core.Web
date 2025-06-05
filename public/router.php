<?php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$path = __DIR__ . $uri;

if ($uri !== '/' && file_exists($path)) {
    return false; // Sert les fichiers statiques normalement
}

require_once __DIR__ . '/index.php';
