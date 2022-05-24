<?php

class Orion_Model_Row extends Zend_Db_Table_Row
{
    protected static $_helper = array();


    protected static function _getHelper($name)
    {
        if(empty($name)) {
            throw new Exception('Helper name is not specify');
        }

        if(!array_key_exists($name, self::$_helper)) {
            self::$_helper[$name]  = Zend_Controller_Action_HelperBroker::getStaticHelper($name);
        }
        
        return self::$_helper[$name]  ;
    }


}

