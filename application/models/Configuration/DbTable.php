<?php

class App_Model_Configuration_DbTable extends Orion_Model_DbTable
{
    protected $_name = 'configuration';

    const PAGE_TYPE_MOBILE = 'mobile';
    const PAGE_TYPE_NORMAL = 'normal';
    
    public function isCron()
    {
        $select = $this->select()->where('name = "cron"');
        $cron = $this->fetchRow($select);
        return $cron->value;
    }
    
    public function getDataForm()
    {
        $select = $this->select()->from(array('c' => $this->_name),array('name','value'));
        $data = $this->fetchAll($select);
        foreach ($data as $value){
            $form[$value['name']] = $value['value'];
        }
        return $form;
    }
    
    public function getValue($name)
    {
        $select = $this->select()->from(array('c' => $this->_name),array('name', 'value'))
                       ->where('name = ?',$name);
        $logo = $this->fetchRow($select);
        return $logo->value;
    }
}