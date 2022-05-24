<?php

class View_Helper_ImageUpload extends Zend_View_Helper_Abstract
{
    protected $_regKey = 'Helper_ImageUpload';

    protected $_path;

    public function imageUpload($fileName, $options = array())
    {
        if(!array_key_exists('resize', $options)){
            $path = array_key_exists('path', $options) ? $options['path']: '/upload/';
        } else {
            $path = array_key_exists('path', $options) ? $options['path']: '/thumbs/';
        }
        $type = array_key_exists('resize', $options) ? $options['resize']: '';

        if(empty($fileName)){
           return $this->replacement($type);
        }

        if(!$this->checkFilePath($fileName, $path)){
            return $this->replacement($type);
        }
        if(!array_key_exists('resize', $options)){
            return $this->view->baseUrl() . $path . $fileName;
        }
        return $this->view->thumbnail($this->view->baseUrl(). $path . $fileName, $type);
    }

    protected function replacement($type)
    {
           switch($type){
                default:
                    return $this->view->imagePath('layout/no_photo141_90.jpg',false);
                break;
            }
    }

    protected function checkFilePath($fileName, $path = null)
    {
        return $path . $fileName;
        if(!$this->view->fileExists($path . $fileName)){
            return false;
        }
        return $this->_path = $path;
    }
}