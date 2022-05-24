<?php

class Orion_ListView_Filter_Between_Date extends Orion_ListView_Filter_Between
{
    protected $format;

    public function __construct($name, $colname, $size, $format = null)
    {

        parent::__construct($name, $colname, $size);

        $this->_format = $format;
    }

    public function setStatus()
    {
        parent::setStatus();
        
        if($this->_fromField != self::FILTER_EMPTY)
        {
            if(Orion_Utility::isDateValid($this->_sessionData['from']))
            {
               $this->_fromField = self::FILTER_READY; 
            }
            else
            {
                $this->_fromField = self::FILTER_INCORRECT;
            }
        }
        
        if($this->_toField != self::FILTER_EMPTY)
        {
            if(Orion_Utility::isDateValid($this->_sessionData['to']))
            {
               $this->_toField = self::FILTER_READY; 
            }
            else
            {
                $this->_toField = self::FILTER_INCORRECT;
            }
        }

        if($this->_fromField == self::FILTER_INCORRECT || $this->_toField == self::FILTER_INCORRECT)
        {
            $this->_status = self::FILTER_INCORRECT;
            return;
        }
        
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
            if($this->_format){
                $format = strtoupper($this->_format);
                $select->where("{$format}({$ugly_colname}) >= ?", $this->_sessionData['from']);
            } else {
                $select->where("{$ugly_colname} >= ?", $this->_sessionData['from']);
            }
        }

        if($this->_toField == self::FILTER_READY)
        {
            if($this->_format){
                $format = strtoupper($this->_format);
                $select->where("{$format}({$ugly_colname}) <= ?", $this->_sessionData['from']);
            } else {
                $select->where("{$ugly_colname} <= ?", $this->_sessionData['to']);
            }
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

        $inputs = "<input class='filtrinput datePicker span1' type='text' name='filter[{$this->_name}][from]' size='{$this->_size}' value='{$this->_sessionData['from']}' {$error_from}>".
                "<input class='double filtrinput datePicker span1' type='text' name='filter[{$this->_name}][to]' size='{$this->_size}' value='{$this->_sessionData['to']}'  {$error_to}>";
        
        return $inputs;
    }
}

?>
