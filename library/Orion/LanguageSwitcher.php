<?php

class Orion_LanguageSwitcher extends Zend_Controller_Plugin_Abstract{
        
    protected static $_lang;
    
    public function setLang($lang)
    {
        self::$_lang = $lang;
    }
    public function getLang()
    {
        return self::$_lang;
    }
    public function preDispatch (Zend_Controller_Request_Abstract $request)
    {
        $module = $request->getModuleName();

        $lang = $request->getParam('lang','pl');
        $this->setLang($lang);
        $this->setGlobalRoutingParam($this->getLang());
        
        $locale = new Zend_Locale();
        $locale->setLocale($this->getLang()); 
        Zend_Registry::set('Zend_Locale', $locale);
        
        
        $translate = new Zend_Translate(Zend_Translate::AN_CSV, APPLICATION_PATH . '/languages/'.$this->getLang().'/admin',$this->getLang());
        $translate->addTranslation(APPLICATION_PATH . '/languages/'.$this->getLang().'/common', $this->getLang());
        $translate->addTranslation(APPLICATION_PATH . '/languages/'.$this->getLang().'/site', $this->getLang());  
        if ($translate->isAvailable($locale->getLanguage())) {
            $translate->setLocale($locale->getLanguage()); 
            Zend_Registry::set('Zend_Translate', $translate);       
        }     
    }
    
    public function setGlobalRoutingParam($param)
    {
        $frontController = Zend_Controller_Front::getInstance();
        $router = $frontController->getRouter();
        $router->setGlobalParam('lang',$param);  
    }
    
    public static function getlangtranslate()
    {
        return 'pl';
    }
    
    
 

}