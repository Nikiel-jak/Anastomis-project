<?php

class Site_ApiController extends Zend_Controller_Action
{
    public function dataAction()
    {
        $cache = $this->getCache();
        if (!($api = $cache->load('api'))) {
            $api = $this->getDataForApi();
            $cache->save($api, 'api');
        }

        $this->_helper->json->getResponse()->setHeader('Access-Control-Allow-Origin', '*');
        $this->_helper->json->getResponse()->setHeader('Access-Control-Allow-Methods', 'POST, GET, DELETE, PUT, PATCH, OPTIONS');
        $this->_helper->json->getResponse()->setHeader('Access-Control-Allow-Headers', 'Content-Type');
        return $this->_helper->json->sendJson(
            [
                'total_count' => count($api['items']),
                'filters' => $api['filters'],
                'items' => array_values($api['items'])
            ]
        );
    }

    public function csvAction()
    {
        $cache = $this->getCache();
        if (!($api = $cache->load('api'))) {
            $api = $this->getDataForApi();
            $cache->save($api, 'api');
        }
        $output = fopen("php://output", 'w') or die("Can't open php://output");
        header("Content-Type:application/csv");
        header("Content-Disposition:attachment;filename=zespolenia.csv");
        fputcsv($output, [
            'Nazwa',
            'Typ',
            'Gas',
            'PDF EN',
            'PDF FR',
            'PDF DE',
            'PDF PL',
            'Szyba 1',
            'Ramka 1',
            'Szyba 2',
            'Ramka 2',
            'Szyba 3',
            'Nazwa Us',
            'OITC',
            'STC',
            'Rw',
            'C',
            'Ctr',
            'RA',
            'RAtr',
            'Całkowita grubość',
            'Szyba laminowana',
            'Klasa szyby laminowanej ochronej',
            'Klasa szyby laminowanej bezpiecznej',
            'Całkowita waga szkła',
            '50',
            '63',
            '80',
            '100',
            '125',
            '160',
            '200',
            '250',
            '315',
            '400',
            '500',
            '630',
            '800',
            '1k',
            '1,25k',
            '1,6k',
            '2k',
            '2,5k',
            '3,15k',
            '4k',
            '5k',
        ]);
        foreach ($api['items'] as $product) {
            $row = array_values($product);
            switch ($row[1]) {
                case 'table.value.triple_IGU':
                    $row[1] = 'Triple IGU';
                    break;
                case 'table.value.double_IGU':
                    $row[1] = 'Double IGU';
                    break;
                case 'table.value.single_glass':
                    $row[1] = 'Single Glass';
                    break;
            }
            switch ($row[21]) {
                case 'table.value.1':
                    $row[21] = 'NIE WYSTĘPUJE';
                    break;
                case 'table.value.2':
                    $row[21] = 'WYSTĘPUJE';
                    break;
            }
            switch ($row[22]) {
                case '1':
                    $row[22] = 'BRAK KLASY';
                    break;
                case '2':
                    $row[22] = 'P1A';
                    break;
                case '3':
                    $row[22] = 'P2A';
                    break;
                case '4':
                    $row[22] = 'P3A';
                    break;
                case '5':
                    $row[22] = 'P4A';
                    break;
            }
            switch ($row[23]) {
                case '1':
                    $row[23] = 'BRAK KLASY';
                    break;
                case '2':
                    $row[23] = '2B2';
                    break;
                case '3':
                    $row[23] = '1B1';
                    break;
            }
            fputcsv($output, array_values($row));
        }
        fclose($output) or die("Can't close php://output");
        die;
    }

