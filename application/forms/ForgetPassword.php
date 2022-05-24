<?php

class App_Form_ForgetPassword extends App_Form_Base
{

    public function init()
    {
        $this->setName('loginform_admin');
        $this->setMethod('post');
        $login = new Zend_Form_Element_Text('email');
        $login->setLabel('ADMIN_FORM_LOGIN_LOGIN')
              ->setDecorators($this->loginDecorator)
              ->setRequired(true);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setIgnore(true)
               ->setAttrib('class', 'btn')
               ->setDecorators($this->submitDecorator)
               ->setLabel('ADMIN_FORM_FOREGETPASSWORD_SUBMIT');


        $this->addElements(array(
            $login,
            $submit
        ));
        
    }
    
}
