<?php

class View_Helper_ResourcePath extends Zend_View_Helper_Abstract 
{
    /**
     * Registry key for placeholder
     * @var string
     */
    protected $_regKey = 'Helper_ResourcePath';  
      
     /**
     * Get image path
     *
     * @param string $fileName
     * @param bool|string $language
     * @param bool|string $theme
     * @param bool|string $skin
     * @return string
     */
	public function resourcePath($fileName,$language = false, $skin = true,$resourceType = null) 
	{
	    if($resourceType == null) {
	        return '';
	    }
	    
	    if($language !== false) {
	    	$language = ($language === true) ? $this->view->lang_prefix : $language;
	    	$fileName = preg_replace('/\.[^.]+$/',"-$language\\0",$fileName);
	    }
	    
        $path_tmp = $this->view->baseUrl();
        
        if($skin) {
        	$skin = ($skin === true) ? $this->view->skin : $skin;
            $path_tmp .= '/skin/' . $skin;
        }
        $path_tmp .= '/' . $resourceType;
        $path_tmp .= '/' . $fileName;
        return $path_tmp;
	}
}