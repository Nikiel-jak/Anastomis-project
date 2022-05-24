<?php
class App_Form_Admin_AttributesValues extends App_Form_Base
{  
    public function init()
    {
        $this->setName('Groups');
        $this->addAttribs(array('class' => 'form-horizontal'));
        
        $value = new Zend_Form_Element_Text('value');
        $value->setRequired(true)
                 ->setLabel('FORMS_VALUE')
                 ->setDecorators($this->textDecorator)->getDecorator('Label')->setRequiredSuffix(self::REQUIRED_FIELD);

        $view = new Zend_Form_Element_Text('view');
        $view->setRequired(true)
                 ->setLabel('FORMS_VIEW')
                 ->setDecorators($this->textDecorator)->getDecorator('Label')->setRequiredSuffix(self::REQUIRED_FIELD);
        
        $status = new Zend_Form_Element_Checkbox('status');
        $status ->setLabel('FORMS_STATUS_ACTIVE')
                 ->setDecorators($this->textDecorator);
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setIgnore(true)
                ->setAttrib('class','btn')
               ->setDecorators($this->submitDecorator)
               ->setLabel('FORMS_SAVE');
               
        $this->addElements(array(
            $value,
            $view,
            $status,
            $submit
        ));
    }
}