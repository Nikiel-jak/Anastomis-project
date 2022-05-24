<?php

class Orion_ListView_SpecialColumn_MultiAction extends Orion_ListView_SpecialColumn
{
    protected $_route;
    protected $_actions;


    public function __construct($options, $actions)
    {
        $this->_actions = $actions;
        parent::__construct($options);

    }

    public function render()
    {
        $view = new Zend_View();
        $options = $this->getOptions();
        foreach($this->_actions as $key => $action){
            foreach($options as $k => $val){
                if(array_key_exists($k, $action)){
                    if(in_array($options[$k], $action[$k])){
                            return "<a class='button icon iconSave buttonLight buttonLeft' href='{$view->url($options, $action[0])}' title='{$view->translate('LISTVIEW_SPECIALCOLUMN_'.  strtoupper($key))}'><span>{$view->translate('LISTVIEW_SPECIALCOLUMN_'.  strtoupper($key))}</span></a>";
                    }
                }
            }
           
        }

    }

  
}

?>
