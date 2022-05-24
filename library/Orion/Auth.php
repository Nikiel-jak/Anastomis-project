<?php

class Orion_Auth extends Zend_Auth
{   
    const ROLE_GUEST = 'guest';
    const ROLE_MEMBER = 'member';
    const ROLE_ADMIN = 'admin';
    const ROLE_SADMIN = 'sadmin';
    const ROLE_DEVELOPER = 'developer';

    const GUEST_PRIORITY = '1';
    const MEMBER_PRIORITY = '2';
    const ADMIN_PRIORITY = '3';
    const SADMIN_PRIORITY = '4';
    const DEVELOPER_PRIORITY = '5';
    
    protected static $_profile = null;
    
    protected static $_priority = array(
        self::ROLE_GUEST => self::GUEST_PRIORITY,
        self::ROLE_MEMBER  => self::MEMBER_PRIORITY,
        self::ROLE_ADMIN => self::ADMIN_PRIORITY,
        self::ROLE_SADMIN => self::SADMIN_PRIORITY,
        self::ROLE_DEVELOPER => self::DEVELOPER_PRIORITY
    );
    
    public static function getProfileId()
    {
        return self::getProfile()->id;
    }

    public static function getProfile()
    {
        $auth = Zend_Auth::getInstance()->getIdentity();
        $userModel = new App_Model_Users_DbTable();
        if(!$auth){
            self::$_profile = new stdClass();
            self::$_profile->id = 0;
            self::$_profile->role = self::ROLE_GUEST;
        } else {
            if (self::$_profile == null) {
                self::$_profile = new stdClass();
            }
            self::$_profile = $userModel->findById(@$auth->id);
        }
        return self::$_profile;  
    }
    
    public static function isLogged()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->getIdentity()){
            return true;
        } else {
            return false;
        }
    }
    
    public static function isAdminSession()
    {
        return self::$_admin == true;
    }
    
    public static function getProfileRole($realm = null)
    {   
        if(self::getProfile()){
            return self::getProfile($realm)->role;
        } else {
            return self::ROLE_GUEST;
        }
    }
    
    public static function getRolePriority($role)
    {
        return self::$_priority[$role];
    }
    
    public static function isDeveloper()
    {
        $role = self::getProfileRole();
        if($role == self::ROLE_DEVELOPER && self::getRolePriority($role) == self::DEVELOPER_PRIORITY) {
            return true;
        } else {
            return false;
        }
    }

    public static function isSuperAdmin()
    {
        if(self::getRolePriority(self::getProfileRole()) > self::ADMIN_PRIORITY) {
            return true;
        } else {
            return false;
        }
    }

    public static function isAdmin()
    {
        if(self::getRolePriority(self::getProfileRole()) == self::ADMIN_PRIORITY) {
            return true;
        } else {
            return false;
        }
    }
    
    public static function getLastFailedLogIn()
    {
        $lastfaildloginin = self::getProfile()->last_failed_log_in;
    	if($lastfaildloginin){
    	   return $lastfaildloginin;
    	} else {
    	   return '';
    	}
    }
    
    public static function getLastLogIn()
    {
        $lastloginin = self::getProfile()->last_log_in;
    	if($lastloginin){
    	   return $lastloginin;
    	} 
        return false;
    }
}