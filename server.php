<?php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve static files natively if they exist (return false = let PHP handle it)
$publicPath = __DIR__.'/public'.$uri;
if ($uri !== '/' && file_exists($publicPath) && !is_dir($publicPath)) {
    return false;
}

// Fallback to Laravel
require __DIR__.'/public/index.php';
