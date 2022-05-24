<?php

class Orion_ListView_SpecialColumn_View extends Orion_ListView_SpecialColumn
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
        return "<a class='button icon iconSave buttonLight buttonLeft' href='{$view->url($options, $this->_route)}' title='{$view->translate('LISTVIEW_SPECIALCOLUMN_VIEW')}'><span>{$view->translate('LISTVIEW_SPECIALCOLUMN_VIEW')}</span></a>";
    }
}

?>
