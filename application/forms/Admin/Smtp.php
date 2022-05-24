<?php

class App_Form_Admin_Smtp extends App_Form_Base
{
    public function init()
    {
        $this->addAttribs(array('class' => 'form-horizontal'));
        
        $host = new Zend_Form_Element_Text('host');
        $host->setRequired(true)
             ->setLabel('FORMS_ADMIN_SMTP_HOST')
             ->setDecorators($this->textDecorator);
        
        $username = new Zend_Form_Element_Text('username');
        $username->setRequired(true)
             ->setLabel('FORMS_ADMIN_SMTP_USERNAME')
             ->setDecorators($this->textDecorator);
        
        $auth = new Zend_Form_Element_Text('auth');
        $auth->setRequired(true)
             ->setLabel('FORMS_ADMIN_SMTP_AUTH')
             ->setDecorators($this->textDecorator);

        $port = new Zend_Form_Element_Text('port');
        $port->setRequired(true)
             ->setLabel('FORMS_ADMIN_SMTP_PORT')
             ->addValidator(new Zend_Validate_Digits())
             ->setDecorators($this->textDecorator);

        $password = new Zend_Form_Element_Text('password');
        $password->setRequired(true)
             ->setLabel('FORMS_ADMIN_SMTP_PASSWORD')
             ->setDecorators($this->textDecorator);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setIgnore(true)
               ->setAttrib('class', 'btn')
               ->setDecorators($this->submitDecorator)
               ->setLabel('FORMS_ADMIN_SMTP_SAVE');

        $this->addElements(array(
            $host,
            $port,
            $password,
            $username,
            $auth,
            $submit
        ));
        
    }
}