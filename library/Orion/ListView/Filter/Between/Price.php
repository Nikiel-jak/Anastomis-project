<?php

class Orion_ListView_Filter_Between_Price extends Orion_ListView_Filter_Between
{
    
    public function getSessionData($post)
    {
        $post['from'] = preg_filter('/[^0-9,.]/', '', $post['from']) ?: $post['from'];
        $post['from'] = str_replace(',', '.', $post['from']);
        
        $post['to'] = preg_filter('/[^0-9,.]/', '', $post['to']) ?: $post['to'];
        $post['to'] = str_replace(',', '.', $post['to']);
        
        $post['to'] = filter_var($post['to'], FILTER_VALIDATE_FLOAT);
        $post['from'] = filter_var($post['from'], FILTER_VALIDATE_FLOAT);
        
        return $post;
    }
        
    public function setStatus()
    {
        parent::setStatus();
        
        $this->_status = self::FILTER_READY;
    }
    
}

?>
