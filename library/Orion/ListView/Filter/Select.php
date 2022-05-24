<?php

class Orion_ListView_Filter_Select extends Orion_ListView_Filter
{
    protected $_colname;
    protected $_options;
    protected $_select_text;
    protected $_error = false;
    
    public function __construct($name, $colname, $options, $select_text = null)
    {
        parent::__construct($name);
        
        $this->_colname = $colname;
        $this->_options = $options;
        $this->_select_text = $select_text;
    }
    
    public function getSessionData($post)
    {
        return trim($post);
    }
    
    public function setStatus()
    {
        if($this->_sessionData == '' || $this->_sessionData == null) $this->_status = self::FILTER_EMPTY;
        else $this->_status = self::FILTER_READY;
    }
    
    public function addWhere(&$select)
    {   
        $tmp = $this->_listview->getColumnNamePairs();
        $ugly_colname = $tmp[$this->_colname];
        
        if($this->_sessionData != -1){
            if(in_array($this->_sessionData, array_keys($this->_options)))
            {
                $select->where("{$ugly_colname} = ?", $this->_sessionData);
            }
        }
    }
    
    public function render()
    {
        $big_name = strtoupper($this->_listview->getName()) . '_FILTER_' . strtoupper($this->_name);
        
        $view = new Zend_View();
        
        $error = '';
        if($this->_error) $error = "style='border: 1px solid red'";
        
        $html = '<select class="span2" name="filter[' . $this->_name . ']" id="' . $this->_name . '">';
        if($this->_select_text != null)
            $html .= '<option value="-1" label = "' . $view->translate($this->_select_text) . '">' . $view->translate($this->_select_text) . '</option>';
        if(count($this->_options)){
            foreach($this->_options as $key => $option){
                if($this->_status == self::FILTER_READY && $key == $this->_sessionData)
                    $html .= '<option selected="selected" value="' . $key . '" label = "' . $view->translate($option) . '">' . $view->translate($option) . '</option>';
                else
                    $html .= '<option value="' . $key . '" label = "' . $view->translate($option) . '">' . $view->translate($option) . '</option>';
            }
        }
        $html .= '</select>';
                
        return $html;
    }
    

}

?>
