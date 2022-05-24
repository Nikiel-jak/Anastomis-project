<?php

class App_Form_Admin_Configuration extends App_Form_Base
{  
    public function init()
    {
        
        $this->addAttribs(array('class' => 'form-horizontal'));

        $name = new Zend_Form_Element_Text('site_name');
        $name->setRequired(true)
             ->setLabel('FORMS_ADMIN_CATEGORIES_NAME')
             ->setDecorators($this->textDecorator);
        $email = new Zend_Form_Element_Text('admin_email');
        $email->setRequired(true)
              ->setDecorators($this->textDecorator)
              ->addValidator(new Zend_Validate_EmailAddress())
              ->setLabel('FORM_ADMIN_CONFIGURATION_OWN_EMAIL');
        
        $radio = new Zend_Form_Element_Radio('site_status');
        $radio->setDecorators($this->radioDecorator)
              ->setLabel('FORM_ADMIN_CONFIGURATION_SHOW_SITE')
              ->setMultiOptions($this->getShowHideSite());
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setIgnore(true)
               ->setAttrib('class', 'btn')
               ->setDecorators($this->submitDecorator)
               ->setLabel('FORMS_ADMIN_CATEGORIES_SAVE');
               
        $metatitle = new Zend_Form_Element_Text('site_title');
        $metatitle->setRequired(true)
                  ->setDecorators($this->textDecorator)
                  ->setLabel('FORMS_ADMIN_CATEGORIES_META_TITLE');
                             
        $metakeyword = new Zend_Form_Element_Text('site_keywords');
        $metakeyword->setRequired(true)
                    ->setDecorators($this->textDecorator)
                    ->setLabel('FORMS_ADMIN_CATEGORIES_META_KEYWORDS');
                    
        $metatextarea = new Zend_Form_Element_Textarea('site_description');
        $metatextarea->setRequired(true)
                     ->setLabel('FORMS_ADMIN_CATEGORIES_META_DESCRIPTION')
                     ->setAttrib('class', 'ckeditor')
                     ->addFilter(new Zend_Filter_StripTags())
                     ->setDecorators($this->textDecorator);
                          
        $this->addElements(array(
            $name,
            $email,
            $radio,
            $metatitle,
            $metakeyword,
            $metatextarea,
            $submit
        ));
    }
    
    public function getShowHideSite()
    {
        $data = array(
            0 => 'ADMIN_FORM_SITE_HIDE',
            1 => 'ADMIN_FORM_SITE_SHOW',
        );
        return $data;
    }
      // Trzeba przemyśleć gdzie to dać nie może być w formularzu nowy plik konfiguracynjy  gobal??
    public function getLanguageSite()
    {
        $data = array(
            'pl' => 'GLOBAL_LANGUAGE_PL',
            'en' => 'GLOBAL_LANGUAGE_EN',
        );
        return $data;
    }
    
    public function getLanguageAdmin()
    {
        $data = array(
            'pl' => 'GLOBAL_LANGUAGE_PL',
        );
        return $data;
    }  
}