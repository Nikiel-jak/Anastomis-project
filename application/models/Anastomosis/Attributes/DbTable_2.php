<?php

class App_Model_Anastomosis_Attributes_DbTable extends Orion_Model_DbTable
{
    protected $_name = 'anastomosis_attributes';
    protected $_primary  = array('id');
    
    public function getByAnastomosisId($id)
    {
        $select = $this->select()->where('anastomosis_id = ?',$id);
        return $this->fetchAll($select);
    }
    
    
    public function getAttributesRelation()
    {
        $ana = array();

        $select = $this->select()->from(array('aa' => $this->_name))
                       ->setIntegrityCheck(false)
                       ->join(array('a' => 'anastomosis'),'aa.anastomosis_id = a.id',array('name','type'))
                       ->where('a.status = ?',App_Model_Anastomosis_DbTable::STATUS_ACTIVE)
                       ->where('a.deleted = ?',App_Model_Anastomosis_DbTable::DELETED_NO);
        $resuls =  $this->fetchAll($select);
        foreach($resuls as $val){
            if(in_array($val->attribute_id, array(10, 12, 13, 14, 15))){
                $ana[$val->anastomosis_id][$val->attribute_id] = $val->value;
            }
        }
        return $ana;
    }
    
    
    
    public function search($data)
    {
        
        $ana = array();
        $name = array();
        $group= array();
        $select = $this->select()->from(array('aa' => $this->_name))
                       ->setIntegrityCheck(false)
                       ->join(array('a' => 'anastomosis'),'aa.anastomosis_id = a.id',array('name','type'))
                       ->where('a.status = ?',App_Model_Anastomosis_DbTable::STATUS_ACTIVE)
                       ->where('a.deleted = ?',App_Model_Anastomosis_DbTable::DELETED_NO)
                ->order(array('aa.value ASC'));
        $resuls =  $this->fetchAll($select);
        foreach($resuls as $val){
            
            //klasa ochrony i bezpieczeństwa
            if(in_array($val->attribute_id, array(40,41))){
                if (empty($ana[$val->name][$val->attribute_id])) {
                    $ana[$val->name][$val->attribute_id] = array();
                }
                $ana[$val->name][$val->attribute_id][] = $val->value;
            }
            else {
                $ana[$val->name][$val->attribute_id] = $val->value;
            }
                $group[$val->name] = $val->type;
           // }
        }
        $to_del = array();
        
        // 
        foreach($ana as $vc => $c){
            if(!empty($data[43]) && !empty($data[45]) && !empty($c[43]) && !empty($c[45]) ){

                if(((@$data[43]['min'] <= $c[43] && @$data[43]['max'] >= $c[43]) && (@$data[45]['min'] <= $c[45] && @$data[45]['max'] >= $c[45])) ||
                        ((@$data[43]['min'] <= $c[45] && @$data[43]['max'] >= $c[45]) && (@$data[45]['min'] <= $c[43] && @$data[45]['max'] >= $c[43]))){
                }
                else {
                    $to_del[] = $vc;
                }
            }
            else if(!empty($data[43]) && !empty($c[43]) ){
                if(@$data[43]['min'] <= $c[43] && @$data[43]['max'] >= $c[43]){
                    
                }
                else {
                    $to_del[] = $vc;
                }
            }
        }
        
        foreach($ana as $vc => $c){
            
            if($c != null){
                $tmp = true;
                foreach($c as $k => $t){
                    //klasa ochrony i bezpieczeństwa - obie ograniczone od góry i od dołu
                    if(in_array($k, array(40,41)) && !empty($data[$k]) ){
                        $tmp2 = false;
                        //jeżeli któryś z elementów z tabeli jest w zakresie to spełnia
                        foreach($t as $k1 => $t1) {
                            if(!empty($t1) && (@$data[$k]['min'] <= $t1 && @$data[$k]['max'] >= $t1) ){
                                $tmp2 = true;
                                break;
                            }   
                        }
                        $tmp = $tmp2 && $tmp;
                    }
                    else if(in_array($k, array(43,45)) && !empty($data[43]) && !empty($data[45])){
                        
                    }
                    else if (is_array(@$data[$k])) {
                         if(!empty($t) && (@$data[$k]['min'] > $t || @$data[$k]['max'] < $t) ){
                            $tmp = false;
                            //echo $t;
                         }    
                    }
                    else if(!empty($t) && @$data[$k] > $t){
                        $tmp = false;
                    }
                }
                
            }
            else{
                $tmp = false;
            }
            if(in_array($vc, $to_del)){
                $tmp = false;
            }
            
            if($tmp){
                $name[$group[$vc]][]['name'] = $vc;
            } 
            else {
            }
        }
        krsort($name);
        return $name;
    }
    
    public function clear($id)
    {
        $this->delete('anastomosis_id = '.$id);
    }
}