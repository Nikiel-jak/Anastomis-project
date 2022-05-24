<?php

class View_Helper_UploadPath extends Zend_View_Helper_Abstract 
{
    /**
     * Registry key for placeholder
     * @var string
     */
    protected $_regKey = 'Helper_UploadPath';    
    /**
     * Get image path
     *
     * @param string $fileName
     * @param bool|string $language
     * @param bool|string $theme
     * @param bool|string $skin
     * @return string
     */
    public function uploadPath($fileName)
    {   
        $path = $this->view->baseUrl();
        return $path.'/upload/'.$fileName;
    }
}