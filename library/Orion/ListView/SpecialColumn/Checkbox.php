<?php

class Orion_ListView_SpecialColumn_Checkbox extends Orion_ListView_SpecialColumn
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
        $options = $this->getOptions();


        $class = (array_key_exists('can_delete', $options) && $options['can_delete'] != 1) ? 'no_delete ' : '';
        $class .= (array_key_exists('class', $options)) ? $options['class'] : '';

        $checked = ($this->_params && is_array($this->_params['checked']) && array_key_exists($options['id'],  $this->_params['checked'])) ? 'checked="checked"' : '';
       // var_dump(array_key_exists($options['id'],  $this->_params['checked']), $options['id']);
        $view = new Zend_View();
        $html = "<input ".$checked." class='checkbox ".$class."' name='".$options['name']."[]' type='checkbox' value='".$options['id']."' >";
        if($this->_params){
            $html .= '<input type="hidden" name="hidden_'.$options['name'].'[]" value="'.$options['id'].'">';
        }
        return $html;
    }
}

?>
