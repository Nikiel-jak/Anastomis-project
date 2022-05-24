<?php

class Admin_ConfigurationController extends Zend_Controller_Action
{
    public function indexAction()
    {
        
    }
    
    public function globalAction()
    {
        $configurationModel = new App_Model_Configuration_DbTable();
        $form = new App_Form_Admin_Configuration(array('logo' => $configurationModel->getValue('site_logo')));
        $request = $this->getRequest();
        if($request->isPost() && $form->isValid($request->getPost())){
            $data = $form->getValues();
            $adapter = $configurationModel->getAdapter();
            $adapter->beginTransaction();
            try{
                foreach($data as $key => $config){
                    $where = $adapter->quoteInto('name = ?',$key);
                    $configurationModel->update(array('value' => $config), $where);
                }
                $adapter->commit();
                Orion_FlashMessenger::addSuccess('ADMIN_CONFIGURATION_GLOBAL_SAVE_SUCCESS');
            } catch (exception $e){
                Orion_Log::error($e);
                Orion_FlashMessenger::addError('COOMON_SYSTEM_ERROR');                
                $adapter->rollBack();
            }
            return $this->_helper->redirector->gotoRouteAndExit(array(),'admin-configuration-global');
        } else {
            $form->populate($configurationModel->getDataForm());
        }
        $this->view->form = $form;
    }
    
    public function smtpAction()
    {
        $form = new App_Form_Admin_Smtp();
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', null, array('skipExtends' => true,'allowModifications'=>true));
        $request = $this->getRequest();
        if($request->isXmlHttpRequest()){
            if(!$form->isValidPartial($request->getPost())){
                    $errorsMessages = $form->getMessages();
                    $this->_helper->json($errorsMessages, array('enableJsonExprFinder' => true));   
            } else {          
                $this->_helper->json('ok', array('enableJsonExprFinder' => true)); 
            }
        }
        if($request->isPost() && $form->isValid($request->getPost())){
            $data = $form->getValues(); 
            unset($data['navigation']);
            $config->production->smpt = $data;
           
            $writer = new Zend_Config_Writer_Ini(array('config'   => $config,
                                                       'filename' => APPLICATION_PATH . '/configs/application.ini'));
            try{  
                $writer->write();
                Orion_FlashMessenger::addSuccess('ADMIN_CONFIGURATION_SMTP_SAVE_SUCCESS');
            } catch (exception $e){
                Orion_Log::error($e);
                Orion_FlashMessenger::addError('COOMON_SYSTEM_ERROR');                 
            }
            return $this->_helper->redirector->gotoRouteAndExit(array(),'admin-configuration-smtp');
        } else {
            $form->populate($config->production->smpt->toArray()); 
        }
        $this->view->form = $form;
    }
    
    public function shoutcastAction()
    {
        $form = new App_Form_Admin_Shoutcast();
        $request = $this->getRequest();
        $configurationModel = new App_Model_Configuration_DbTable();
        if($request->isXmlHttpRequest()){
            if(!$form->isValidPartial($request->getPost())){
                    $errorsMessages = $form->getMessages();
                    $this->_helper->json($errorsMessages, array('enableJsonExprFinder' => true));   
            } else {          
                $this->_helper->json('ok', array('enableJsonExprFinder' => true)); 
            }
        }
        if($request->isPost() && $form->isValid($request->getPost())){
            $data = $form->getValues();
            $adapter = $configurationModel->getAdapter();
            $adapter->beginTransaction();
            try{
                foreach($data as $key => $config){
                    $where = $adapter->quoteInto('name = ?',$key);
                    $configurationModel->update(array('value' => $config), $where);
                }
                $adapter->commit();
                Orion_FlashMessenger::addSuccess('ADMIN_CONFIGURATION_GLOBAL_SAVE_SUCCESS');
            } catch (exception $e){
                Orion_Log::error($e);
                Orion_FlashMessenger::addError('COOMON_SYSTEM_ERROR');                
                $adapter->rollBack();
            }   
        } else {
            $form->populate($configurationModel->getDataForm());
        }
        $this->view->form = $form;
    }
    
    public function siteMapAction()
    {   
       
        $xml = new DOMDocument('1.0', 'utf-8');
        $xml->formatOutput = true;
        $string = file_get_contents(APPLICATION_PATH.'/configs/routes.ini');
        $array = (explode('.',preg_replace('/ = /','',$string)));
        $fl_array = preg_grep('/route"/', $array);
        foreach($fl_array as $val){
            $val = trim(preg_replace(array('"routes"','/"/','/route/'), array('','',''),$val));
            if(strstr($val,'admin') === FALSE ){
                if(strstr($val,':') === FALSE){
                    if(strstr($val,'potwierdzenie') === FALSE){
                        $data[] = 'http://radyjko.net/'.$val;
                    }
                }
            }
        }
          //  $xml = '<urlset>';
        $root = $xml->appendChild($xml->createElementNS('http://www.sitemaps.org/schemas/sitemap/0.9', 'urlset'));
        foreach($data as $d){
            
           $item = $xml->createElement('url');
           $root->appendChild($item);
           $url = $xml->createElement('loc', $d);
           $item->appendChild($url);
           $date = $xml->createElement('lastmod', date('Y-m-d'));
           $item->appendChild($date);
           $rob = $xml->createElement('changefreq', 'weekly');
           $item->appendChild($rob);
           $prio = $xml->createElement('priority','0.9');
           $item->appendChild($prio);
         }
        $shoutCastModel = new App_Model_Shoutcast_DbTable();
        $shoutcast = $shoutCastModel->selectAllShputcast();
        foreach ($shoutcast as $s){
           $item = $xml->createElement('url');
           $root->appendChild($item);
           $url = $xml->createElement('loc', 'http://radyjko.net/radio/'.$s['url']);
           $item->appendChild($url);
           $date = $xml->createElement('lastmod', date('Y-m-d'));
           $item->appendChild($date);
           $rob = $xml->createElement('changefreq', 'weekly');
           $item->appendChild($rob);
           $prio = $xml->createElement('priority','0.9');
           $item->appendChild($prio);  
        }
        
        $output = $xml->saveXML();
        $path = PUBLIC_PATH;
        $file = "/sitemap.xml"; 
        if(!is_writeable($path)){
            die('zmien prawa na katalog public');
        }
        if(file_exists($path.$file)){
            unlink($path.$file);
        }
        try{
            $fp = fopen($path.$file, "a");
            fwrite($fp, $output);  
            fclose($fp); 
            Orion_FlashMessenger::addSuccess('Mapa została wygenerowana pomyślnie'); 
        } catch (exception $e){
            Orion_Log::error($e);
            Orion_FlashMessenger::addError('COOMON_SYSTEM_ERROR'); 
        } 
        return $this->_helper->redirector->gotoRouteAndExit(array(),'admin');
    }
}