    public function dataUsAction()
    {
        $cache = $this->getCache();
        if (!($api = $cache->load('api_us'))) {
            $api = $this->getDataForApi();
            $api['items'] = array_filter($api['items'], function ($v, $k) {
                return $v['OITC'] != 0 && $v['STC'] != 0;
            }, ARRAY_FILTER_USE_BOTH);
            unset($api['filters']['STC'][0]);
            unset($api['filters']['OITC'][0]);
            $cache->save($api, 'api_us');
        }

        $this->_helper->json->getResponse()->setHeader('Access-Control-Allow-Origin', '*');
        $this->_helper->json->getResponse()->setHeader('Access-Control-Allow-Methods', 'POST, GET, DELETE, PUT, PATCH, OPTIONS');
        $this->_helper->json->getResponse()->setHeader('Access-Control-Allow-Headers', 'Content-Type');
        return $this->_helper->json->sendJson(
            [
                'total_count' => count($api['items']),
                'filters' => $api['filters'],
                'items' => array_values($api['items'])
            ]
        );
    }

    private function getCache()
    {
        $frontendOptions = array(
            'lifetime' => 60 * 60 * 24,
            'automatic_serialization' => true,
        );
        $backendOptions = array(
            'cache_dir' => APPLICATION_PATH . '/cache/',
            'file_name_prefix' => 'zend_cache_query',
            'hashed_directory_level' => 2,
        );
        return Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
    }

    public function translateAction()
    {
        $languageSwitcher = new Orion_Language();
        $translateDataFromCsv = $languageSwitcher->getCsvTranslate($this->_getParam('lang'));
        $this->_helper->json->getResponse()->setHeader('Access-Control-Allow-Origin', '*');
        $this->_helper->json->getResponse()->setHeader('Access-Control-Allow-Methods', 'POST, GET, DELETE, PUT, PATCH, OPTIONS');
        $this->_helper->json->getResponse()->setHeader('Access-Control-Allow-Headers', 'Content-Type');

        $tableTranslate = [];
        foreach ($translateDataFromCsv as $key => $value) {
            if (stristr($value[0], 'table')) {
                $tableTranslate[$value[0]] = $value[1];
            }
        }

        return $this->_helper->json->sendJson($tableTranslate);
    }

