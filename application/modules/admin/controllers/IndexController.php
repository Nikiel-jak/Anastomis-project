<?php

class Admin_IndexController extends Zend_Controller_Action
{
    
    public function indexAction()
    {   
    }
    
    public function changeLanguageAction()
    {       
        $prefix = $this->_getParam('prefix');
        $language = new Orion_Language();
        $language->setLanguageContent($prefix);
        Orion_FlashMessenger::addInformation('Zmieniłeś język na: '.$prefix);
        return $this->_helper->redirector->gotoRouteAndExit(array(),'admin');
    }



   


}

