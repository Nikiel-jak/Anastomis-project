<?php

class App_Form_admin_User extends App_Form_Base
{
    protected $_userId;
    
    public function setUserId($userId)
    {
        $this->_userId = $userId;
    }
    
    public function getUserId()
    {
        return $this->_userId;
    }
    
    public function init()
    {
        $this->addAttribs(array('class' => 'form-horizontal'));
        $this->setMethod('post');
                   
        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('FORM_ADMIN_USER_EMAIL')
              ->setDecorators($this->textDecorator)
              ->addValidator(new Zend_Validate_EmailAddress())
              ->setRequired(true)
            ->getDecorator('Label')->setRequiredSuffix(self::REQUIRED_FIELD);
        	  
		$password = new Zend_Form_Element_Password('password');
        $password->setLabel('FORM_ADMIN_USER_PASSWORD')
                 ->setDecorators($this->textDecorator)
                 ->addValidator(new Zend_Validate_StringLength(6, 12))
                 ->addFilter(new Zend_Filter_StringTrim());

				 
		$confirm_password = new Zend_Form_Element_Password('confirm_password');
        $confirm_password->setIgnore(true)
                ->setLabel('FORM_ADMIN_USER_PASSWORD_CONFIRM')
                 ->setDecorators($this->textDecorator)
                 ->addFilter(new Zend_Filter_StringTrim());

        if($this->getFormType() == 'add'){
            $password->setRequired(true);
            $confirm_password->setRequired(true);
        }	  
        $status = new Zend_Form_Element_Checkbox('status');
        $status->setOptions(array('checkedValue' => 1, 'uncheckedValue' => 0))
               ->setDecorators($this->textDecorator)
               ->setLabel('Aktywny');
			   
		$role = new Zend_Form_Element_Select('role');
        $role->setRequired()->addMultiOptions(self::getRole())
             ->setDecorators($this->selectDecorator)
             ->setLabel('FORM_ADMIN_USER_ROLE')
            ->getDecorator('Label')->setRequiredSuffix(self::REQUIRED_FIELD);
        
        
        if(!$this->getUserId()){
            $password->setRequired(true)->getDecorator('Label')->setRequiredSuffix(self::REQUIRED_FIELD);
            $confirm_password->setRequired(true)->getDecorator('Label')->setRequiredSuffix(self::REQUIRED_FIELD);
        }
        
                    
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setIgnore(true)
               ->setAttrib('class', 'btn')
               ->setDecorators($this->submitDecorator)
               ->setLabel('FORM_ADMIN_USER_SAVE');  
               
        $this->addElements(array(
                $email,
                $password,
                $confirm_password,
//                $firstname,
//                $lastname,
//                $gender,
//                $type,
//        		$birth_date,
        		$status,
        		$role,
                $submit
        )); 
 
    }
    public function isValid($data)
    {    

        if($this->getFormType() == 'edit'){
            if($this->getUserId()){
                $userModel = new App_Model_Users_DbTable();
                $user = $userModel->getById($this->getUserId());
                if($user->email != $data['email']){
                    $email = $this->getElement('email');
                    $email->addValidator(new Orion_Validate_ValueNotExist('users','email'));   
                }
            }
            if(!$data['password'] || !$data['confirm_password']){
                unset($data['password']);
                unset($data['confirm_password']);
            } else{
                $confirm = $this->getElement('confirm_password');
                $confirm->addValidator(new Zend_Validate_Identical($data['password'])); 
            }       
        } else {
            $email = $this->getElement('email');
            $email->addValidator(new Orion_Validate_ValueNotExist('users','email'));   
            $confirm = $this->getElement('confirm_password');
            $confirm->addValidator(new Zend_Validate_Identical($data['password'])); 
        }
        return parent::isValid($data);
    }
        //AJAX VALID
    public function isValidPartial($data)
    {                                    
        if($this->getFormType() == 'edit'){
            if($this->getUserId() && array_key_exists('email', $data)){
                $userModel = new App_Model_Users_DbTable();
                $user = $userModel->getById($this->getUserId());
                if($user->email != $data['email']){
                    $email = $this->getElement('email');
                    $email->addValidator(new Orion_Validate_ValueNotExist('users','email'));   
                }
            }      
        } else {
            if($data['email']){
                $email = $this->getElement('email');
                $email->addValidator(new Orion_Validate_ValueNotExist('users','email'));   
            }
        }
        if(@$data['confirm_password']){
            $confirm = $this->getElement('confirm_password');
            $confirm->addValidator(new Zend_Validate_Identical($data['password']));
        }
        return parent::isValid($data);
    }
   	
    public static function getStatus()
    {
        $data = array(
            App_Model_Users_DbTable::STATUS_INACTIVE => 'FORM_ADMIN_USERS_STATUS_INACTIVE',
            App_Model_Users_DbTable::STATUS_ACTIVE => 'FORM_ADMIN_USERS_STATUS_ACTIVE',
            App_Model_Users_DbTable::STATUS_BANNED => 'FORM_ADMIN_USERS_STATUS_BANNED',
        );
        return $data;
    }
	
	public static function getRole()
    {
        $data = array(
            App_Model_Users_DbTable::ROLE_ADMIN => 'FORM_AMIN_USERS_ROLE_ADMIN',
            App_Model_Users_DbTable::ROLE_SADMIN => 'FORM_AMIN_USERS_ROLE_SADMIN',
            App_Model_Users_DbTable::ROLE_DEVELOPER => 'FORM_AMIN_USERS_ROLE_DEVELOPER',

        );
        return $data;
    }
    
    public static function getType()
    {
        $data = array(
            0 => 'FORM_ADMIN_USERS_TYPE_NORMAL',
            1 => 'FORM_ADMIN_USERS_TYPE_PREMIUM',
        );
        return $data;
    }

}