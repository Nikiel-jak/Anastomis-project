<?php

class Admin_GroupsController extends Zend_Controller_Action
{
    public function indexAction()
    {       
        $request = $this->getRequest();
        $model = new App_Model_Groups_DbTable();
        $listview = new Orion_ListView('groups', array(
            'select' => $model->getAll(true),
            'columns' => array(
                'checkbox' => array('__special__', new Orion_ListView_SpecialColumn_Checkbox(array('id' => '{id}','name' => 'ids'))),
                'name' => array('name'),
                'status' => array('status', array(new Orion_ListView_Transformer_Status()), array()),
                'created_at' => array('created_at',array(new Orion_ListView_Transformer_Date('Y-m-d'))),
                'edit' => array('__special__', new Orion_ListView_SpecialColumn_Edit(array('id' => '{id}'), 'admin-groups-edit')),
            ),
            'invisibleColumns' => array(
                'id' => array('id')
            ),
            'buttons' => array(
                array('add', 'btn','admin-groups-add')
            ),
            'forms' =>  array(
                'options' => array('method' => 'Post', 'action' => 'admin-groups-update','class' => 'autoVisible', 'params' => array()),
                'submits' => array(
                    array('active',array(),'btn btn-success'),
                    array('deactive',array(),'btn btn-danger'),
                    array('delete',array(),'btn btn-inverse'),
                )
            ),
            'filters' => array(
                new Orion_ListView_Filter_Select('status', 'status', App_Model_Groups_DbTable::getAvaliableStatus(), 'COMMON_FILTER_STATUS_SELECT'),
                new Orion_ListView_Filter_Between_Date('created_at', 'created_at', 13, 'DATE')
            ),
            'search' => array('name'),
            'defaultSort' => array('created_at', false),
            'sortableColumns' => array('status','name','created_at'),
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
        $form = new App_Form_Admin_Groups();
        $form->setAction($this->view->url(array()),'admin-groups-add');
        if($request->isPost() && $form->isValid($request->getPost())){
            $model = new App_Model_Groups_DbTable();
            $adapter = $model->getAdapter();
            $adapter->beginTransaction();
            try{
                $model->create($form->getValues());
                $adapter->commit();
                Orion_FlashMessenger::addSuccess('ADMIN_MESSAGE_DATA_ADD_SUCCESS');
                return $this->_helper->redirector->gotoRouteAndExit(array(), 'admin-groups');
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
        $model = new App_Model_Groups_DbTable();
        $id = $this->_getParam('id');
        $row = $model->getById($id);
        $form = new App_Form_Admin_Groups();
        
        if($request->isPost()){
            if($form->isValid($request->getPost())){
                $adapter = $model->getAdapter();
                $adapter->beginTransaction();
                try{
                    $data = $form->getValues();
                    $model->edit($data, $adapter->quoteInto('id = ?', $row->id));
                    $adapter->commit();
                    Orion_FlashMessenger::addSuccess('ADMIN_SLIDER_EDIT_SUCCESS');
                    return $this->_helper->redirector->gotoRouteAndExit(array(), 'admin-groups');
                } catch (Exception $e){
                    $adapter->rollBack();
                    Orion_Log::error($e);
                    Orion_FlashMessenger::addError('COMMON_SYSTEM_ERROR');
                }
            }
        } else {
            $form->populate($row->toArray());
        }
        $this->view->group = $row;
        $this->view->form = $form;
    }

    public function updateAction()
    {
        $request = $this->getRequest();
        if($request->isPost()){
            $model = new App_Model_Groups_DbTable();
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
        return $this->_helper->redirector->gotoRouteAndExit(array(), 'admin-groups');
    }
}

