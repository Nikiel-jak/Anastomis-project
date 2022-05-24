<?php

class View_Helper_ImagePath extends Zend_View_Helper_Abstract 
{
    /**
     * Registry key for placeholder
     * @var string
     */
    protected $_regKey = 'Helper_ImagePath';    
    /**
     * Get image path
     *
     * @param string $fileName
     * @param bool|string $language
     * @param bool|string $theme
     * @param bool|string $skin
     * @return string
     */
    public function imagePath($fileName,$language = false, $skin = 'site')
    {
        return $this->view->resourcePath($fileName,$language,$skin,'images');
    }
}