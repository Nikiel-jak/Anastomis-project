<?php

class App_Model_Anastomosis_DbTable extends Orion_Model_DbTable
{
    protected $_name = 'anastomosis';
    protected $_primary  = array('id');
    
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    
    const DELETED_YES = 1;
    const DELETED_NO = 2;
    
    const TYPE_SINGLE = 1;
    const TYPE_COMPLEX_ONE_CHAMBER = 2;
    const TYPE_COMPLEX_TWO_CHAMBER = 3;

    const TYPE_INPUT_TEXT = 1;
    const TYPE_INPUT_SELECT = 2;

    public static function getType()
    {
        return array(
            self::TYPE_SINGLE => 'Szyby pojedyncze',
            self::TYPE_COMPLEX_ONE_CHAMBER => 'Szyby zespolone jednokomorowe',
            self::TYPE_COMPLEX_TWO_CHAMBER => 'Szyby zespolone dwukomorowe',
        );
    }

    public static function getTypeByIndex($index)
    {
        $types = array(
            self::TYPE_SINGLE => 'table.value.single_glass',
            self::TYPE_COMPLEX_ONE_CHAMBER => 'table.value.double_IGU',
            self::TYPE_COMPLEX_TWO_CHAMBER => 'table.value.triple_IGU',
        );

        return $types[$index];
    }
    
    public static function getAvaliableStatus()
    {
        return array(
            self::STATUS_ACTIVE => 'aktywny',
            self::STATUS_INACTIVE => 'nieaktywny',
        );
    }

    public function setStatusActive($ids)
    {
        foreach($ids as $val){
            $where = 'id = '.$val;
            $up[] =$this->update(array('status' => self::STATUS_ACTIVE), $where);
        }
        return $up;
    }
    
    public function setStatusInactive($ids)
    {
        foreach($ids as $val){
            $where = 'id = '.$val;
            $up[] =$this->update(array('status' => self::STATUS_INACTIVE), $where);
        }
        return $up;
    }

    public function setDeleted($ids)
    {
        foreach($ids as $val){
            $where = 'id = '.$val;
            $up[] =$this->update(array('deleted' => self::DELETED_YES), $where);
        }
        return $up;
    }
    
	/**
	 * Dodanie danych - Zespolenia.
	 * @param array $data
	 * @param int $created_by
	 * @param int $language
	 * @return int
	 */
    public function create(array $data, $created_by = null, $language = true)
    {
        $row = $data['row'];
        $data['name'] = $row['name'];
        $data['status'] = $row['status'];
        $data['type'] = $row['type'];
		$data['us_name']=$row['us_name'];
        $data['show_in_us']=$row['show_in_us'];
        $data['pdf_pl'] = $row['pl'];
        $data['pdf_en'] = $row['en'];
        $data['pdf_fr'] = $row['fr'];
        $data['pdf_de'] = $row['de'];
        unset($data['row']);
        unset($row['name']);
        unset($row['status']);
        unset($row['type']);
		unset($row['us_name']);
        unset($row['show_in_us']);
        unset($row['pl']);
        unset($row['en']);
        unset($row['de']);
        unset($row['fr']);
        unset($row['en_remove']);
        unset($row['pl_remove']);
        unset($row['fr_remove']);
        unset($row['de_remove']);
        $id = parent::create($data, $created_by, $language );
        $model = new App_Model_Anastomosis_Attributes_DbTable();
        foreach ($row as $key => $value){
            if (is_array($value)){
                foreach ($value as $vv){
                    if (empty($vv) === true) {
                        $vv = null;
                    }
                    $model->insert(array('attribute_id' => $key, 'value' => $vv, 'anastomosis_id' => $id));
                }
            } else {
                if (empty($value) === true) {
                    $value = null;
                }
                $model->insert(array('attribute_id' => $key, 'value' => $value, 'anastomosis_id' => $id));
            }
        }
        return $id;
    }
    
	/**
	 * Aktualizacja danych - Zespolenia.
	 * @param array $data
	 * @param int $id
	 * @param int $modified_by
	 * @return int
	 */
    public function edit(array $data, $id, $modified_by = null) {
		
        $row = $data['row'];
        $data['name'] = $row['name'];
        $data['status'] = $row['status'];
        $data['type'] = $row['type'];
        $data['us_name']=$row['us_name'];
        $data['show_in_us']=$row['show_in_us'];
        $data['modified_by'] = $modified_by;
        if(!empty($row['pl'])) {
            $data['pdf_pl'] = $row['pl'];
        }
        if(!empty($row['en'])) {
            $data['pdf_en'] = $row['en'];
        }
        if(!empty($row['fr'])) {
            $data['pdf_fr'] = $row['fr'];
        }
        if(!empty($row['de'])) {
            $data['pdf_de'] = $row['de'];
        }

        unset($data['row']);
        unset($row['name']);
        unset($row['status']);
        unset($row['type']);
		unset($row['us_name']);
        unset($row['show_in_us']);
		unset($row['pl']);
		unset($row['en']);
		unset($row['de']);
		unset($row['fr']);
		if ($row['pl_remove'] == 1) {
            $data['pdf_pl'] = null;
        }
		if($row['en_remove'] == 1) {
            $data['pdf_en'] = null;
        }
		if($row['fr_remove'] == 1) {
            $data['pdf_fr'] = null;
        }
		if($row['de_remove'] == 1) {
            $data['pdf_de'] = null;
        }
        unset($row['en_remove']);
        unset($row['pl_remove']);
        unset($row['fr_remove']);
        unset($row['de_remove']);


        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $model = new App_Model_Anastomosis_Attributes_DbTable();
        $model->clear($id);
//        var_dump($row);
//        die();
        foreach($row as $key => $value){
            if(is_array($value)){
                foreach($value as $vv){
                    if (empty($vv) === true) {
                        $vv = null;
                    }
                    $model->insert(array('attribute_id' => $key, 'value' => $vv, 'anastomosis_id' => $id));
                }
            } else {
                if (empty($value) === true) {
                    $value = null;
                }
                $model->insert(array('attribute_id' => $key, 'value' => $value, 'anastomosis_id' => $id));
            }
        }

        return $this->update($data, $where);
    }
    
    public function getAll($sql = null)
    {
        $select = $this->select()->where('deleted = ?',self::DELETED_NO);
        if($sql){
            return $select; 
        }
        return $this->fetchAll($select);
    }
}