<?php

class App_Form_Confirm extends App_Form_Base
{
    public function init()
    {
        $this->setName('confirmform');
        $this->setMethod('post');
        
        $submit_no = new Zend_Form_Element_Submit('cancel');
        $submit_no->setAttrib('id','submit_no')
                  ->setAttrib('class','uniform')
                  ->setDecorators($this->confirmAction)
                  ->setLabel('COMMON_NO');

        $submit_yes = new Zend_Form_Element_Submit('submit');
        $submit_yes->setAttrib('id','submit_yes')
                   ->setAttrib('class','uniform')
                   ->setDecorators($this->confirmAction)
                   ->setLabel('COMMON_YES');


        $this->addElements(array(
            $submit_yes,
            $submit_no,
        ));
        
    }
    
}
