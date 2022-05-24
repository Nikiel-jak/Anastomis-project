<?php
class Orion_Menu extends Zend_Controller_Plugin_Abstract 
{  
    public function preDispatch(Zend_Controller_Request_Abstract $request) 
    {   
        $pageModel = new App_Model_Pages_DbTable();
        $page = $pageModel->getRootPage(Orion_Language::getPageId());
        $menu = $pageModel->getByLftRgt($page->lft, $page->rgt, null, Orion_Language::getPageId());
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
        $view->menu = $menu->toArray();
    }
}  