    /**
     * @return array
     */
    private function getDataForApi()
    {
        $model = new App_Model_Anastomosis_DbTable();
        $select = $model->select()
            ->from(
                ['a' => 'anastomosis'],
                [
                    'pdf_pl',
                    'pdf_en',
                    'pdf_fr',
                    'pdf_de',
                    'item_name' => 'a.name',
                    'item_us_name' => 'a.us_name',
                    'item_type' => 'a.type',
                    'item_id' => 'a.id',
                    'aa.value',
                    'attribute_name' => 'at.name'
                ]
            )
            ->setIntegrityCheck(false)
            ->join(array('aa' => 'anastomosis_attributes'), 'aa.anastomosis_id = a.id')
            ->join(array('at' => 'attributes'), 'at.id = aa.attribute_id')
            ->where('a.deleted = ?', App_Model_Anastomosis_DbTable::DELETED_NO)
            ->where('a.status = ?', App_Model_Anastomosis_DbTable::STATUS_ACTIVE)
            ->where('a.language_id = ?', Orion_Language::getContentId());
        $rows = $model->fetchAll($select);

        $items = [];
        $filters = [];
        foreach ($rows as $row) {
            if (isset($items[$row['item_id']]) === false) {
                $items[$row['item_id']] = [
                    'name' => '',
                    'type' => '',
                    'gas_type' => '',
                    'en' => '',
                    'fr' => '',
                    'de' => '',
                    'pl' => '',
                    'glass_1' => '',
                    'spacer_1' => '',
                    'glass_2' => '',
                    'spacer_2' => '',
                    'glass_3' => '',
                    'item_us_name' => '',
                    'OITC' => '',
                    'STC' => '',
                    'Rw' => '',
                    'C' => '',
                    'Ctr' => '',
                    'RA' => '',
                    'RAtr' => '',
                    'total_thickness' => '',
                    'Szyba_Laminowana' => '',
                    'Klasa_szyby_laminowanej_ochronej' => '',
                    'Klasa_szyby_laminowanej_bezpiecznej' => '',
                    'total_glass_weight' => '',
                    'value_50' => '',
                    'value_63' => '',
                    'value_80' => '',
                    'value_100' => '',
                    'value_125' => '',
                    'value_160' => '',
                    'value_200' => '',
                    'value_250' => '',
                    'value_315' => '',
                    'value_400' => '',
                    'value_500' => '',
                    'value_630' => '',
                    'value_800' => '',
                    'value_1k' => '',
                    'value_1_25k' => '',
                    'value_1_6k' => '',
                    'value_2k' => '',
                    'value_2_5k' => '',
                    'value_3_15k' => '',
                    'value_4k' => '',
                    'value_5k' => '',
                ];
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
                $items[$row['item_id']]['OITC'] = '-';
                $items[$row['item_id']]['STC'] = '-';
            }
            if (is_numeric(substr($attribName, 0, 1))) {
                $attribName = 'value_' . $attribName;
            }
            $items[$row['item_id']][$attribName] = $row['value'];
            if (empty($items[$row['item_id']]['OITC']) === true) {
                $items[$row['item_id']]['OITC'] = '-';
            }
            $items[$row['item_id']]['name'] = $row['item_name'];
            $items[$row['item_id']]['item_us_name'] = $row['item_us_name'];
            $items[$row['item_id']]['en'] = $row['pdf_en'];
            $items[$row['item_id']]['fr'] = $row['pdf_fr'];
            $items[$row['item_id']]['de'] = $row['pdf_de'];
            $items[$row['item_id']]['pl'] = $row['pdf_pl'];
            $type = App_Model_Anastomosis_DbTable::getTypeByIndex($row['item_type']);
            $items[$row['item_id']]['type'] = $type;
            $items[$row['item_id']]['gas_type'] = '-';
            $items[$row['item_id']]['spacer_1'] = '-';
            $items[$row['item_id']]['spacer_2'] = '-';
            $filters['type'][$type] = $type;
            if (in_array($row['item_type'], [App_Model_Anastomosis_DbTable::TYPE_COMPLEX_ONE_CHAMBER, App_Model_Anastomosis_DbTable::TYPE_COMPLEX_TWO_CHAMBER])) {
                $exploded = explode('/', $row['item_name']);

                $value = preg_replace(['/Termo/'], [''], $exploded[1]);
                $filters['spacer_1'][$value] = $value;
                $items[$row['item_id']]['spacer_1'] = $value;
                $items[$row['item_id']]['gas_type'] = 'Argon';
                if ($row['item_type'] == App_Model_Anastomosis_DbTable::TYPE_COMPLEX_TWO_CHAMBER) {
                    $value = preg_replace(['/Termo/'], [''], $exploded[3]);
                    $filters['spacer_2'][$value] = $value;
                    $items[$row['item_id']]['spacer_2'] = $value;
                    $items[$row['item_id']]['gas_type'] = 'Argon';
                }
            }

            if ($attribName === 'glass_2') {
                $items[$row['item_id']]['glass_2'] = in_array($items[$row['item_id']]['glass_2'], [0, ''])
                    ? '-'
                    : $items[$row['item_id']]['glass_2'];
            }
            if ($attribName === 'glass_3') {
                $items[$row['item_id']]['glass_3'] = in_array($items[$row['item_id']]['glass_3'], [0, ''])
                    ? '-'
                    : $items[$row['item_id']]['glass_3'];
            }

            if (in_array($attribName, ['Rw', 'C', 'Ctr', 'RA', 'RAtr', 'STC', 'OITC', 'glass_1', 'glass_2', 'glass_3'])) {
                if (in_array($row['value'], [null, ''], true) === false) {
                    $filters[$attribName][$row['value']] = $row['value'];
                }
            }
            if (in_array($attribName, ['Szyba_Laminowana'])) {
                $items[$row['item_id']][$attribName] = 'table.value.' . $row['value'];
                $filters[$attribName]['table.value.' . $row['value']] = 'table.value.' . $row['value'];
            }
        }

        return [
            'items' => $items,
            'filters' => $filters,
        ];
    }
}