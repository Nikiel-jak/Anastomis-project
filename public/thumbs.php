<?php
$publicPath = dirname(__FILE__);
$baseUrl    = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl    = $baseUrl == '/' ? '' : $baseUrl;

$type = 'default'; $w = $h = null;
$fileDest = $file = str_replace(array($baseUrl . '/thumbs/','/thumbs/'), '', urldecode(array_shift(explode('?',$_SERVER['REQUEST_URI']))));
$matches = array();
if (preg_match('/(.*)-s(\d+)x(\d+)(\.[a-z]+)$/i', $file, $matches)) {
    $file = $matches[1].$matches[4];
    $type = 'custom';
    $w = $matches[2];
    $h = $matches[3];
} else if (preg_match('/(.*)-t([a-z_]+)(\.[a-z]+)$/i', $file, $matches)) {
    $file = $matches[1].$matches[3];
    $type = $matches[2];
}
$source = "{$publicPath}/upload/{$file}";
$destination = "{$publicPath}/thumbs/{$fileDest}";
if (!is_readable($source)) {
    @header("HTTP/1.0 404 Not Found");
    echo '404 Not Found';
    die();
}
if (!file_exists(dirname($destination))) {
    @mkdir(dirname($destination), 0777, true); //rekurencyjnie
}
if (!is_writable(dirname($destination))) {
    @header("HTTP/1.0 403 Forbidden");
    echo '403 Forbidden';
    die();
}
include '../library/Orion/ResizePhoto.php';
$resize = new Orion_ResizePhoto($source);
switch ($type) {
    case 'small_bw':
        $resize->size_width(200);
        $resize->setBalckAndWhite(true);
        $resize->save($destination);
        list($width, $height) = getimagesize($destination);
        if($height > 140){
           $resize2 = new Orion_ResizePhoto($destination);
           $resize2->size_height(140);
           $resize2->save($destination);
        }
        unset($resize);
        break;
    case 'small_p':
        $resize->size_width(200);
        $resize->save($destination);
        list($width, $height) = getimagesize($destination);
        if($height > 140){
           $resize2 = new Orion_ResizePhoto($destination);
           $resize2->size_height(140);
           $resize2->save($destination);
        }
        unset($resize);
        break;
    case 'small':
        $resize->autoSize(120,80);
        $resize->save($destination);
        unset($resize);
        break;
    case 'small_c':
        $resize->size_width(200);
        $resize->save($destination);
        list($width, $height) = getimagesize($destination);
        if($height > 140){
           $resize2 = new Orion_ResizePhoto($destination);
           $resize2->size_height(140);
           $resize2->save($destination);
        }
        unset($resize);
    break;
    case 'gallery_page':
        $resize->autoSize(140,100);
        $resize->save($destination);
        unset($resize);
    case 'banners':
        $resize->cut(319,114);
        $resize->save($destination);
        unset($resize);
    case 'gallery_page_cut_new':
        $resize->cut(138, 100);
        $resize->save($destination);
        unset($resize);
    case 'news_list':
        $resize->autoSize(142,102);
        $resize->save($destination);
        unset($resize);
        break;
    case 'news_home':
        $resize->cut(142,102);
        $resize->save($destination);
        unset($resize);
        break;
    case 'gallery_page':
        $resize->autoSize(140,100);
        $resize->save($destination);
        unset($resize);
        break;
    case 'lightbox':
        $resize->size_width(650);
        $resize->save($destination);
        unset($resize);
        list($width, $height, $type, $attr) = getimagesize($destination);
        if($height > 400) {
            $resize = new Yeti_ResizePhoto($destination);
            $resize->size_height(400);
            $resize->save($destination);
            unset($resize);
        }
        break;
    case 'slider_list':
        $resize->size_width(138);
        $resize->size_height(198);
        $resize->save($destination);
        unset($resize);
        list($width, $height, $type, $attr) = getimagesize($destination);
        break;
    case 'slider_one':
        $resize->size_width(255);
        $resize->size_height(370);
        $resize->save($destination);
        unset($resize);
        list($width, $height, $type, $attr) = getimagesize($destination);
        break;  
    case 'home_slider':
        $resize->autoSize(932,374);
        $resize->save($destination);
        unset($resize);
        break;
    default:
        header("HTTP/1.0 404 Not Found");
        echo '404 Not Found';
        die();
}



header('Content-Type: '.r19mime($destination));
header('Content-Length: '.filesize($destination));
readfile($destination);
die();
function r19mime($x) {
    if (defined(FILEINFO_MIME_TYPE)&&($t=new finfo(FILEINFO_MIME_TYPE))) $m=$t->file($url);
    else if (function_exists('mime_content_type')) $m=mime_content_type($url);
    if (!$m) {
        $mime_types=array(
        'txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'swf' => 'application/x-shockwave-flash',
        'flv' => 'video/x-flv',
        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'cab' => 'application/vnd.ms-cab-compressed',
        // audio/video
        'mp3' => 'audio/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',
        // adobe
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',
        // ms office
        'doc' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        // open office
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );
        $ext=strtolower(array_pop(explode('.',$url)));
        if (array_key_exists($ext,$mime_types)) $m=$mime_types[$ext];
        else $m='application/octet-stream';
    } return $m;
}
