<?php
error_reporting(E_ALL);


class App_Model_Anastomosis_Attributes_DbTable extends Orion_Model_DbTable
{
    protected $_name = 'anastomosis_attributes';
    protected $_primary = array('id');

    public function getByAnastomosisId($id)
    {
        $select = $this->select()->where('anastomosis_id = ?', $id);
        return $this->fetchAll($select);
    }

    public function getAttributesRelation()
    {
        $ana = array();

        $select = $this->select()->from(array('aa' => $this->_name))
            ->setIntegrityCheck(false)
            ->join(array('a' => 'anastomosis'), 'aa.anastomosis_id = a.id', array('name', 'type'))
            ->where('a.status = ?', App_Model_Anastomosis_DbTable::STATUS_ACTIVE)
            ->where('a.deleted = ?', App_Model_Anastomosis_DbTable::DELETED_NO);
        $resuls = $this->fetchAll($select);
        foreach ($resuls as $val) {
            if (in_array($val->attribute_id, array(10, 12, 13, 14, 15))) {
                $ana[$val->anastomosis_id][$val->attribute_id] = $val->value;
            }
        }
        return $ana;
    }


    public function search_new($data) 
    {
        /**
        * #RW = 10
        * #C = 12
        * #CTR = 13
        * #RA = 14
        * #RATR = 15
        * 
        * Szyba laminowana = 39
         */

        $where_rw = "";
        $where_c = "";
        $where_ctr = "";

        $arr = [];


        /**
         * RW
         */
        if (isset($data['10']['max']) && isset($data['10']['min'])) {
                    
            $atr_rw_min = $this->_db->quote($data['10']['min']);
            $atr_rw_max = $this->_db->quote($data['10']['max']);

            $where_rw = " and (att.`attribute_id`=10 and (att.`value` >=".$atr_rw_min." and att.`value` <= ".$atr_rw_max."))";
        }

        /**
         * C
         */
        if (isset($data['12']['max']) && isset($data['12']['min'])) {
                    
            $atr_c_min = $this->_db->quote($data['12']['min']);
            $atr_c_max = $this->_db->quote($data['12']['max']);

            $where_c = " or (att.`attribute_id`=12 and (att.`value` >=".$atr_c_min." and att.`value` <= ".$atr_c_max."))";
        }


        /**
         * Ctr
         */
        if (isset($data['13']['max']) && isset($data['13']['min'])) {
                    
            $atr_ctr_min = $this->_db->quote($data['13']['min']);
            $atr_ctr_max = $this->_db->quote($data['13']['max']);

            $where_ctr = " or (att.`attribute_id`=13 and (att.`value` >=".$atr_ctr_min." and att.`value` <= ".$atr_ctr_max."))";
        }        




        /*echo '<pre>';
            print_r($data);
        echo '</pre>';*/


        echo $q = "select 
                ana.`name`, ana.`type`,
                att.*
                
                from anastomosis ana
                inner join anastomosis_attributes att on ana.`id`=att.`anastomosis_id` WHERE ana.status = 1 and ana.deleted = 2
                and (1=1 ".$where_rw.$where_c.$where_ctr.")                 
                group by att.`anastomosis_id` ORDER BY `att`.`anastomosis_id`, `att`.`attribute_id` ASC";
        $q_exe = $this->_db->query($q);        
        $res = $q_exe->fetchAll();
        foreach ($res as $row) {
            //echo $row->name.'<br />';

            $arr[$row->type][]['name'] = $row->name;

        }
        






        return $arr;



    }



