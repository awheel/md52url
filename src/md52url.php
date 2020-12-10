<?php

namespace awheel\md52url;

use finfo;

class md52url
{
    /**
     * 静态资源域名列表, 随机使用一个
     *
     * @var array
     */
    protected $domains = array();

    /**
     * path 前缀
     *
     * @var string
     */
    protected $prefix = '';

    /**
     * 文件类型, 能增删, 不能修改, 近义的请使用同一个, 如 jpeg-jpg, png=ms.png
     *
     * @var array
     */
    protected $fileTypes = array(
        '001' => 'jpg',
        '002' => 'png',
        '003' => 'gif',
        '004' => 'bmp',
        '005' => 'psd',
        '006' => 'zip',
        '007' => 'rar',
        '008' => '7z',
        '009' => 'tar',
        '010' => 'txt',
        '011' => 'pdf',
        '012' => 'csv',
        '013' => 'doc',
        '014' => 'docx',
        '015' => 'ppt',
        '016' => 'pptx',
        '017' => 'xls',
        '018' => 'xlsx',
        '019' => 'json',
        '020' => 'md',
        '021' => 'swf',
        '022' => 'apk',
        '023' => 'jar',
        '024' => 'mp4',
        '025' => 'avi',
        '026' => 'wmv',
        '027' => 'asf',
        '028' => 'eml',
    );

    /**
     * 构造函数
     *
     * @param array $domains
     * @param string $prefix
     *
     * @return self
     */
    public function __construct(array $domains = array(),  $prefix = null)
    {
        $this->domains = $domains;
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * 通过 md5 获取 完整 url
     *
     * @param $md5
     *
     * @return string
     */
    public function url($md5)
    {
        $path = $this->path($md5);
        if (!$path) return '';

        return rtrim($this->domains[array_rand($this->domains)], '/') . $path;
    }

    /**
     * 通过 md5 获取 完整路径
     *
     * @param $md5
     *
     * @return string
     */
    public function path($md5)
    {
        if (strlen($md5) != 35) return '';

        $postfix = substr($md5, -3, 3);
        $postfix = isset($this->fileTypes[$postfix]) ? '.'.$this->fileTypes[$postfix] : '';

        return sprintf("%s/%s/%s/%s/%s%s",
            $this->prefix ? '/'.trim($this->prefix, '/') : '',
            substr($md5, 0, 2),
            substr($md5, 2, 2),
            substr($md5, 4, 2),
            $md5,
            $postfix
        );
    }

    /**
     * 获取文件 md5
     *
     * @param mixed $filename
     * @param string $originName 原始文件名, 可选，用于辅助识别文件类型
     *
     * @return string
     */
    public function md5($filename, $originName = null)
    {
        if (empty($filename) || !is_file($filename)) return '';

        $md5 = md5_file($filename);
        $exploded = explode('.', $originName ? $originName : $filename);
        $postfix = end($exploded);

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = explode('/', $finfo->file($filename));
        switch ($mimeType[0]) {
            // 图片
            case 'image':
                switch ($mimeType[1]) {
                    case 'jpeg':
                        // no break
                    case 'jpg':
                        // no break;
                        $fileType = 'jpg';
                        break;

                    default:
                        $fileType = $mimeType[1];
                        break;
                }
                break;

            // 纯文本
            case 'text':
                $fileType = $postfix;
                break;

            // 软件文件
            case 'application':
                switch ($mimeType[1]) {
                    case 'zip':
                        // no break
                    case 'pdf':
                        // no break
                        $fileType = $mimeType[1];
                        break;

                    case 'x-tar':
                        $fileType = 'tar';
                        break;

                    case 'x-gzip':
                        $fileType = 'tgz';
                        break;

                    case 'x-xz':
                        $fileType = 'xz';
                        break;

                    case 'x-rar':
                        $fileType = 'rar';
                        break;

                    case 'x-7z-compressed':
                        $fileType = '7z';
                        break;

                    case 'msword':
                        $fileType = 'doc';
                        break;

                    case 'vnd.ms-powerpoint':
                        $fileType = 'ppt';
                        break;

                    case 'vnd.ms-excel':
                        $fileType = 'xls';
                        break;

                    case 'vnd.openxmlformats-officedocument.wordprocessingml.document':
                        $fileType = 'docx';
                        break;

                    case 'vnd.openxmlformats-officedocument.presentationml.presentation':
                        $fileType = 'pptx';
                        break;

                    case 'vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                        $fileType = 'xlsx';
                        break;

                    case 'x-shockwave-flash':
                        $fileType = 'swf';
                        break;

                    // todo jar 和 apk 会识别成一样的, 需要解包后根据文件内容判别
                    case 'java-archive':
                        $fileType = 'apk';
                        break;

                    default:
                        $fileType = $mimeType[1];
                        break;
                }
                break;

            // 视频
            case 'video':
                switch ($mimeType[1]) {
                    case 'x-msvideo':
                        $fileType = 'avi';
                        break;

                    case 'x-ms-wmv':
                    case $mimeType[1] == 'x-ms-asf' && $postfix == 'wmv':
                        $fileType = 'wmv';
                        break;

                    case 'x-ms-asf':
                        $fileType = 'asf';
                        break;

                    default:
                        $fileType = $mimeType[1];
                        break;
                }
                break;

            default:
                $fileType = $postfix;
                break;
        }

        $fileTypes = array_flip($this->fileTypes);
        $fileTypeKey = isset($fileTypes[$fileType]) ? $fileTypes[$fileType] : '';

        return $md5.$fileTypeKey;
    }
}
