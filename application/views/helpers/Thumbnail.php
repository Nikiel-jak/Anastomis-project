<?php

class View_Helper_Thumbnail extends Zend_View_Helper_Abstract
{
    protected $_regKey = 'Helper_Thumbnail';

    public function thumbnail($path, $type = null)
    {
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $uploadsPath = $baseUrl . '/uploads';
        $thumbsPath = $baseUrl . '/thumbs';
        $path = str_replace($uploadsPath , $thumbsPath , $path);

        if ($type != null)
            $path = preg_replace('/\.([a-z]+)$/i' , "-t{$type}.$1" , $path);
        return $path;
    }
}

