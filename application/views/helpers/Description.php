<?php

class View_Helper_Description extends Zend_View_Helper_Placeholder_Container_Standalone
{
    /**
     * Registry key for placeholder
     * @var string
     */
    protected $_regKey = 'Helper_Description';

    public function description($name = null, $setType = Zend_View_Helper_Placeholder_Container_Abstract::APPEND)
    {
        if ($name) {
        	$navi = array('name'=>$name);
            if ($setType == Zend_View_Helper_Placeholder_Container_Abstract::SET) {
                $this->set($navi);
            } elseif ($setType == Zend_View_Helper_Placeholder_Container_Abstract::PREPEND) {
                $this->prepend($navi);
            } else {
                $this->append($navi);
            }
        }
        
        return $this;
    }

    public function toString()
    {
		$separator = $this->getSeparator();

        $items = array();
        foreach ($this as $item) {
        	$items[] = $this->_escape($item['name']);
        }
        return '<meta name="description" content="' . implode($separator, $items) . '" />';
    }  

    /**
     * Cast to string
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}