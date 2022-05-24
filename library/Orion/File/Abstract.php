<?php
abstract class Orion_File_Abstract implements Orion_File_Interface{

    protected $_extension;

    protected $_basename;

    protected $_filename;

    protected $_tmpPath;

    protected $_type;

    protected $_key;

    protected $_uploadPath = '/uploads';

    protected $_newName;

    protected $_newPath;

    protected static $_uploadTmp = '/uploads/tmp';

    public function __construct($path)
    {
        if(file_exists($path)){
            $this->_tmpPath = $path;
        } elseif(file_exists(PUBLIC_PATH . $path)) {
            $this->_tmpPath = PUBLIC_PATH . $path;
        } elseif(file_exists(PUBLIC_PATH . self::$_uploadTmp .'/'. $path)) {
            $this->_tmpPath = PUBLIC_PATH . self::$_uploadTmp .'/'. $path;
        } else {
            throw new Yeti_File_Exception('File is not exist');
        }
        $file_info = pathinfo($path);
        $this->_extension = $file_info['extension'];
        $this->_basename = $file_info['basename'];
        $this->_filename = $file_info['filename'];
    }

    protected function _preparePath()
    {

        if(!is_writable(PUBLIC_PATH . $this->_uploadPath)){
            throw new Yeti_File_Exception('Path "'.$this->_uploadPath.'"is not writeable');
        }
        if(!is_dir(PUBLIC_PATH . $this->_uploadPath.'/'.$this->_type)){
            mkdir(PUBLIC_PATH . $this->_uploadPath.'/'.$this->_type,0777);
        }
        if(!is_dir(PUBLIC_PATH . $this->_uploadPath.'/'.$this->_type.'/'.$this->_key)){
            mkdir(PUBLIC_PATH . $this->_uploadPath.'/'.$this->_type.'/'.$this->_key,0777);
        }
        return PUBLIC_PATH . $this->_uploadPath.'/'.$this->_type.'/'.$this->_key;
    }

    public function copy($type = null, $path = null)
    {
        $this->_uploadPath = ($path) ? $path : $this->_uploadPath;
        // tworzenie głównego katalogu
        $this->_type = ($type) ? $type  : Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
        // suma kontrolna pliku
        $lognKey =  md5_file($this->_tmpPath);
        // 2 elementy wycinek sumy jako podkatalog
        $this->_key = substr($lognKey,0,2);
        // nowa nazwa pliku
        $this->_newName = $lognKey. '.' .$this->_extension;
        // generowanie nowej sciezki
        $this->_newPath = $this->_preparepath();
        //kopiowanie
        copy($this->_tmpPath, $this->_newPath ."/". $this->_newName );
        //usuwanie
        $this->_deleteFile($this->_tmpPath);
        
    }

    protected function _deleteFile($path)
    {
        unlink($path);
        if(!file_exists($path)){
            return true;
        }
        return false;
    }


    public function delete()
    {
        return $this->_deleteFile($this->_tmpPath);
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
        return $this->_uploadPath . str_replace(PUBLIC_PATH . $this->_uploadPath, '', $this->_newPath).'/'.$this->_newName;
    }

    public static function getTmpDestination()
    {
        return PUBLIC_PATH . self::$_uploadTmp;
    }

}