    /**
     * Wyszukiwarka frontend.
     * @param type $data
     * @return type
     */
    public function search($data)
    {

        $arr = []; //Tablica wyjściowa
        $ana = array();
        $name = array();
        $group = array();

        /**
         * Dla wersji amerykańskiej pobieramy kolumnę "us_name".
         */
        if ($_SESSION['language']['page']['prefix'] == "en_US") {
            $anastomosis_tab_cols = array('us_name as name', 'type', 'id');

            /**
             * Powrotne przeliczenie wagi z LB na KG tylko dla wersji amerykańskiej.
             */
            if (isset($data['42'])) {
                $data['42']['min'] = (0.45359237 * $data['42']['min']);
                $data['42']['max'] = (0.45359237 * $data['42']['max']);
            }
        } else {
            $anastomosis_tab_cols = array('name', 'type', 'id');
        }

         $select = $this->select()->from(array('aa' => $this->_name))
            ->setIntegrityCheck(false)
            ->join(array('a' => 'anastomosis'), 'aa.anastomosis_id = a.id', $anastomosis_tab_cols)
            ->where('a.status = ?', App_Model_Anastomosis_DbTable::STATUS_ACTIVE)
            ->where('a.deleted = ?', App_Model_Anastomosis_DbTable::DELETED_NO)
            ->order(array('aa.anastomosis_id ASC'));
        $resuls = $this->fetchAll($select);

        foreach ($resuls as $val) {

            if ($_SESSION['language']['page']['prefix'] == "en_US") {

                /**
                 * Zaokrąglenie wartości w górę lub w dół dla atrybutów należących do Oktaw i Tercji.
                 * Tylko dla wesji US.
                 */
                if (
                    $val->attribute_id == 16 || $val->attribute_id == 19 || $val->attribute_id == 23 ||
                    $val->attribute_id == 26 || $val->attribute_id == 29 || $val->attribute_id == 32 ||
                    $val->attribute_id == 35 || $val->attribute_id == 17 || $val->attribute_id == 20 ||
                    $val->attribute_id == 24 || $val->attribute_id == 27 || $val->attribute_id == 30 ||
                    $val->attribute_id == 33 || $val->attribute_id == 36 || $val->attribute_id == 18 ||
                    $val->attribute_id == 21 || $val->attribute_id == 25 || $val->attribute_id == 28 ||
                    $val->attribute_id == 31 || $val->attribute_id == 34 || $val->attribute_id == 37) {
                    $val->value = round($val->value);
                }
            }

            if (!empty($val->id)) {

                //klasa ochrony i bezpieczeństwa
                if (in_array($val->attribute_id, array(40, 41))) {
                    
                    if (empty($ana[$val->id][$val->attribute_id])) {
                        $ana[$val->id][$val->attribute_id] = array();
                    }

                    $ana[$val->id][$val->attribute_id][] = $val->value;

                } else {
                    $ana[$val->id][$val->attribute_id] = $val->value;
                }

                $group[$val->id] = $val->type;
                //echo 'Attr ID: '.$val->attribute_id.'--'.$val->value.'--'.$val2." \n";
            }
        }
        $to_del = array();

        foreach ($ana as $vc => $c) {

            

            if (!empty($data[43]) && !empty($data[45]) && !empty($c[43]) && !empty($c[45])) {

                if (((@$data[43]['min'] <= $c[43] && @$data[43]['max'] >= $c[43]) && (@$data[45]['min'] <= $c[45] && @$data[45]['max'] >= $c[45])) ||
                    ((@$data[43]['min'] <= $c[45] && @$data[43]['max'] >= $c[45]) && (@$data[45]['min'] <= $c[43] && @$data[45]['max'] >= $c[43]))) {
                } else {
                    $to_del[] = $vc;
                }
            } else if (!empty($data[43]) && !empty($c[43])) {
                if (@$data[43]['min'] <= $c[43] && @$data[43]['max'] >= $c[43]) {

                } else {
                    $to_del[] = $vc;
                }
            }
        }

        foreach ($ana as $vc => $c) {
            foreach ($data as $id => $val_tmp) {
                if (isset($c[$id])) {
                    if ($c != null) {
                        $tmp = true;
                        foreach ($c as $k => $t) {
                            //klasa ochrony i bezpieczeństwa - obie ograniczone od góry i od dołu
                            if (in_array($k, array(40, 41)) && !empty($data[$k])) {
                                $tmp2 = false;
                                //jeżeli któryś z elementów z tabeli jest w zakresie to spełnia
                                foreach ($t as $k1 => $t1) {
                                    if (!empty($t1) && (@$data[$k]['min'] <= $t1 && @$data[$k]['max'] >= $t1)) {
                                        $tmp2 = true;
                                        break;
                                    }
                                }
                                $tmp = $tmp2 && $tmp;
                                if ($tmp == false) {
                                    $to_del[] = $vc;
                                }

                            } else if (in_array($k, array(16, 17, 18)) && !empty($data[$k])) {
                                //echo "MIN: ".$data[$k].'<br />';
                                //echo "SZYBA: ".$t.'<br />';
                                if (@$data[$k] > $t || empty($t)) {

                                    //echo "do usuniecia".'<br />';
                                    $to_del[] = $vc;
                                    $tmp = false;
                                } else {
                                    //echo "ok".'<br />';
                                }
                            } else if (in_array($k, array(43, 45)) && !empty($data[43]) && !empty($data[45])) {

                            } else if (isset($data[$k]) && is_array(@$data[$k])) {
                                if (!empty($t) && (@$data[$k]['min'] > $t || @$data[$k]['max'] < $t)) {
                                    $to_del[] = $vc;
                                    $tmp = false;
                                    //echo $t;
                                }
                            } else if (isset($data[$k]) && !empty($t) && @$data[$k] > $t) {
                                $to_del[] = $vc;
                                $tmp = false;
                            }
                        }
                    } else {
                        $tmp = false;
                    }
                } else {
                    $tmp = false;
                    break;
                }
            }

            if (in_array($vc, $to_del)) {
                $tmp = false;
            }

            if ($tmp) {
                //$name[$group[$vc]][]['name'] = $vc;
                $name[] = $vc;
            } else {

            }
        }

        $where_tmp = "";
        $or = " or ";
        $i = 0;

        foreach ($name as $name_row) {
            $i++;
        
            if ($i == count($name)) {
                $or = "";
            }

            $where_tmp .= "ana.`id`=".$name_row.$or;
        }

        if ($where_tmp != "") {
            $where = "(".$where_tmp.")";
        }

        /**
         * Dla wersji Amerykańskiej sortujmey po parametrach - 
         */
        if ($_SESSION['language']['page']['prefix'] == "en_US") {

            $q = "
            SELECT 
                ana.`id`,
                ana.`name`,
                ana.`type`,
                att.`value` AS rw, 
                CAST(att2.value AS DECIMAL(5,0)) AS c, 
                CAST(att3.`value` AS DECIMAL(5,0)) AS ctr,    
                CAST(att4.`value` AS DECIMAL(5,0)) AS ra,
                CAST(att5.`value` AS DECIMAL(5,0)) AS ratr,
                CAST(att6.`value` AS DECIMAL(5,0)) AS stc,
                CAST(att7.`value` AS DECIMAL(5,0)) AS oitc                  
            FROM
                anastomosis ana,
                anastomosis_attributes att,
                anastomosis_attributes att2,
                anastomosis_attributes att3,
                anastomosis_attributes att4,
                anastomosis_attributes att5,
                anastomosis_attributes att6,
                anastomosis_attributes att7,                
                attributes attr,
                attributes attr2,
                attributes attr3,
                attributes attr4,
                attributes attr5,
                attributes attr6,
                attributes attr7                
            where
                ana.`id` = att.`anastomosis_id`
                and ana.`id` = att2.`anastomosis_id`
                and ana.`id` = att3.`anastomosis_id`
                and ana.`id` = att4.`anastomosis_id`
                and ana.`id` = att5.`anastomosis_id`
                and ana.`id` = att6.`anastomosis_id`
                and ana.`id` = att7.`anastomosis_id`                
                and att.`attribute_id` = attr.`id`
                and att2.`attribute_id` = attr2.`id`
                and att3.`attribute_id` = attr3.`id`
                and att4.`attribute_id` = attr4.`id`
                and att5.`attribute_id` = attr5.`id`  
                and att6.`attribute_id` = attr6.`id`
                and att7.`attribute_id` = attr7.`id`                  
            and
                attr.`id` = 10 AND attr2.`id` = 12 and attr3.`id` = 13 and attr4.`id` = 14 and attr5.`id` = 15 and attr6.`id` = 46 and attr7.`id` = 47
            and ".$where."
            ORDER BY stc asc, oitc asc, ana.`name` asc";

        } else {
            $q = "
            SELECT 
                ana.`id`,
                ana.`name`,
                ana.`type`,
                att.`value` AS rw, 
                CAST(att2.value AS DECIMAL(5,0)) AS c, 
                CAST(att3.`value` AS DECIMAL(5,0)) AS ctr,    
                CAST(att4.`value` AS DECIMAL(5,0)) AS ra,
                CAST(att5.`value` AS DECIMAL(5,0)) AS ratr
            FROM
                anastomosis ana,
                anastomosis_attributes att,
                anastomosis_attributes att2,
                anastomosis_attributes att3,
                anastomosis_attributes att4,
                anastomosis_attributes att5,
                attributes attr,
                attributes attr2,
                attributes attr3,
                attributes attr4,
                attributes attr5    
            where
                ana.`id` = att.`anastomosis_id`
                and ana.`id` = att2.`anastomosis_id`
                and ana.`id` = att3.`anastomosis_id`
                and ana.`id` = att4.`anastomosis_id`
                and ana.`id` = att5.`anastomosis_id`
                and att.`attribute_id` = attr.`id`
                and att2.`attribute_id` = attr2.`id`
                and att3.`attribute_id` = attr3.`id`
                and att4.`attribute_id` = attr4.`id`
                and att5.`attribute_id` = attr5.`id`    
            and
                attr.`id` = 10 AND attr2.`id` = 12 and attr3.`id` = 13 and attr4.`id` = 14 and attr5.`id` = 15     
            and ".$where."
            ORDER BY rw asc,  c asc,  ctr asc, ra asc, ratr asc, ana.`name` asc";
        }
        
        $q_exe = $this->_db->query($q);
        $res = $q_exe->fetchAll();

        if ($_SESSION['language']['page']['prefix'] == "en_US") {
            foreach ($res as $row) {
                $arr[$row->type][]['name'] = $row->name . ' Rw=' . $row->rw . ' (STC=' . $row->stc . ', OITC=' . $row->oitc . ')';
            }
        } else {
            foreach ($res as $row) {
                $arr[$row->type][]['name'] = $row->name . ' Rw=' . $row->rw . ' (C=' . $row->c . ', Ctr=' . $row->ctr . ')';
            }
    }
        return $arr;
    }

    public function clear($id)
    {
        $this->delete('anastomosis_id = ' . $id);
    }
}
