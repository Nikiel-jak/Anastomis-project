<?php

abstract class Orion_ListView_Helper
{
    protected $_options;
    protected $_row;
    protected $_listview;
    
    public function __construct($options = array())
    {
        $this->_options = $options;
    }
    
    protected function getOptions()
    {
        return $this->_listview->transformArray($this->_options, $this->_row);
    }
    
    public function setRow($row)
    {
        $this->_row = $row;
    }
    
    public function setListView($listview)
    {
        $this->_listview = $listview;
    }

    public function getListViewName()
    {
        return $this->_listview->getName();
    }

}

?>
