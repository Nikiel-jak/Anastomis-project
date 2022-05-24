<?php

class View_Helper_FlashPlayer extends Zend_View_Helper_Abstract 
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
    public function flashPlayer()
    {
        return $this->view->resourcePath('player.swf',false,'global','swf');
    }
}