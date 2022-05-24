<?php
class App_Form_Admin_Groups extends App_Form_Base
{  
    public function init()
    {
        $this->setName('Groups');
        $this->addAttribs(array('class' => 'form-horizontal'));
        
        $name = new Zend_Form_Element_Text('name');
        $name->setRequired(true)
                 ->setLabel('FORMS_NAME')
                 ->setDecorators($this->textDecorator)->getDecorator('Label')->setRequiredSuffix(self::REQUIRED_FIELD);
        
        $hint = new Zend_Form_Element_Text('hint');
        $hint->setLabel('FORMS_HINT')
                 ->setDecorators($this->textDecorator);
        
        $status = new Zend_Form_Element_Checkbox('status');
        $status ->setLabel('FORMS_STATUS_ACTIVE')
                 ->setDecorators($this->textDecorator);
        
        $can_hide = new Zend_Form_Element_Checkbox('can_hide');
        $can_hide ->setLabel('FORMS_CAN_HIDE')
                 ->setDecorators($this->textDecorator);
                    
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setIgnore(true)
                ->setAttrib('class','btn')
               ->setDecorators($this->submitDecorator)
               ->setLabel('FORMS_SAVE');
               
        $this->addElements(array(
            $name,
            $hint,
            $can_hide,
            $status,
            $submit
        ));
    }
    
}