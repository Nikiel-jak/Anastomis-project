<?php

class Orion_Log
{
    protected $_logger;
    
    static $fileLogger = null;
    
    public static function getInstance()
    {
        if (self::$fileLogger === null)
        {
            self::$fileLogger = new self();
        }
        return self::$fileLogger;
    }
    
    protected function __construct()
    {
        $this->_logger = Zend_Registry::get('log');
    }
    
    public static function shoutcast($message)
    {
        $filter = new Zend_Log_Filter_Priority(Zend_Log::ERR);
        self::getInstance()->getLog()->addFilter($filter);  
        $writer = new Zend_Log_Writer_Stream(ROOT_PATH.'/var/log/shoutcast/shoutcast_error.log', 'a+');  
        self::getInstance()->getLog()->addWriter($writer);
        self::getInstance()->getLog()->err($message);   
    }
    
    public static function info($message)
    {   
        $filter = new Zend_Log_Filter_Priority(Zend_Log::INFO);
        self::getInstance()->getLog()->addFilter($filter);
        self::getInstance()->getLog()->info($message);
    }
    
    public static function error($message)
    {   
        $filter = new Zend_Log_Filter_Priority(Zend_Log::ERR);
        self::getInstance()->getLog()->addFilter($filter);
        $type = (get_class($message));
        switch ($type)
        {
            case 'Zend_Db_Statement_Exception':
            $writer = new Zend_Log_Writer_Stream(ROOT_PATH.'/var/log/db/db_error.log', 'a+');
            break;
            case 'Zend_Auth_Adapter_Exception':
            $writer = new Zend_Log_Writer_Stream(ROOT_PATH.'/var/log/login/login_error.log', 'a+');
            break;
            default:
            $writer = new Zend_Log_Writer_Stream(ROOT_PATH.'/var/log/error.log', 'a+');
            break;
        }
        
        self::getInstance()->getLog()->addWriter($writer);
        self::getInstance()->getLog()->err($message);   
    }
    
    public function getLog()
    {
        return $this->_logger;
    }    
}   