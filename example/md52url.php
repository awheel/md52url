<?php

require __DIR__.'/../src/md52url.php';

use awheel\md52url\md52url;

$md2url = new md52url(array(
    'http://img1.example.com',
    'http://img2.example.com',
    'http://img3.example.com',
    'http://img4.example.com',
));

$md5 = $md2url->md5(__DIR__.'/test.txt', __DIR__.'/test.json');
echo $md5.PHP_EOL;

$path = $md2url->path($md5);
var_dump($path);
echo $path.PHP_EOL;

$url = $md2url->url($md5);
echo $url.PHP_EOL;
