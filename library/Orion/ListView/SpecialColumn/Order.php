<?php

class Orion_ListView_SpecialColumn_Order extends Orion_ListView_SpecialColumn
{
    protected $_params;
    
    public function __construct($options, $params = null)
    {
        if($params){
            $this->_params = $params;
        }
        parent::__construct($options);
    }

    public function render()
    {
        $html = '';
        $options = $this->getOptions();
        $view = new Zend_View();
        $pagg = $this->_listview->getPaginator();
        $key = $pagg->getCurrentItems()->key();
        if($key != 0){
            $html .= '<a href="'.$view->url(array('id' => $options['id'],'content_id' => $options['content_id'],'type' => 'up'),'admin-pagemanager-change-position').'">UP</a>';
 
        }
        if($key != 0 && $pagg->getTotalItemCount() != ($key+1) && ($key+1) != $pagg->getItemCountPerPage()){
            $html .= ' || ';
        }
        if($pagg->getTotalItemCount() != ($key+1) && ($key+1) != $pagg->getItemCountPerPage()){
            $html .= '<a href="'.$view->url(array('id' => $options['id'], 'content_id' => $options['content_id'],'type' => 'down'),'admin-pagemanager-change-position').'">DOWN</a>';
        }
        return $html;
    }
}

?>
