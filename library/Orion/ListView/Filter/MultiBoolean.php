<?php

class Orion_ListView_Filter_MultiBoolean extends Orion_ListView_Filter
{
    protected $_options;


    public function __construct($name, $options)
    {
        parent::__construct($name);
        
        $this->_options = $options;
    }
    
    public function getSessionData($post)
    {
        return $post;
    }
    
    public function setStatus()
    {
        $this->_status = self::FILTER_READY;
    }
    
    public function addWhere(&$select)
    {
        $tmp = $this->_listview->getColumnNamePairs();

        foreach($this->_options as $colname => $cutename)
        {
            $ugly_colname = $tmp[$colname];
            
            if(isset($this->_sessionData[$colname]))
                $select->where("{$ugly_colname} = ?", true);
        }
    }
    
    public function render()
    {
        $big_name = strtoupper($this->_listview->getName()) . '_FILTER_' . strtoupper($this->_name);
        
        $view = new Zend_View();
        
        $result = '';
        
        foreach($this->_options as $colname => $cutename)
        {
            $selected = '';
            if(isset($this->_sessionData[$colname]))
            {
                $selected="checked";
            }
            
            $id = strtolower($view->translate($cutename));
            
            $result .= "<div class='filtr_element filtr_checkbox'><label class='checkbox' for='{$id}' ><input id='{$id}' type='checkbox' name='filter[{$this->_name}][{$colname}]' {$selected} class='checkbox'>" . $view->translate($cutename) . "</input></label></div>";
        }
        
        return $result;
    }
    

}

?>
