<?php

class App_Form_Login extends App_Form_Base
{
    
    public function init()
    {   
        $this->setName('loginform');
        $this->setMethod('post');
        
        $login = new Zend_Form_Element_Text('login');
        $login->setOptions(array('required' => 'required', 'type' => 'email'))
              ->setAttrib("type", "email")
              ->addValidator(new Zend_Validate_EmailAddress())
              ->setDecorators($this->loginDecorator)
              ->setRequired(true);
        
        $password = new Zend_Form_Element_Password('password');
        $password->setDecorators($this->passwordDecorator)
                 ->setRequired(true);

        $view = new Zend_View();
                 
        if($this->getFormType() != 'site'){
            $login->setLabel('ADMIN_FORM_LOGIN_LOGIN');
            $password->setLabel('ADMIN_FORM_LOGIN_PASSWORD');
        } else {
            $login->setAttrib('placeholder', $view->translate('ADMIN_FORM_LOGIN_LOGIN'))->removeDecorator('Label');;
            $password->setAttrib('placeholder', $view->translate('ADMIN_FORM_LOGIN_PASSWORD'))->removeDecorator('Label');;
        }
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setIgnore(true)
               ->setAttrib('class', 'btn')
               ->setDecorators($this->submitDecorator)
               ->setLabel('ADMIN_FORM_LOGIN_SUBMIT');


        $this->addElements(array(
            $login,
            $password,
            $submit
        ));
        
    }
   
    
}