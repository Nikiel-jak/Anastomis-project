<?php

class Orion_ListView_SpecialColumn_MultiBoolean extends Orion_ListView_SpecialColumn
{
    protected $_list;
    
    public function __construct($options, $list)
    {
        parent::__construct($options);
        $this->_list = $list;
    }

    public function render()
    {
        $options = $this->getOptions();
        $view = new Zend_View();
        
        $result = '';
        foreach($this->_list as $item => $cutename)
        {
            if($options[$item] == true)
            {
                if($result != '') $result .= ', ';
                $result .= $view->translate($cutename);
            }
        }

        return $result;
    }
}

?>
