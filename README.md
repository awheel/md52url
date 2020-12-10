MD52URL
====

通过使用文件的 md5 来还原文件的路径, 记录时仅仅记录 md5, 相对会方便很多.

因为需要上传的不仅仅是图片, 还有其它不确定的格式, 所以会在 md5 的后面额外添加 `3` 位, 如 `001` 来记录文件的格式.

`001` 对应一个 `table` 下的索引值, 可以还原到文件的类型和后缀.

如
`1aac0bdb5aeac0bdaa420c99e4d38725001`,
还原得到的路径是:
`1a/ac/0b/1aac0bdb5aeac0bdaa420c99e4d38725001.jpg`

其中 `001` 对应 jpg.

# 安装方法:
```
composer require awheel/md52url;
```

# 使用方式:
```

use awheel\md52url;

$md2url = new md52url([
    'http://img1.example.com',
    'http://img2.example.com',
    'http://img3.example.com',
    'http://img4.example.com',
]);

$md5 = $md2url->md5(__DIR__.'/test.png');
echo $md5.PHP_EOL;

$path = $md2url->path($md5);
echo $path.PHP_EOL;

$url = $md2url->url($md5);
echo $url.PHP_EOL;


```
