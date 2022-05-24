<?php

class Admin_AnastomosisController extends Zend_Controller_Action {
	
	/**
	 * Widok - "Lista zespoleń".
	 */
    public function indexAction() {    
        $request = $this->getRequest();
        $model = new App_Model_Anastomosis_DbTable();
        $listview = new Orion_ListView('anastomosis', array(
            'select' => $model->getAll(true),
            'columns' => array(
                'checkbox' => array('__special__', new Orion_ListView_SpecialColumn_Checkbox(array('id' => '{id}','name' => 'ids'))),
                'name' => array('name'),
                'type' => array('type',array(new Orion_ListView_Transformer_Translate('ANA_'))),
                'status' => array('status', array(new Orion_ListView_Transformer_Status()), array()),
                'created_at' => array('created_at',array(new Orion_ListView_Transformer_Date('Y-m-d'))),
                'edit' => array('__special__', new Orion_ListView_SpecialColumn_Edit(array('id' => '{id}'), 'admin-anastomosis-edit')),
            ),
            'invisibleColumns' => array(
                'id' => array('id')
            ),
            'buttons' => array(
                array('add', 'btn','admin-anastomosis-add')
            ),
            'forms' =>  array(
                'options' => array('method' => 'Post', 'action' => 'admin-anastomosis-update','class' => 'autoVisible', 'params' => array()),
                'submits' => array(
                    array('active',array(),'btn btn-success'),
                    array('deactive',array(),'btn btn-danger'),
                    array('delete',array(),'btn btn-inverse'),
                )
            ),
            'filters' => array(
                new Orion_ListView_Filter_Select('status', 'status', App_Model_Anastomosis_DbTable::getAvaliableStatus(), 'COMMON_FILTER_STATUS_SELECT'),
                new Orion_ListView_Filter_Between_Date('created_at', 'created_at', 13, 'DATE')
            ),
            'search' => array('name'),
            'defaultSort' => array('created_at', false),
            'sortableColumns' => array('status','name','created_at','type'),
            'itemsPerPage' => 25,
            'itemsPerPageOptions' => array(5,10,15,20,25),
            'numberColumn' => array()
        ));
        $listview->init($request->getPost());
        $this->view->listview = $listview->build();
    }
    
    
	/**
	 * Widok dodania "Zespolenia".
	 * @return type
	 */
    public function addAction() {

        $frontendOptions = array(
            'lifetime' => 60 * 60 * 24,
            'automatic_serialization' => true,
        );
        $backendOptions = array(
            'cache_dir' => APPLICATION_PATH . '/cache/',
            'file_name_prefix' => 'zend_cache_query',
            'hashed_directory_level' => 2,
        );
        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        $cache->clean();

        $form = new App_Form_Admin_Anastomosis();
        $request = $this->getRequest();
		
		/**
		 * Dodanie danych. Zapis w bazie.
		 */
        if($request->isPost() && $form->isValid($request->getPost())){
            
			$data = $form->getValues();
            $model = new App_Model_Anastomosis_DbTable();
            $adapter = $model->getAdapter();
            $adapter->beginTransaction();
            
			try{
                $model->create($data);
                $adapter->commit();
                Orion_FlashMessenger::addSuccess('ADMIN_SLIDER_EDIT_SUCCESS');
                return $this->_helper->redirector->gotoRouteAndExit(array(), 'admin-anastomosis');
            } catch (Exception $ex) {								
                $adapter->rollBack();
                Orion_Log::error($ex);
                Orion_FlashMessenger::addError('COMMON_SYSTEM_ERROR');
            }
        }
        $this->view->form = $form;
    }
    
	
	/**
	 * Nie znana funkcja.
	 */
    public function updateAction() {

        $frontendOptions = array(
            'lifetime' => 60 * 60 * 24,
            'automatic_serialization' => true,
        );
        $backendOptions = array(
            'cache_dir' => APPLICATION_PATH . '/cache/',
            'file_name_prefix' => 'zend_cache_query',
            'hashed_directory_level' => 2,
        );
        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        $cache->clean();
		
        $request = $this->getRequest();
        if($request->isPost()){
            $model = new App_Model_Anastomosis_DbTable();
            $data = $request->getPost();
            $action = array_keys($this->_getParam('actiontype'));
            $adapter = $model->getAdapter();
            $adapter->beginTransaction();
            try{
                switch($action[0]){
                    case 'active':
                        $model->setStatusActive($data['ids']);
                        Orion_FlashMessenger::addSuccess('ADMIN_BANNERS_ACTIVE_SUCCESS');
                        break;
                    case 'deactive':
                        $model->setStatusInactive($data['ids']);
                        Orion_FlashMessenger::addSuccess('ADMIN_BANNERS_DEACTIVE_SUCCESS');
                        break;
                    case 'delete':
                        $model->setDeleted($data['ids']);
                        Orion_FlashMessenger::addSuccess('ADMIN_BANNERS_DELETE_SUCCESS');
                        break;
                }
                $adapter->commit();
            } catch (Exception $e){
				
	
				
                $adapter->rollBack();
                Orion_Log::error($e);
                Orion_FlashMessenger::addError('COMMON_SYSTEM_ERROR');

            }
        }
        return $this->_helper->redirector->gotoRouteAndExit(array(), 'admin-anastomosis');
    }
    
	/**
	 * Widok edycji "Zespolenia"
	 */
    public function editAction() {

        $frontendOptions = array(
            'lifetime' => 60 * 60 * 24,
            'automatic_serialization' => true,
        );
        $backendOptions = array(
            'cache_dir' => APPLICATION_PATH . '/cache/',
            'file_name_prefix' => 'zend_cache_query',
            'hashed_directory_level' => 2,
        );
        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        $cache->clean();

        $request = $this->getRequest();
        $model = new App_Model_Anastomosis_DbTable();
        $id = $this->_getParam('id');
        $row = $model->getById($id);
        $form = new App_Form_Admin_Anastomosis($row);

		/**
		 * Aktualizacja danych.
		 */
        if($request->isPost()){
			
            if($form->isValid($request->getPost())){
                $adapter = $model->getAdapter();
                $adapter->beginTransaction();
                try{
                    $data = $form->getValues();
                    $model->edit($data, $row->id,0);
                    $adapter->commit();
                    Orion_FlashMessenger::addSuccess('ADMIN_SLIDER_EDIT_SUCCESS');
                    return $this->_helper->redirector->gotoRouteAndExit(array('id' => $row->id), 'admin-anastomosis-edit');
                } catch (Exception $e){
                    Orion_Log::error($e);
                    echo $e;
                    die();
                    $adapter->rollBack();
                    Orion_FlashMessenger::addError('COMMON_SYSTEM_ERROR');
                }
            }
			
        } else {
            $model2= new App_Model_Anastomosis_Attributes_DbTable();
            $rows = $model2->getByAnastomosisId($row->id);
            $populate = $row->toArray();
            foreach($rows as $el){
                if(array_key_exists($el['attribute_id'], $populate)){
                    if(!is_array($populate[$el['attribute_id']])){
                        $first = $populate[$el['attribute_id']];
                        $populate[$el['attribute_id']] = array();
                        $populate[$el['attribute_id']][] = $first;
                    }
                    $populate[$el['attribute_id']][] = $el['value'];
                } else {
                    $populate[$el['attribute_id']] = $el['value'];
                }
            }
            $form->populate($populate);
        }
        $this->view->anastomosis = $row;
        $this->view->form = $form;
    }
}

