<?php

class App_Form_Admin_Language extends App_Form_Base{
    
    public function init()
    {   
        $this->addAttribs(array('class' => 'form-horizontal'));
        
        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('Nazwa:')
                ->setAttrib('class', 'span4')
             ->setRequired(true)
              ->setDecorators($this->textDecorator)->getDecorator('Label')->setRequiredSuffix(self::REQUIRED_FIELD);
        
        $status = new Zend_Form_Element_Checkbox('status');
        $status->setOptions(array('checkedValue' => 1, 'uncheckedValue' => 0))
               ->setDecorators($this->checkboxDecorator)
               ->setLabel('Aktywny:');
              
        $default = new Zend_Form_Element_Checkbox('default');
        $default->setOptions(array('checkedValue' => 1, 'uncheckedValue' => 2))
               ->setDecorators($this->checkboxDecorator)
               ->setLabel('Domyslny:');
        
        $prefix = new Zend_Form_Element_Text('prefix');
        $prefix->setLabel('Prefix:')
               ->addValidator(new Orion_Validate_ValueNotExist('language','prefix'))
               ->setAttrib('class', 'span1')
              ->setDecorators($this->textDecorator)
              ->setRequired(true)->getDecorator('Label')->setRequiredSuffix(self::REQUIRED_FIELD);
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setIgnore(true)
               ->setAttrib('class','btn')
               ->setDecorators($this->submitDecorator)
               ->setLabel('Zapisz');  
               
        $this->addElements(array(
                $name,
                $prefix,
                $status,
                $default,
                $submit
        )); 
    }
    
    public function isValid($data) {
        $language = $this->getFormType();
        if($language->prefix == $data['prefix']){
            $this->getElement('prefix')->clearValidators();
        }
        return parent::isValid($data);
    }
}