<?php

class Orion_ListView_Filter_SingleInput extends Orion_ListView_Filter
{
    const MATCH_EQUAL = 1;
    const MATCH_STRING = 2;
    const MATCH_GREATER = 3;
    
    protected $_colname;
    protected $_matchtype;
    protected $_size;
    protected $_class;


    public function __construct($name, $colname, $matchtype, $size, $class = null)
    {
        parent::__construct($name);
        
        $this->_colname = $colname;
        $this->_matchtype = $matchtype;
        $this->_size = $size;
        $this->_class = $class;
    }
    
    public function getSessionData($post)
    {
        return trim($post);
    }
    
    public function setStatus()
    {
        if ($this->_sessionData == '' || $this->_sessionData == null)
        {
            $this->_status = self::FILTER_EMPTY;
        }
        else
        {
            $this->_status = self::FILTER_READY;
        }
    }
    
    public function addWhere(&$select)
    {
        $tmp = $this->_listview->getColumnNamePairs();
        $ugly_colname = $tmp[$this->_colname];

        if($this->_matchtype == self::MATCH_EQUAL)
        {
            $value = preg_filter('/[^0-9]/', '', $this->_sessionData) ?: $this->_sessionData;
            if(count($value) && preg_match("/\d+/", $value))
                $select->where("{$ugly_colname} = ?", $this->_sessionData);
            else 
                $this->_status = self::FILTER_INCORRECT;
        }
        elseif($this->_matchtype == self::MATCH_GREATER)
        {
            $value = preg_filter('/[^0-9]/', '', $this->_sessionData) ?: $this->_sessionData;
            if(count($value) && preg_match("/\d+/", $value))
                $select->where("{$ugly_colname} >= ?", $this->_sessionData);
            else 
                $this->_status = self::FILTER_INCORRECT;
        }
        else if($this->_matchtype == self::MATCH_STRING)
        {
            $select->where("{$ugly_colname} LIKE ?", "%{$this->_sessionData}%");
        }
        else
        {
            throw new Exception('Nieznany typ MATCH w SingleInput Filter!');
        }
    }
    
    public function render()
    {
        $big_name = strtoupper($this->_listview->getName()) . '_FILTER_' . strtoupper($this->_name);
        
        $view = new Zend_View();
        
        $error = '';
        if($this->_status == self::FILTER_INCORRECT) $error = "style='border: 1px solid red'";
        
        $class = '';
        if($this->_class) $class = 'class = "' . $this->_class. '"';
        
        if($this->_status == self::FILTER_EMPTY)
        {
            return "<input {$class} type='text' name='filter[{$this->_name}]' size='{$this->_size}' value='' placeholder='{$view->translate($big_name)}' {$error}>";
        }
        else
        {
            return "<input type='text' name='filter[{$this->_name}]' size='{$this->_size}' value='{$this->_sessionData}' placeholder='{$view->translate($big_name)}' {$error}>";
        }
    }
    

}

?>
