<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{   
    protected function _initAutoloader()
    { 
        $autoloader = new Zend_Loader_Autoloader_Resource(array(
            'namespace' => 'App',
            'basePath' => APPLICATION_PATH,
            'resourceTypes' => array(
                'model' => array(
                    'namespace' => 'Model',
                    'path' => 'models/'
                ),
                'form' => array(
                    'namespace' => 'Form',
                    'path' => 'forms/'
                ),
                'plugin' => array(
                    'namespace' => 'Plugin',
                    'path' => 'plugins/'
                ),
            )
        ));
        return $autoloader;
    }
    
    protected function _initSession()
    {
        if(!$this->hasPluginResource('Session')) {
            $this->registerPluginResource('Session');
        }
        $session = $this->getPluginResource('Session')->init();
        if (isset($_REQUEST["PHPSESSID"])) {
            Zend_Session::setId($_REQUEST["PHPSESSID"]);
        }
        Zend_Session::start();
        return $session;
    }   

    protected function _initDb()
    {
        if(!$this->hasPluginResource('Db')) {
            $this->registerPluginResource('Db');
        }
        $db = $this->getPluginResource('Db')->init();
        $db->query('SET NAMES UTF8');
        $db->setFetchMode(Zend_Db::FETCH_OBJ);

        return $db;
    }
    protected function _initConfig()
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
        Zend_Registry::set('config', $config);  
        return $config; 
    }
    
    protected function _initLanguage()
    { 
        $language = new Orion_Language();
        $language->setLanguageByUrl($_SERVER['REQUEST_URI']);
        $language->setLanguageContent($language->getPagePrefix());
        $translate = new Zend_Translate(array('adapter' => Zend_Translate::AN_CSV,'disableNotices' => true));
        $translate->addTranslation(APPLICATION_PATH . '/languages/pl/admin', Orion_Language::getPagePrefix());
        $translate->addTranslation(APPLICATION_PATH . '/languages/'.Orion_Language::getPagePrefix().'/common', Orion_Language::getPagePrefix());
        $translate->addTranslation(APPLICATION_PATH . '/languages/'.Orion_Language::getPagePrefix().'/site', Orion_Language::getPagePrefix());  
        if($translate->isAvailable(Orion_Language::getPagePrefix())) {
            $translate->setLocale(Orion_Language::getPagePrefix()); 
            Zend_Registry::set('Zend_Translate', $translate); 
        }
    }
    
    protected function _initRouting()
    {   
        $frontController = Zend_Controller_Front::getInstance();
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini');
        $router = $frontController->getRouter();
        $router->addConfig($config, 'routes');
    }
    
    protected function _initSmtp()
    {   $options = $this->getOption('smpt'); 
        $transport = new Zend_Mail_Transport_Smtp(
            $options['host'],
            $options
        );
        Zend_Mail::setDefaultTransport($transport);
        return $transport;
    }
    
    
    protected function _initVersion()
    {    
        $this->bootstrap('view');
        $view = $this->getResource('View');
        $version = $this->getOption('version');     
        $view->app_version = $version;
        return $version;
    }
    
    protected function _initView()
    {   
        if(!$this->hasPluginResource('View')) {
            $this->registerPluginResource('View');
        }
        $view = $this->getPluginResource('View')->init();
        $view->doctype('XHTML1_STRICT');
        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8');
        $view->headMeta()->appendName('copyright', '');
        $view->headMeta()->appendName('author', 'Krzysztof Nikiel');
        $view->headMeta()->appendName('editor', 'Krzysztof Nikiel');
        $view->headMeta()->appendName('design', 'Krzysztof Nikiel');
        $view->headMeta()->appendName("robots","NOINDEX, NOFOLLOW");
        return $view;
    }
    
    protected function _initLog()
    {
        if($this->hasPluginResource('log'))
        {
            $r = $this->getPluginResource('log');
            $log = $r->getLog();
            Zend_Registry::set('log', $log);
        }
    }
    
   protected function _initRedirect()
   {
           $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
           $redirector->setUseAbsoluteUri(true);
   }

}


