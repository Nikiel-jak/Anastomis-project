<?php

class App_Form_Site_ContactSecond extends App_Form_Base
{

    public function init()
    {
        $this->setName('contact');
        $this->setMethod('post');
        
        $email = new Zend_Form_Element_Text('email');
        $email->addValidator(new Zend_Validate_EmailAddress())
             ->setDecorators($this->sitetextDecorator)
             ->setLabel('SITE_CONTACT_EMAIL')
             ->setRequired(true)->getDecorator('Label')
             ->setRequiredSuffix(self::REQUIRED_FIELD);
        
        $number = new Zend_Form_Element_Text('number');
        $number->setDecorators($this->sitetextDecorator)
             ->setLabel('SITE_CONTACT_ORDER_NUMBER')
             ->setRequired(true)->getDecorator('Label')
             ->setRequiredSuffix(self::REQUIRED_FIELD);
        
        $textarea = new Zend_Form_Element_Textarea('question');
        $textarea->setRequired(true)
                 ->setAttribs(array('rows'=>8,'cols' => 70))
                 ->setDecorators($this->textareaDecorator)
                 ->setLabel('SITE_CONTACT_TEXTAREA')
                 ->getDecorator('Label')->setRequiredSuffix(self::REQUIRED_FIELD);
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setIgnore(true)
                ->setDecorators($this->submitSiteDecorator)
               ->setLabel('SITE_CONTACT_SUBMIT');
        
        $this->addElements(array(
            $email,
            $number,
            $textarea, 
            $submit,
        )); 
    }
}