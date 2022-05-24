<?php

class Orion_ListView_SpecialColumn_Confirm extends Orion_ListView_SpecialColumn
{
    protected $_route;
    
    public function __construct($options, $route)
    {
        parent::__construct($options);
        $this->_route = $route;      
    }

    public function render()
    {
        $options = $this->getOptions();

        $view = new Zend_View();
        if(!empty($options['id'])){
            return "<a class='btn btn-success' href='{$view->url($options, $this->_route)}' title='{$view->translate('LISTVIEW_SPECIALCOLUMN_CONFRIM')}'><span>{$view->translate('LISTVIEW_SPECIALCOLUMN_CONFRIM')}</span></a>";
        } else {
            return '------------';
        }
        
    }
}

?>
