<?php

class App_Model_Users_DbTable extends Orion_Model_DbTable
{  	
    protected $_name = 'users';

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_BANNED = 2;
    const STATUS_DELETED = 3;
	
    const ROLE_DEVELOPER = 'developer';
    const ROLE_SADMIN = 'sadmin';
    const ROLE_ADMIN = 'admin';
    const ROLE_MEMBER = 'member';
    const ROLE_GUEST = 'quest';
    
    protected $_provinces = array(
        1 => 'COMMON_FORM_PROVINCE_1',
        2 => 'COMMON_FORM_PROVINCE_2',
        3 => 'COMMON_FORM_PROVINCE_3',
        4 => 'COMMON_FORM_PROVINCE_4',
        5 => 'COMMON_FORM_PROVINCE_5',
        6 => 'COMMON_FORM_PROVINCE_6',
        7 => 'COMMON_FORM_PROVINCE_7',
        8 => 'COMMON_FORM_PROVINCE_8',
        9 => 'COMMON_FORM_PROVINCE_9',
        10 => 'COMMON_FORM_PROVINCE_10',
        11 => 'COMMON_FORM_PROVINCE_11',
        12 => 'COMMON_FORM_PROVINCE_12',
        13 => 'COMMON_FORM_PROVINCE_13',
        14 => 'COMMON_FORM_PROVINCE_14',
        15 => 'COMMON_FORM_PROVINCE_15',
        16 => 'COMMON_FORM_PROVINCE_16',
    );
    
    public static function getAvaliableStatus()
    {
        return array(
            self::STATUS_ACTIVE => 'aktywny',
            self::STATUS_INACTIVE => 'nieaktywny',
        );
    }

    public function SetFailedLogIn($params)
    {   
        $data = array(
            'last_failed_log_in' => new Zend_Db_Expr('NOW()'),
        );
        $where = $this->getAdapter()->quoteInto('email = ?', $params['login']);
        $this->update($data, $where);  
    }

    public function SetLastLogIn($params)
    {   
        $user = $this->getByEmail($params['login']);
        $where = $this->getAdapter()->quoteInto('email = ?', $params['login']);
        $data = array(
            'last_memory_log_in' => new Zend_Db_Expr('NOW()'),
            'last_log_in' => $user->last_memory_log_in,
        );
        $this->update($data, $where);  
    }
    
    public function getByEmail($email)
    {
        $select = $this->select()->where('email = ?',$email);
        return $this->fetchRow($select);
    }
    
    public function setNewUser($data)
    {   
        $data['status'] = 0;
        $data['type'] = 0;
        $data['password'] = sha1($data['password']);
        return $this->insert($data);
    }
    
    public function insert(array $data)
    {
        $data['created_by'] = Orion_Auth::getProfileId();
        $data['created_at'] = new Zend_Db_Expr('NOW()');
        return  parent::insert($data);
    }
    
    public function getUserToDelete()
    {
        $select = $this->select()->from(array('u' => $this->_name))
                       ->where('status != ?',self::STATUS_DELETED)
                       ->where('status = ?',self::STATUS_INACTIVE)
                       ->where('created_at < ? ',date('Y-m-d',strtotime(date('Y-m-d')." -1 month ")))
                       ->limit(100);
        return $this->fetchAll($select);    
    }

    public function getProvinces()
    {
        return $this->_provinces;
    }
    
    public function getRoomMates()
    {   
        $data = array();
        $select = $this->select()->from(array('u' => 'users'))
                       ->setIntegrityCheck(false)
                       ->join(array('ur' => 'users_rooms'),'ur.user_id = u.id AND ur.status = 1');
        $results = $this->fetchAll($select);
        foreach($results as $res){
            $data[$res->room_id][] = $res;
        }
        return $data;
    }
    
    public function setStatusActive($ids)
    {
        if(is_array($ids)){
            $ids= implode(',', $ids);
        }
        $where = 'id IN ('.$ids.')';
        return $this->update(array('status' => self::STATUS_ACTIVE), $where);
    }
    
    public function setStatusInactive($ids)
    {
        if(is_array($ids)){
            $ids= implode(',', $ids);
        }
        $where = 'id IN ('.$ids.')';
        return $this->update(array('status' => self::STATUS_INACTIVE), $where);
    }

    public function setDeleted($ids)
    {
        if(is_array($ids)){
            $ids= implode(',', $ids);
        }
        $where = 'id IN('.$ids.')';
        return $this->update(array('status' => self::STATUS_DELETED), $where);
    }
    
    public function getAll($sql = null)
    {
        $select = $this->select()->where('status != ?',self::STATUS_DELETED);
        if($sql){
            return $select; 
        }
        return $this->fetchAll($select);
    }
}