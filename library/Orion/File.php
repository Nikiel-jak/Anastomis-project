<?php

class Orion_File extends Orion_File_Abstract
{
    protected $_extension;

    protected $_basename;

    protected $_filename;

    protected $_tmpPath;

    protected $_type;

    protected $_key;

    protected $_uploadPath = '/upload';

    protected $_newName;

    protected $_newPath;

    protected static $_uploadTmp = '/upload/tmp';

    protected $_copy = false;

    protected $_files;

    public function __construct($path)
    {
        if(is_array($path)){
            foreach($path as $key => $p){
               $file = new Yeti_File($p);
               $this->_files[$key] = $file->copy();
            }
            return $this;
        }
        
        if(file_exists($path)) {
            $this->_tmpPath = $path;
        }elseif(file_exists(PUBLIC_PATH . self::$_uploadTmp .'/'. $path)) {
            $this->_tmpPath = PUBLIC_PATH . self::$_uploadTmp .'/'. $path;
        } elseif(file_exists($this->_tmpPath = PUBLIC_PATH . $this->_uploadPath .'/'. $path)) {
            $this->_tmpPath = PUBLIC_PATH . $this->_uploadPath .'/'. $path;
        } else {
            // nie znalazł pliku
        }


        $file_info = pathinfo($path);
        $this->_extension = $file_info['extension'];
        $this->_basename = $file_info['basename'];
        $this->_filename = $file_info['filename'];
    }
   

    protected function _preparePath()
    {
        $path = PUBLIC_PATH . $this->_uploadPath.'/'.$this->_type.'/'.$this->_key;

        if(!is_dir($path)){
            if(mkdir($path, 0777, true) === false) {
                throw new Yeti_File_Exception('Path "' . $path . '"is not writeable');
            }
        }

        if(!is_writable($path)) {
            throw new Yeti_File_Exception('Path "'.$this->_uploadPath.'"is not writeable');
        }
        
        return $path;
    }

    public function copy($type = null, $deleteTmp = true)
    {
        
        if(!$this->_tmpPath){
            $this->_copy = false;
            return $this;
        }
        // tworzenie głównego katalogu
        $this->_type = ($type) ? $type  : Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
        // suma kontrolna pliku
        
        $lognKey =  md5($this->_filename);
        // 2 elementy wycinek sumy jako podkatalog
        $this->_key = substr($lognKey,0,2);
        // nowa nazwa pliku
        $this->_newName = $lognKey . '.' . $this->_extension;
        // generowanie nowej sciezki
        $this->_newPath = $this->_preparepath();
        //kopiowanie
        if(file_exists($this->_newPath . '/' . $this->_newName )){
            do{
                $randname = (substr($lognKey,0,-2).rand(0,9).rand(0,9));
                $this->_newName = $randname. '.' .$this->_extension;
            } while(file_exists($this->_newPath . '/' . $this->_newName));
        }

        $this->_copy = copy($this->_tmpPath, $this->_newPath . '/' . $this->_newName );
        
        if($deleteTmp) {
            $this->_deleteFile($this->_tmpPath);
        }
        
        return $this;
    }

    protected function _getSize()
    {
        $data = array();
        if($this->_copy){
            $path = $this->_newPath ."/". $this->_newName;
        } else {
            $path = $this->_tmpPath;
        }
        if(file_exists($path)){
            list($width, $height, $type, $attr) = getimagesize($path);
            $data['width'] = $width;
            $data['height'] = $height;
            $data['type'] = $type;
            $data['attr'] = $attr;
        }
        return $data;
    }

    public function getWidth()
    {
        $size = $this->_getSize();
        return $size['width'];
    }

    public function getHeight()
    {
        $size = $this->_getSize();
        return $size['height'];
    }

    protected function _deleteFile($path)
    {
        if(file_exists($path)){
            unlink($path);
        }
        return true;
    }


    public function delete()
    {
         if(!$this->_tmpPath){
            return true;
        }
        return $this->_deleteFile(($this->_copy) ? $this->_newPath ."/". $this->_newName : $this->_tmpPath);
    }

    public function setUploadPath($path)
    {
        $this->_uploadPath = $path;
    }

    public function getExtension()
    {
        return $this->_extension;
    }

    public function getName()
    {
        return $this->_newName;
    }
    
    public function getPath()
    {
        return str_replace(PUBLIC_PATH . $this->_uploadPath, '', $this->_newPath).'/'.$this->_newName;
    }

    public static function getTmpDestination()
    {
        return PUBLIC_PATH . self::$_uploadTmp;
    }

    public function deleteAll()
    {
        if(is_array($this->_files) && count($this->_files)){
            foreach($this->_files as $file){
                $file->delete();
            }
        }
    }

    public function getAllPath()
    {
        $path = null;
         if(is_array($this->_files) && count($this->_files)){
            foreach($this->_files as $key => $file){
                $path[$key] = $file->getPath();
            }
        }
        return $path;
    }

}