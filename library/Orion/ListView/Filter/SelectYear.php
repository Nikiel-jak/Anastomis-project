<?php

class Orion_ListView_Filter_SelectYear extends Orion_ListView_Filter
{
    protected $_colname;
    protected $_options;
    protected $_error = false;
    
    public function __construct($name, $colname, $options)
    {
        parent::__construct($name);
        
        $this->_colname = $colname;
        $this->_options = $options;
    }
    
    public function getSessionData($post)
    {
        return trim($post);
    }
    
    public function setStatus()
    {
        if($this->_sessionData == '') $this->_status = self::FILTER_EMPTY;
        else $this->_status = self::FILTER_READY;
    }
    
    public function getDefaultSessionData()
    {
        $model = new App_Model_Budgets_Year_DbTable();
        return $model->getActiveYear(false);
    }
    
    public function addWhere(&$select)
    {   
        $tmp = $this->_listview->getColumnNamePairs();
        @$ugly_colname = $tmp[$this->_colname];
        
        if(in_array($this->_sessionData, array_keys($this->_options)))
        {
            
            $select->where("extract(year from {$this->_colname}) = ?", $this->_sessionData);
        }
        else {
            $model = new App_Model_Budgets_Year_DbTable();
            $select->where("extract(year from {$this->_colname}) = ?", $model->getActiveYear(false));
        }
    }
    
    public function render()
    {
        $big_name = strtoupper($this->_listview->getName()) . '_FILTER_' . strtoupper($this->_name);
        
        $view = new Zend_View();
        
        $error = '';
        if($this->_error) $error = "style='border: 1px solid red'";
        $html = '<select name="filter[' . $this->_name . ']" id="' . $this->_name . '">';
        if(count($this->_options)){
            foreach($this->_options as $key => $option){
                if($this->_status == self::FILTER_READY && $key == $this->_sessionData)
                    $html .= '<option selected="selected" value="' . $key . '" label = "' . $view->translate(strtoupper($option)) . '">' . $view->translate(strtoupper($option)) . '</option>';
                else
                    $html .= '<option value="' . $key . '" label = "' . $view->translate(strtoupper($option)) . '">' . $view->translate(strtoupper($option)) . '</option>';
            }
        }
        $html .= '</select>';
                
        return $html;
    }
    

}

?>
