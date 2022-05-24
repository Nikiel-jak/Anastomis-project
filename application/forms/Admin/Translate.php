<?php

class App_Form_admin_Translate extends App_Form_Base{
    
    public function init()
    {   
        $this->addAttribs(array('class' => 'form-horizontal'));
        $key = new Zend_Form_Element_Text('0');
        $key->setLabel('Indeks:')
              ->setDecorators($this->textDecorator)
              ->setAttribs(array('disabled' => 'disabled','class' => 'span7'))
              ->setIgnore(false);
        
        $value = new Zend_Form_Element_Textarea('1');
        $value->setLabel('Tłumaczenie:')
                ->setAttribs(array('class' => 'span7'))
              ->setDecorators($this->textDecorator)
              ->setRequired(true)->getDecorator('Label')->setRequiredSuffix(self::REQUIRED_FIELD);
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setIgnore(true)
               ->setAttrib('class','btn')
               ->setDecorators($this->submitDecorator)
               ->setLabel('Zapisz');  
               
        $this->addElements(array(
                $key,
                $value,
                $submit
        )); 
    }
}