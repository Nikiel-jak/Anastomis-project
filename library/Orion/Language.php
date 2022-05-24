<?php

class Orion_Language {
        
    protected $_pageLanguage;
    protected $_anguage;

    public  function __construct() 
    {   
        $this->_language = new Zend_Session_Namespace('language');
        $this->_language->avaliable = $this->getAvaliableLanguage();
    }

    public function getAvaliableLanguage()
    {   
        $data = array();
        $model = new App_Model_Language_DbTable();
        if ($handle = opendir(APPLICATION_PATH.'/languages/')) 
        {
             while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $data[] = $entry;
                }
            }
            closedir($handle);
        }
        if(!count($data)){
            throw new Zend_Exception('No translation File');
        }
        foreach($model->getAvaliable() as $lang){
            if(in_array($lang->prefix, $data))
            {
                $avaliable[$lang->prefix] = array(
                    'name' => $lang->name,
                    'prefix' => $lang->prefix,
                    'id' => $lang->id
                );
            }
        }   
        return $avaliable;
    }
    
    public function getDefaultLanguage()
    {
        $model = new App_Model_Language_DbTable();
        $row = $model->getDefault();
        return $data = array(
            'name' => $row->name,
            'prefix' => $row->prefix,
            'id' => $row->id
        );
    }
    
    public function setLanguageByUrl($url)
    {
        $data = explode('/',$url);
        return $this->setLanguageByPrefix($data[2]);
    }
    public function setLanguageByPrefix($lang)
    {   
        if(array_key_exists($lang, $this->_language->avaliable)){
            return $this->_language->page = $this->_language->avaliable[$lang];
        }
        return $this->_language->page = $this->getDefaultLanguage();
    } 
    
    public function setLanguageByBrowser($_server)
    {   
        $lang = substr($_server, 0, 2);
        if(array_key_exists($lang, $this->_language->avaliable)){
            return $this->_language->page = $this->_language->avaliable[$lang];
        }
        return $this->_language->page = $this->getDefaultLanguage();
    } 
    
    public static function getPagePrefix()
    {
       $content = new Zend_Session_Namespace('language');
       return $content->page['prefix'];
    }
    
    public static function getPageId()
    {
        $content = new Zend_Session_Namespace('language');
        return $content->page['id'];
    }
    
    public static function getContentPrefix()
    {
        $content = new Zend_Session_Namespace('language');
        return $content->content['prefix'];
    }
    
    public static function getContentId()
    {
        $content = new Zend_Session_Namespace('language');
        return $content->content['id'];
    }
    
    public static function getLanguages()
    {
        $content = new Zend_Session_Namespace('language');
        return $content->avaliable;
    }
    
    public function setLanguageContent($lang)
    {   
        if(array_key_exists($lang, $this->_language->avaliable)){
            
           $this->_language->content = $this->_language->avaliable[$lang];
        }
    }
    
    public function getCsvTranslate($lang, $module = 'site')
    {
        $location = APPLICATION_PATH.'/languages/'.$lang.'/'.$module.'/';     
        if($dir = opendir($location)){
            while($file = readdir($dir)){
                $data = explode('.',$file);
                if(is_file($location.$file) && $data[1] == 'csv'){
                    $csv = fopen($location.$file,'r');
                    while($row = fgetcsv($csv,100000,';')){
                        $file_csv[] = $row;
                    }
                }
            }
        }
        return $file_csv;
    }
    
    public function setCsvTranslate($data, $lang, $file_csv ='site.csv', $module = 'site')
    {   
        $location = APPLICATION_PATH.'/languages/'.$lang.'/'.$module.'/';     
        if($dir = opendir($location)){
            while($file = readdir($dir)){
                if(is_file($location.$file)){
                    unlink($location.$file);
                }
            }
        }
        $location = APPLICATION_PATH.'/languages/'.$lang.'/'.$module.'/'.$file_csv;  
        $fp = fopen($location, 'w');
        foreach ($data as $fields) {
            fputcsv($fp, $fields, ';','"');
        }   
        fclose($fp);
    }
}