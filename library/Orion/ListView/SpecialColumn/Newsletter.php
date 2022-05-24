<?php

class Orion_ListView_SpecialColumn_Newsletter extends Orion_ListView_SpecialColumn
{
    protected $_route;
    protected $_text;


    public function __construct($options, $route, $text = null)
    {
        parent::__construct($options);
        $this->_route = $route;
        if($text){
            $this->_text = $text;
        }
    }

    public function render()
    {
        $options = $this->getOptions();

        $text = ($this->_text) ? $this->_text : 'LISTVIEW_SPECIALCOLUMN_EDIT';

        $view = new Zend_View();
        if($options['status'] != 7){
            return "<a class='btn' href='{$view->url($options, $this->_route)}' title='{$view->translate($text)}'><span>{$view->translate($text)}</span></a>";
        } else {
            return "----";
        }
    }
}

?>
