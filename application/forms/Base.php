<?php

class App_Form_Base extends Zend_Form
{
    const FORM_ADD = 'add';
    const FORM_EDIT = 'edit';
    const REQUIRED_FIELD = ' <span class="required">* </span>';
    const REQUIRED_FIELD_INFO = ' <span class="required">** </span>';

    protected $_formType = '';
    protected $_recordId;
    
    protected $config = null;
    
    public function __construct($options = null) {
        $this->config = Zend_Registry::get('config');
        parent::__construct($options);
    }
    
    public function getFileSize($size)
    {
        if($size == -1) {
            $size = (int)ini_get('post_max_size') * 1024 *1024;
        }
        
        return (int)$size;
    }
    
    public function getTmpDir()
    {
        $config = Zend_Registry::get('config');
        return $config->system->tmp_dir;
    }
    
    public function setDestination($elementName, $path)
    {
        if(!is_dir($path)){
            mkdir($path,0777, true);
        }
        $this->_destination = $path;
        $this->getElement($elementName)->setDestination($path);
    }
    
    public function setFormType($type)
    {
        $this->_formType = $type;
    }

    public function getFormType()
    {
        return $this->_formType;
    }

    public function setRecordId($id)
    {
        $this->_recordId = $id;
    }

    public function getRecordId()
    {
        return $this->_recordId;
    }

    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }
        parent::loadDefaultDecorators();
        $this->removeDecorator('HtmlTag');
    }

    public function loadDefaultSiteDecorators()
    {
    	$this->setDisableLoadDefaultDecorators(true);
        $this->addDecorator('FormElements')
                ->addDecorator('Form')
                ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'form'));
    }
    
    public $siteselectDecorator = array(
        'ViewHelper',
        array('Description', array('tag' => 'div', 'tagClass'=>'label', 'escape'=>false)),
        'Errors',
        array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div', 'class'=>'element')),
        array('Label', array('tag' => 'div', 'tagClass'=>'label', 'escape'=>false)),
        array('HtmlTag', array('tag' => 'div', 'class' => 'form-section')),
    );
    
    public $siteselectLangDecorator = array(
        'ViewHelper',
        array('Description', array('tag' => 'div', 'tagClass'=>'label', 'escape'=>false)),
        'Errors',
        array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div', 'class'=>'element')),
        array('Label', array('tag' => 'div', 'tagClass'=>'label', 'escape'=>false)),
        array('HtmlTag', array('tag' => 'div', 'class' => 'form-section select-language')),
    );
     
    public $sitetextDecorator = array(
        'ViewHelper',
        array('Description', array('tag' => 'div', 'tagClass'=>'label', 'escape'=>false)),
        array('Errors',array('escape' =>false)),
        array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div', 'class'=>'element')),
        array('Label', array('tag' => 'div',  'escape'=>false)),
        array('HtmlTag', array('tag' => 'div', 'class' => 'form-section')),
    );
    
     public $sitecheckboxDecorator = array(
        'ViewHelper',
        array('Description', array('tag' => 'div', 'tagClass'=>'label', 'escape'=>false)),
        array('Errors',array('escape' =>false)),
        array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div', 'class'=>'element')),
        array('Label', array('tag' => 'div', 'tagClass'=>'label', 'escape'=>false)),
        array('HtmlTag', array('tag' => 'div', 'class' => 'form-section checkbox-section')),
    );
     
    public $radioDecorator = array(
        'ViewHelper',
        'Errors',
        array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div', 'class'=>'controls')),
        array('Label', array('tag' => 'div', 'tagClass'=>'label', 'class' => 'control-label', 'escape'=>false)),
        array('HtmlTag', array('tag' => 'div', 'class' => 'ontrol-group')),
    );
    public $textDecorator = array(
        'ViewHelper',
        array('Errors',array('escape' =>false)),
        array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div', 'class'=>'controls')),
        array('Description', array('tag' => 'div',  'escape'=>false)),
        array('Label', array('tag' => 'div', 'class' => 'control-label', 'escape'=>false)),
        array('HtmlTag', array('tag' => 'div', 'class' => 'control-group')),
    );

    public $fileDecorator = array(
        'File',
        array('Errors',array('escape' =>false)),
        array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div','class'=>'controls')),
        array('Description', array('tag' => 'div','class'=>'controls','escape'=>false)),
        array('Label', array('tag' => 'div','class'=>'control-label','escape'=>false)),
        array('HtmlTag', array('tag' => 'div', 'class' => 'control-group')),
    );

    public $checkboxDecorator = array(
        'ViewHelper',
        array('Description', array('tag' => 'div', 'tagClass'=>'label', 'escape'=>false)),
        array('Errors',array('escape' =>false)),
        array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div', 'class'=>'controls')),
        array('Label', array('tag' => 'div', 'class' => 'control-label', 'tagClass'=>'label', 'escape'=>false)),
        array('HtmlTag', array('tag' => 'div', 'class' => 'control-group')),
    );

    public $multiRadioDecorator = array(
        'ViewHelper',
        array('Description', array('tag' => 'div', 'tagClass'=>'label', 'escape'=>false)),
        array('Errors',array('escape' =>false)),
        array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div', 'class'=>'element radio')),
        array('Label', array('tag' => 'div', 'tagClass'=>'label', 'escape'=>false)),
        array('HtmlTag', array('tag' => 'div', 'class' => 'form_item')),
    );

    public $textareaDecorator = array(
        'ViewHelper',
        array('Description', array('tag' => 'div', 'tagClass'=>'label', 'escape'=>false)),
        'Errors',
        array(array('elementDiv' => 'HtmlTag'), array('tag' => 'p', 'class'=>'element')),
        array('Label', array('tag' => 'div', 'tagClass'=>'label', 'escape'=>false)),
        array('HtmlTag', array('tag' => 'div', 'class' => 'form-section')),
    );

    public $selectDecorator = array(
        'ViewHelper',
        array('Description', array('tag' => 'div', 'tagClass'=>'label', 'escape'=>false)),
        'Errors',
        array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div', 'class'=>'controls')),
        array('Label', array('tag' => 'div', 'class' => 'control-label', 'tagClass'=>'label', 'escape'=>false)),
        array('HtmlTag', array('tag' => 'div', 'class' => 'control-group')),
    );

    public $submitDecorator = array(
        'ViewHelper',
        array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div','class'=>'button')),
    );
    
    public $submitSiteDecorator = array(
        'ViewHelper',
        array(array('elementDiv' => 'HtmlTag'), array('tag' => 'p')),
    );
   
    public $submitQuizDecorator = array(
        'ViewHelper',
        array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div','class'=>'buttonWrite')),
    );

    public $addSceneDecorator = array(
        'ViewHelper',
        array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div','class'=>'button buttonSubmit buttonLight icon iconAddScene')),
    );
        
    public $productSubmitDecorator = array(
        'ViewHelper',
        array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div','class'=>'button buttonSubmit buttonLight icon iconSave')),
        array(array('clearDiv' => 'HtmlTag'), array('tag' => 'div','class'=>'clearSpace','placement' => 'append'))
    );
    
    public $submitLoginDecorator = array(
        'ViewHelper',
        array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div','class'=>'submitButton right')),
    );
    
    public $loginAgreeDecorator = array(
        'ViewHelper',
        array('Description', array('tag' => 'div','tagClass'=>'label','escape'=>false)),
        'Errors',
        array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div', 'class'=>'element')),
        array('Label', array('tag' => 'div', 'tagClass'=>'label', 'escape'=>false, 'placement'=>'append')),
        array(array('agreeContainer' => 'HtmlTag'), array('tag' => 'div', 'class'=>'agree_container')),
        array(array('clearDiv' => 'HtmlTag'), array('tag' => 'div','class'=>'both','placement' => 'append'))
    );
    public $subscriberDecorator = array(
        'ViewHelper',
        'Errors',
        array('Label', array('tag' => 'div', 'tagClass'=>'label', 'escape'=>false, 'placement'=> 'prepend' ),
        array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div', 'class'=>'element')),),
        array(array('subscriberDecorato' => 'HtmlTag'), array('tag' => 'div', 'class'=>'form_item_checkbox')),
        array(array('clearDiv' => 'HtmlTag'), array('tag' => 'div','class'=>'both','placement' => 'append'))
    );
    public $agreeDecorator = array(
        array('Description', array('tag' => 'div','tagClass'=>'label','escape'=>false, 'placement'=> 'append' )),
        'ViewHelper',
        'Errors',
        array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div', 'class'=>'element')),
        array('Label', array('tag' => 'div', 'tagClass'=>'label', 'escape'=>false, 'placement'=> 'append' )),
        array(array('agreeContainer' => 'HtmlTag'), array('tag' => 'div', 'class'=>'agree_form_item')),
        array(array('clearDiv' => 'HtmlTag'), array('tag' => 'div','class'=>'both','placement' => 'append'))
    );

    public $agreePanelDecorator = array(
        'ViewHelper',
        array('Label', array('tagClass'=>'label', 'escape'=>false, 'placement' => 'append')),
        'Errors',
        array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div', 'class'=>'element')),
        array('Description', array('tag' => 'div','tagClass'=>'label','escape'=>false,'placement' => 'prepend')),
        array(array('agreeContainer' => 'HtmlTag'), array('tag' => 'div', 'class'=>'agree_form_item'))
    ); 
    
    public $radioInlineDecorator = array(
        'ViewHelper',
        array('Description', array('tag' => 'div','tagClass'=>'label','escape'=>false)),
        'Errors',
        array('HtmlTag',array('class'=>'form_inline_radiobuttons')),
        array('Label', array('tag' => 'div','tagClass'=>'label','escape'=>false)),
        array(array('row' => 'HtmlTag'), array('tag' => 'div','class'=>'radio_container')),
        array(array('clearDiv' => 'HtmlTag'), array('tag' => 'div','class'=>'both','placement' => 'append'))
    );
    
    public $multicheckboxProductDecorator = array(
        'ViewHelper',
        array('Description', array('tag' => 'div', 'tagClass'=>'label', 'escape'=>false)),
        'Errors',
        array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div', 'class'=>'element')),
        array('Label', array('tag' => 'div', 'tagClass'=>'label', 'escape'=>false)),
        array('HtmlTag', array('tag' => 'div', 'class' => 'form_item products_search'))
    );
    
    public $hidden_element = array(
    	'ViewHelper',
    );
    
    public $fieldsetDecorator = array(
        array('FormElements'),
        array('Fieldset'),
    );
    
    public $fieldsetSpaceDecorator = array(
        array('FormElements'),
        array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div', 'class' => 'tab_container')),
        array(array('clearDiv' => 'HtmlTag'), array('tag' => 'div','class'=>'clearSpace','placement' => 'append'))
    );
    
    public $fieldsetProductSapDecorator = array(
        array('FormElements'),
        array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div', 'id' => 'sap_product_container')),
    );
    
    public $fieldsetSubmitDecorator = array(
        array('FormElements'),
        array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div', 'class' => 'tab_container_submit')),
    );
    
    public $fieldsetDataDecorator = array(
        array('Description', array('elementDiv' => 'HtmlTag'), array('tag' => 'div', 'class' => 'desc')),
        array('FormElements'),
        array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div', 'class' => 'tab_container_data')),
    );
    
    public $htmlDecorator = array(
        'ViewHelper',
        array('Description', array('tag' => 'div','tagClass'=>'label','escape'=>false)),
        'Errors',
        array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div','class'=>'fck')),
        array('Label', array('tag' => 'div','tagClass'=>'label_fck','escape'=>false)),
        array(array('clearDiv' => 'HtmlTag'), array('tag' => 'div','class'=>'both','placement' => 'append'))
    );
    public $passwordDecorator = array(
        'ViewHelper',
        array('Description', array('tag' => 'div', 'tagClass'=>'label', 'escape'=>false)),
        'Errors',
        array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div', 'class'=>'element')),
        array('Label', array('tag' => 'div', 'tagClass'=>'label', 'escape'=>false)),
        array('HtmlTag', array('tag' => 'div', 'class' => 'password_group'))
    );
    
    public $loginDecorator = array(
        'ViewHelper',
        array('Description', array('tag' => 'div', 'tagClass'=>'label', 'escape'=>false)),
        'Errors',
        array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div', 'class'=>'element')),
        array('Label', array('tag' => 'div', 'tagClass'=>'label', 'escape'=>false)),
        array('HtmlTag', array('tag' => 'div', 'class' => 'login_group'))
    );
}
