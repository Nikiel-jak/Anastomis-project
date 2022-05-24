<?php

class App_Form_ResetPassword extends App_Form_Base
{

    public function init()
    {
        $this->setName('loginform_admin');
        $this->setMethod('post');
		$password = new Zend_Form_Element_Password('password');
        $password->setLabel('ACCOUNT_PASSWORD_RESET')
                 ->setDecorators($this->textDecorator)
                 ->addValidator(new Zend_Validate_StringLength(6, 12))
                 ->addFilter(new Zend_Filter_StringTrim())
                 ->setRequired(true);
				 
		$confirm_password = new Zend_Form_Element_Password('confirm_password');
        $confirm_password->setLabel('ACCOUNT_PASSWORD_RESET_CONFIRM')
                 ->setDecorators($this->textDecorator)
                 ->addFilter(new Zend_Filter_StringTrim())
                 ->setRequired(true);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setIgnore(true)
               ->setAttrib('class', 'btn')
               ->setDecorators($this->submitDecorator)
               ->setLabel('ADMIN_FORM_RESET_SUBMIT');


        $this->addElements(array(
            $password,
            $confirm_password,
            $submit
        ));
        
    }
    
    public function isValid($data)
    {
        $confirm = $this->getElement('confirm_password');
        $confirm->addValidator(new Zend_Validate_Identical($data['password']));
        return parent::isValid($data);
    }
        //AJAX VALID
    public function isValidPartial($data)
    {
        if(@$data['confirm_password']){
            $confirm = $this->getElement('confirm_password');
            $confirm->addValidator(new Zend_Validate_Identical($data['password']));
        }
        return parent::isValidPartial($data);
    }
}