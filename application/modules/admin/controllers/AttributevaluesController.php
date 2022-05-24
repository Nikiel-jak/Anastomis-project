<?php

class Admin_AttributevaluesController extends Zend_Controller_Action
{
    public function listAction()
    {
        $attribute_id = $this->_getParam('attribute_id');
        $modelAttribute = new App_Model_Attributes_DbTable();
        $attribute = $modelAttribute->findById($attribute_id);

        $request = $this->getRequest();
        $model = new App_Model_Attributes_Values_DbTable();
        $listview = new Orion_ListView('attributes', array(
            'select' => $model->getAllByAttribute($attribute_id, true),
            'columns' => array(
                'checkbox' => array('__special__', new Orion_ListView_SpecialColumn_Checkbox(array('id' => '{id}','name' => 'ids'))),
                'value' => array('value'),
                'view' => array('view'),
                'status' => array('status', array(new Orion_ListView_Transformer_Status()), array()),
                'created_at' => array('created_at',array(new Orion_ListView_Transformer_Date('Y-m-d'))),
                'edit' => array('__special__', new Orion_ListView_SpecialColumn_Edit(array('id' => '{id}','attribute_id' => $attribute_id), 'admin-attributes-values-edit')),
            ),
            'invisibleColumns' => array(
                'id' => array('id')
            ),
            'buttons' => array(
                array('add', 'btn','admin-attributes-values-add', array('attribute_id' => $attribute_id))
            ),
            'forms' =>  array(
                'options' => array('method' => 'Post', 'action' => 'admin-attributes-values-update','class' => 'autoVisible', 'params' => array('attribute_id' => $attribute_id)),
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
            'sortableColumns' => array('status','view','created_at','value'),
            'itemsPerPage' => 25,
            'itemsPerPageOptions' => array(5,10,15,20,25),
            'numberColumn' => array(),
            'header' => false
        ));
        $listview->init($request->getPost());
        $this->view->attribute = $attribute;
        $this->view->listview = $listview->build();
    }

    public function addAction()
    {
        $attribute_id = $this->_getParam('attribute_id');
        $modelAttribute = new App_Model_Attributes_DbTable();
        $attribute = $modelAttribute->findById($attribute_id);

        $request = $this->getRequest();
        $form = new App_Form_Admin_AttributesValues();
        if($request->isPost() && $form->isValid($request->getPost())){
            $model = new App_Model_Attributes_Values_DbTable();
            $adapter = $model->getAdapter();
            $adapter->beginTransaction();
            try{
                $data = $form->getValues();
                $data['attribute_id'] = $attribute_id;
                $model->create($data);
                $adapter->commit();
                Orion_FlashMessenger::addSuccess('ADMIN_MESSAGE_DATA_ADD_SUCCESS');
                return $this->_helper->redirector->gotoRouteAndExit(array('attribute_id' => $attribute_id), 'admin-attributes-values');
            } catch (Exception $e){
                $adapter->rollBack();
                Orion_Log::error($e);
                Orion_FlashMessenger::addError('COMMON_SYSTEM_ERROR');
            }
        }
        $this->view->attribute = $attribute;
        $this->view->form = $form;
    }

    public function editAction()
    {
        $request = $this->getRequest();

        $attribute_id = $this->_getParam('attribute_id');
        $modelAttribute = new App_Model_Attributes_DbTable();
        $attribute = $modelAttribute->findById($attribute_id);

        $row_id = $this->_getParam('id');
        $model = new App_Model_Attributes_Values_DbTable();
        $row = $model->findById($row_id);

        $form = new App_Form_Admin_AttributesValues();

        if($request->isPost()){
            if($form->isValid($request->getPost())){
                $adapter = $model->getAdapter();
                $adapter->beginTransaction();
                try{
                    $data = $form->getValues();
                    $model->edit($data, $adapter->quoteInto('id = ?', $row->id));
                    $adapter->commit();
                    Orion_FlashMessenger::addSuccess('ADMIN_SLIDER_EDIT_SUCCESS');
                    return $this->_helper->redirector->gotoRouteAndExit(array('attribute_id' => $attribute_id), 'admin-attributes-values');
                } catch (Exception $e){
                    $adapter->rollBack();
                    Orion_Log::error($e);
                    Orion_FlashMessenger::addError('COMMON_SYSTEM_ERROR');
                }
            }
        } else {
            $form->populate($row->toArray());
        }

        $this->view->form = $form;
        $this->view->row = $row;
        $this->view->attribute = $attribute;
    }

    public function updateAction()
    {
        $attribute_id = $this->_getParam('attribute_id');
        $modelAttribute = new App_Model_Attributes_DbTable();
        $attribute = $modelAttribute->findById($attribute_id);

        $request = $this->getRequest();
        if($request->isPost()){
            $model = new App_Model_Attributes_Values_DbTable();
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
        return $this->_helper->redirector->gotoRouteAndExit(array('attribute_id' => $attribute->id), 'admin-attributes-values');

    }
}

