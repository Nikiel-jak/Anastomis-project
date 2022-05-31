<?php
require dirname(__FILE__).'/../../application/Environment.php';
$application->bootstrap(array(
    'autoloader', 'config', 'db', 'smtp', 'view' , 'routing','log','session'
));

$db = Zend_Db_Table_Abstract::getDefaultAdapter();
$sql = "SELECT a.id, a.name, max(aa.value) as max, min(aa.value) as min FROM `attributes` AS a
LEFT JOIN anastomosis_attributes AS aa ON a.id = aa.attribute_id
GROUP BY a.name;";
$stmt = $db->query($sql);
$attributes =  $stmt->fetchAll();
try {
    foreach ($attributes as $attribute) {
//    var_dump($attribute);
//    'UPDATE attributes SET min = "' . $attribute->min . '", max = "' . $attribute->max .'" WHERE id  = "' . $attribute->id . '"';
        $data = [];
        if ($attribute->min !== null) {
            $data['min'] = $attribute->min;
        }
        if ($attribute->max !== null) {
            $data['max'] = $attribute->max;
        }

        if (count($data)){
            $db->update('attributes', $data, ['id = ?' => $attribute->id]);
        }

    }
} catch (Exception $exception) {
    echo $exception;
}

echo 'END';
