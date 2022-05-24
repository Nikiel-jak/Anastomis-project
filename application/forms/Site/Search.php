<?php

class App_Form_Site_Search extends App_Form_Base
{   
    protected $lang;
    protected $type;
    
    public function setLang($lang){
        $this->lang = $lang;
    }
    
    public function getLang()
    {
        return $this->lang;
    }
    
    public function setType($type){
        $this->type = $type;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function init()
    {
        $this->setName('search');
        $this->addAttribs(array('class' => 'form-horizontal'));
              
        $number = new Zend_Form_Element_Text('number');
        $number->setLabel('SITE_SEARCH_FORM_ORDER_NUMBER')
                ->setRequired(true)
                ->addValidator(new Zend_Validate_Digits())
                ->setDecorators($this->sitetextDecorator)
                ->getDecorator('Label')->setRequiredSuffix(self::REQUIRED_FIELD);
        
        $works = new Zend_Form_Element_Select('works');
        $works->setLabel('SITE_SEARCH_FORM_ORDER_WORKS')
                ->setRequired(true)
                ->setMultiOptions(array(1 => '1 - Nowa Wieś',2 => '2 - Tychy', 3 => '3 - Tczew',4 => '4 - Radomsko'))
                ->setDecorators($this->siteselectDecorator)
                ->getDecorator('Label')->setRequiredSuffix(self::REQUIRED_FIELD);
        
        $model = new App_Model_Declaration_DbTable();
        $year = new Zend_Form_Element_Select('year');
        $year->setLabel('SITE_SEARCH_FORM_ORDER_YEAR')
                ->setRequired(true)
                ->setMultiOptions($model->getYear())
                ->setDecorators($this->siteselectDecorator)
                ->getDecorator('Label')->setRequiredSuffix(self::REQUIRED_FIELD);
        
        $lp = new Zend_Form_Element_Text('lp');
        $lp->setDecorators($this->sitetextDecorator)
                ->setAttrib('style', 'width:40px;')
                ->setLabel('SITE_SEARCH_FORM_ORDER_LP')->setDescription('SITE_SEARCH_FORM_LP_INFO');
        
        $show= new Zend_Form_Element_Checkbox('show');
        $show->setDecorators($this->sitecheckboxDecorator)
                ->setLabel('SITE_SEARCH_FORM_ORDER_SHOW_LABEL')
                ->setDescription('SITE_SEARCH_FORM_ORDER_SHOW');
        
        if($this->getType() == App_Model_Configuration_DbTable::PAGE_TYPE_MOBILE){
            $lp->setAttrib('style', 'width:248px;');
            $l = array(
            'pl' => 'PL',
            'ru'=>'RU',
            'en'=>'EN',
            'de'=>'DE',
            'fr' => 'FR',
            'cs' => 'CZ',
            'sl' => 'SL',
            'it' => 'IT',
            );
        } else {
        $l = array(
            'pl' => 'SITE_SEARCH_FORM_LANGUAGE_PL',
            'ru'=>'SITE_SEARCH_FORM_LANGUAGE_RU',
            'en'=>'SITE_SEARCH_FORM_LANGUAGE_EN',
            'de'=>'SITE_SEARCH_FORM_LANGUAGE_DE',
            'fr' => 'SITE_SEARCH_FORM_LANGUAGE_FR',
            'cs' => 'SITE_SEARCH_FORM_LANGUAGE_CZ',
            'sl' => 'SITE_SEARCH_FORM_LANGUAGE_SL',
            'it' => 'SITE_SEARCH_FORM_LANGUAGE_IT',
            );
        }
        $lang = new Zend_Form_Element_Select('language');
        $lang->setLabel('SITE_SEARCH_FORM_ORDER_LANGUAGE')
                ->setRequired(true)
                ->setMultiOptions($l)
                ->setDecorators($this->siteselectDecorator)
                ->getDecorator('Label')->setRequiredSuffix(self::REQUIRED_FIELD);
        if(array_key_exists($this->getLang(), $l)){
            $lang->setValue($this->getLang());
        }
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setIgnore(true)
            ->setDecorators($this->submitSiteDecorator)
            ->setLabel('SITE_SEARCH_FORM_SUBMIT'); 
        
        
        if($this->getType() == App_Model_Configuration_DbTable::PAGE_TYPE_MOBILE){
            $lang->setDecorators($this->siteselectLangDecorator);
            $this->addElements(array(
                    $lang,
                    $number,
                    $year,
                    $works,
                    $lp,
                    $show,
                    $submit
            )); 	   
        } else {
            $this->addElements(array(
                    $number,
                    $year,
                    $works,
                    $lp,
                    $show,
                    $lang,
                    $submit
            ));
        }
    }
    
}