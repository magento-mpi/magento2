<?php

// no files specified return 404
if (empty($_GET['f'])) {
    header('404 Not found');
    echo "SYNTAX: proxy.php/x.js?f=file1.js,file2.js&c=0&d=0&e=86400";
    exit;
}

// get files content
$files = explode(',', $_GET['f']);
$out = '';
foreach ($files as $f) {
    $p = str_replace('/', DIRECTORY_SEPARATOR, $f);

    // check for security
    if ($p[0]==DIRECTORY_SEPARATOR || strpos($p, '..')!==false || !file_exists($p)) {
        continue;
    }
    $out .= file_get_contents($p)."\r\n";
}

// remove spaces
if (!(isset($_GET['c']) && !$_GET['c'])) {
    $out = preg_replace('#[ \t]+#', ' ', $out);
}

// use gzip or deflate, use if not specified in .htaccess
if (!(isset($_GET['d']) && !$_GET['d'])) {
    ini_set('zlib.output_compression', 1);
}

// add Expires header
$time = time()+(isset($_GET['e']) ? $_GET['e'] : 365)*86400;
header('Expires: '.gmdate('r', $time));

echo $out;
