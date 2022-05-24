<?php

class Admin_AttributesController extends Zend_Controller_Action
{
    public function indexAction()
    {      
        $request = $this->getRequest();
        $model = new App_Model_Attributes_DbTable();
        $listview = new Orion_ListView('attributes', array(
            'select' => $model->getAll(true),
            'columns' => array(
                'checkbox' => array('__special__', new Orion_ListView_SpecialColumn_Checkbox(array('id' => '{id}','name' => 'ids'))),
                'name' => array('name'),
                'step' => array('step'),
                'status' => array('status', array(new Orion_ListView_Transformer_Status()), array()),
                'created_at' => array('created_at',array(new Orion_ListView_Transformer_Date('Y-m-d'))),
                'edit' => array('__special__', new Orion_ListView_SpecialColumn_Edit(array('id' => '{id}'), 'admin-attributes-edit')),
            ),
            'invisibleColumns' => array(
                'id' => array('id')
            ),
            'buttons' => array(
                array('add', 'btn','admin-attributes-add')
            ),
            'forms' =>  array(
                'options' => array('method' => 'Post', 'action' => 'admin-attributes-update','class' => 'autoVisible', 'params' => array()),
                'submits' => array(
                    array('active',array(),'btn btn-success'),
                    array('deactive',array(),'btn btn-danger'),
                    array('delete',array(),'btn btn-inverse'),
                )
            ),
            'filters' => array(
                new Orion_ListView_Filter_Select('status', 'status', App_Model_Attributes_DbTable::getAvaliableStatus(), 'COMMON_FILTER_STATUS_SELECT'),
                new Orion_ListView_Filter_Between_Date('created_at', 'created_at', 13, 'DATE')
            ),
            'search' => array('name'),
            'defaultSort' => array('created_at', false),
            'sortableColumns' => array('status','name','created_at','step'),
            'itemsPerPage' => 25,
            'itemsPerPageOptions' => array(5,10,15,20,25),
            'numberColumn' => array()
        ));
        $listview->init($request->getPost());
        $this->view->listview = $listview->build();
    }

    public function addAction()
    {
        $request = $this->getRequest();
        $form = new App_Form_Admin_Attributes();
        if($request->isPost() && $form->isValid($request->getPost())){
            $model = new App_Model_Attributes_DbTable();
            $adapter = $model->getAdapter();
            $adapter->beginTransaction();
            try{
                $id= $model->create($form->getValues());
                $adapter->commit();
                Orion_FlashMessenger::addSuccess('ADMIN_MESSAGE_DATA_ADD_SUCCESS');
                if(array_key_exists('def', $request->getPost())){
                    return $this->_helper->redirector->gotoRouteAndExit(array('attribute_id' => $id), 'admin-attributes-values');
                }
                return $this->_helper->redirector->gotoRouteAndExit(array(), 'admin-attributes');
            } catch (Exception $e){
                $adapter->rollBack();
                Orion_Log::error($e);
                Orion_FlashMessenger::addError('COMMON_SYSTEM_ERROR');
            }
        }
        $this->view->form = $form;
    }

    public function editAction()
    {   
        $request = $this->getRequest();
        $model = new App_Model_Attributes_DbTable();
        $id = $this->_getParam('id');
        $row = $model->getById($id);
        $form = new App_Form_Admin_Attributes();
        
        if($request->isPost()){
            if($form->isValid($request->getPost())){
                $adapter = $model->getAdapter();
                $adapter->beginTransaction();
                try{
                    $data = $form->getValues();
                    $model->edit($data, $adapter->quoteInto('id = ?', $row->id));
                    $adapter->commit();
                    Orion_FlashMessenger::addSuccess('ADMIN_SLIDER_EDIT_SUCCESS');
                    if(array_key_exists('def', $request->getPost())){
                        return $this->_helper->redirector->gotoRouteAndExit(array('attribute_id' => $row->id), 'admin-attributes-values');
                    }
                    return $this->_helper->redirector->gotoRouteAndExit(array(), 'admin-attributes');
                } catch (Exception $e){
                    $adapter->rollBack();
                    Orion_Log::error($e);
                    Orion_FlashMessenger::addError('COMMON_SYSTEM_ERROR');
                }
            }
        } else {
            $form->populate($row->toArray());
        }
        $this->view->attribute = $row;
        $this->view->form = $form;
    }

    public function updateAction()
    {
        $request = $this->getRequest();
        if($request->isPost()){
            $model = new App_Model_Attributes_DbTable();
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
        return $this->_helper->redirector->gotoRouteAndExit(array(), 'admin-attributes');
    }
}

