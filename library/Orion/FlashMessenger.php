<?php

class Orion_FlashMessenger extends Zend_Controller_Action_Helper_Abstract
{
	const ERROR = 0;
	const SUCCESS = 1;
	const INFORMATION = 2;
	
	public static function addMessage($message,$type,$options = array()) 
	{    
		$translate = Zend_Registry::get('Zend_Translate');
		$message = (count($options)) ? vsprintf($translate->_($message),$options) : $translate->_($message);
		$flashMessanger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
		$flashMessanger->addMessage(array($message,$type));
  
	}
	
	public static function addInformation()
	{
	    $args = func_get_args();
	    $message = array_shift($args);
	    self::addMessage($message,self::INFORMATION,$args);
	}
	
	public static function addError()
	{
	    $args = func_get_args();
	    $message = array_shift($args);
	    self::addMessage($message,self::ERROR,$args);
	}


	public static function addSuccess()
	{
	    $args = func_get_args();
	    $message = array_shift($args);
	    self::addMessage($message,self::SUCCESS,$args);
	}
}