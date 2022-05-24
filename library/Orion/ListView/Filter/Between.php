<?php

class Orion_ListView_Filter_Between extends Orion_ListView_Filter
{
    protected $_colname;
    protected $_size;
    
    protected $_fromField;
    protected $_toField;
    
    public function __construct($name, $colname, $size)
    {
        parent::__construct($name);
        
        $this->_colname = $colname;
        $this->_size = $size;
    }
    
    public function getSessionData($post)
    {
        $post['from'] = trim($post['from']);
        $post['to'] = trim($post['to']);
        return $post;
    }
    
    public function setStatus()
    {
        if ($this->_sessionData['from'] == '' || $this->_sessionData['from'] == null)
            $this->_fromField = self::FILTER_EMPTY;
        else
            $this->_fromField = self::FILTER_READY;
                
        if ($this->_sessionData['to'] == '' || $this->_sessionData['to'] == null)
            $this->_toField = self::FILTER_EMPTY;
        else
            $this->_toField = self::FILTER_READY;
                

        if($this->_fromField == self::FILTER_EMPTY && $this->_toField == self::FILTER_EMPTY)
        {
            $this->_status = self::FILTER_EMPTY;
            return;
        }
        
        $this->_status = self::FILTER_READY;
    }
    
    public function addWhere(&$select)
    {   
        $tmp = $this->_listview->getColumnNamePairs();
        $ugly_colname = $tmp[$this->_colname];
        
        if($this->_fromField == self::FILTER_READY)
        {
            $select->where("{$ugly_colname} >= ?", $this->_sessionData['from']);
        }
        
        if($this->_toField == self::FILTER_READY)
        {
            $select->where("{$ugly_colname} <= ?", $this->_sessionData['to']);
        }
        
    }
    
    public function render()
    {
        $big_name = strtoupper($this->_listview->getName()) . '_FILTER_' . strtoupper($this->_name);
        $placeholder_from = $big_name."_FROM";
        $placeholder_to = $big_name."_TO";
        
        $view = new Zend_View();
        
        $error_from = '';
        $error_to = '';
        if($this->_fromField == self::FILTER_INCORRECT)
        {
            $error_from = "style='border: 1px solid red'";
        }
        
        if($this->_toField == self::FILTER_INCORRECT)
        {
            $error_to = "style='border: 1px solid red'";
        }
        
        $inputs = "<input class='double' type='text' name='filter[{$this->_name}][from]' size='{$this->_size}' value='{$this->_sessionData['from']}' placeholder='{$view->translate($placeholder_from)}' {$error_from}>".
                "<input type='text' name='filter[{$this->_name}][to]' size='{$this->_size}' value='{$this->_sessionData['to']}' placeholder='{$view->translate($placeholder_to)}' {$error_to}>";
        
        return $inputs;
    }
    

}

?>
