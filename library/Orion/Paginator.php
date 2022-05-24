<?php

class Orion_Paginator extends Zend_Paginator
{   

    public static function initialize($select)
    {   
        $view = Zend_Layout::getMvcInstance()->getView();
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $name = Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName();
        $routing = new Zend_Session_Namespace('routing');
        $routing->name = $name;
        $config = new Zend_Session_Namespace($name);
        
        $json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
        if($request->isXmlHttpRequest()){
            $config->itemperpage = $request->getParam('itemperpage');
            $json->sendJson('ok', array('enableJsonExprFinder' => true));
        }
        if (!$config->itemperpage) {
            $config->itemperpage = 10; 
        }
        $page = $request->getParam('page');
        $paginator = parent::factory($select);
        $paginator->setItemCountPerPage($config->itemperpage);
        $paginator->setCurrentPageNumber($page);
        $view->paginator= $paginator;

    }
    
}