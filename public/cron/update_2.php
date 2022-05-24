<?php
require dirname(__FILE__) . '/../../application/Environment.php';
$application->bootstrap(array(
    'autoloader', 'config', 'db', 'view', 'routing', 'log'
));

$model = new App_Model_Anastomosis_DbTable();
$select = $model->select()
    ->from(array('a' => 'anastomosis'), array('pdf_pl', 'pdf_en', 'pdf_fr', 'pdf_de', 'item_name' => 'a.name', 'item_type' => 'a.type', 'item_id' => 'a.id', 'aa.value', 'attribute_name' => 'at.name'))
    ->setIntegrityCheck(false)
    ->join(array('aa' => 'anastomosis_attributes'), 'aa.anastomosis_id = a.id')
    ->join(array('at' => 'attributes'), 'at.id = aa.attribute_id')
    ->where('a.deleted = ?', App_Model_Anastomosis_DbTable::DELETED_NO)
    ->where('a.status = ?', App_Model_Anastomosis_DbTable::STATUS_ACTIVE)
    ->where('a.language_id = ?', 1);
$rows = $model->fetchAll($select);

$items = [];
foreach ($rows as $row) {
    if ($row['item_type'] != 2) {
        continue;
    }
    $attribName = strip_tags($row['attribute_name']);
    if (in_array($attribName, ['1', '2', '3'])) {
        $attribName = 'glass_' . $attribName;
    }
    $attribName = preg_replace(
        ['/help/', '/ /', '/,/', '/Grubość_całkowita_pakietu/', '/Waga_pakietu_1m2/'],
        ['', '_', '_', 'total_thickness', 'total_glass_weight'],
        $attribName
    );
    if (isset($items[$row['item_id']]['OITC'], $items[$row['item_id']]['STC']) === false) {
        $items[$row['item_id']]['OITC'] = '0';
        $items[$row['item_id']]['STC'] = '0';
    }
    if (is_numeric(substr($attribName, 0, 1))) {
        $attribName = 'value_' . $attribName;
    }
    $items[$row['item_id']][$attribName] = $row['value'];
    $items[$row['item_id']]['name'] = $row['item_name'];
    $items[$row['item_id']]['type'] = $row['item_type'];
    $items[$row['item_id']]['gas_type'] = '-';

    if (in_array($row['item_type'], [App_Model_Anastomosis_DbTable::TYPE_COMPLEX_ONE_CHAMBER, App_Model_Anastomosis_DbTable::TYPE_COMPLEX_TWO_CHAMBER])) {
        $exploded = explode('/', $row['item_name']);

        $value = preg_replace(['/Termo/'], [''], $exploded[1]);
        $items[$row['item_id']]['spacer_1'] = $value;
        $items[$row['item_id']]['gas_type'] = 'Argon';
        if ($row['item_type'] == App_Model_Anastomosis_DbTable::TYPE_COMPLEX_TWO_CHAMBER) {
            $value = preg_replace(['/Termo/'], [''], $exploded[3]);
            $items[$row['item_id']]['spacer_2'] = $value;
            $items[$row['item_id']]['gas_type'] = 'Argon';
        }
    }
}

$counter = 0;
$results = [];
if (($handle = fopen(PUBLIC_PATH . '/upload/2.csv', "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if ($counter === 0) {
            $columnName = $data;
            $counter++;
            continue;
        }

        $val = 0;
        $results[$data[7]] = $val;
        foreach ($items as $id => $item) {
            if (strtolower(trim($item['name'])) == strtolower(trim($data[7]))) {
                $val++;
                $results[$item['name']] = $val;
                if (empty($data[25]) === false) {
                    $info = pathinfo($data[25]);

                    $content = file_get_contents($data[25]);
                    file_put_contents(UPLOAD_PATH . '/pdf/en/'. $info['basename'], $content);
                    $model->update(['pdf_en' => $info['basename']], 'id = '. $id);

                }
                if (empty($data[26]) === false) {
                    $info = pathinfo($data[26]);

                    $content = file_get_contents($data[26]);
                    file_put_contents(UPLOAD_PATH . '/pdf/pl/'. $info['basename'], $content);
                    $model->update(['pdf_pl' => $info['basename']], 'id = '. $id);

                }

            }
        }
        $counter++;
    }
}
echo 'TOTAL ITEMS: ' . count($items) . '<br/>';
echo 'NO FOUND:<br/>';
foreach ($items as $id => $item) {
    if (array_key_exists($item['name'], $results) === false) {
        echo $item['name'] . ' #' . $id. '<br/>';
    }
}
echo 'MATCHES: <br/>';
var_dump($results);