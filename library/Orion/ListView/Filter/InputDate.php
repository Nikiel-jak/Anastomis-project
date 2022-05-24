<?php

class Orion_ListView_Filter_InputDate extends Orion_ListView_Filter
{
    protected $_colname;
    protected $_matchtype;
    protected $_size;
    protected $_class;
    
    const MATCH_GREATER = 1;
    const MATCH_LOWER = 2;

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
            if(Orion_Utility::isDateValid($this->_sessionData))
            {
               $this->_status = self::FILTER_READY; 
            }
            else
            {
                $this->_status = self::FILTER_INCORRECT;
            }
        }
    }
    
    public function addWhere(&$select)
    {
        $tmp = $this->_listview->getColumnNamePairs();
        $ugly_colname = $tmp[$this->_colname];

        if($this->_matchtype == self::MATCH_GREATER)
        {
            $select->where("{$ugly_colname} >= ?", "{$this->_sessionData}");
        }
        elseif($this->_matchtype == self::MATCH_LOWER)
        {
            $select->where("{$ugly_colname} <= ?", "{$this->_sessionData}");
        }
        else
        {
            throw new Exception('Nieznany typ MATCH w InputDate Filter!');
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
