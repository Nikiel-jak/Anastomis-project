<?php

class Admin_DeclarationController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $request = $this->getRequest();
        $model = new App_Model_Declaration_DbTable();
        $listview = new Orion_ListView('declaration', array(
            'select' => $model->getAll(true),
            'columns' => array(
                'checkbox' => array('__special__', new Orion_ListView_SpecialColumn_Checkbox(array('id' => '{id}','name' => 'ids'))),
                'number' => array('number'),
                'works' => array('works'),
                'year' => array('year'),
                'lp' => array('lp'),
                'status' => array('status', array(new Orion_ListView_Transformer_Status()), array()),
                'created_at' => array('created_at',array(new Orion_ListView_Transformer_Date('Y-m-d'))),
//                'edit' => array('__special__', new Orion_ListView_SpecialColumn_Edit(array('id' => '{id}'), 'admin-declaration-edit')),
            ),
            'invisibleColumns' => array(
                'id' => array('id')
            ),
//            'buttons' => array(
//                array('add', 'btn','admin-declaration-add')
//            ),
            'forms' =>  array(
                'options' => array('method' => 'Post', 'action' => 'admin-declaration-update','class' => 'autoVisible', 'params' => array()),
                'submits' => array(
                    array('active',array(),'btn btn-success'),
                    array('deactive',array(),'btn btn-danger'),
                    array('delete',array(),'btn btn-inverse'),
                )
            ),
            'filters' => array(
                new Orion_ListView_Filter_Select('status', 'status', App_Model_Declaration_DbTable::getAvaliableStatus(), 'COMMON_FILTER_STATUS_SELECT'),
                new Orion_ListView_Filter_Between_Date('created_at', 'created_at', 13, 'DATE')
            ),
            'search' => array('number'),
            'defaultSort' => array('created_at', false),
            'sortableColumns' => array('status','number','created_at','works','lp','year'),
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
        $form = new App_Form_Admin_Declaration();
        if($request->isPost() && $form->isValid($request->getPost())){
            $model = new App_Model_Declaration_DbTable();
            $adapter = $model->getAdapter();
            $adapter->beginTransaction();
            try{
                $model->addDeclaration($form->getValues());
                $adapter->commit();
                Orion_FlashMessenger::addSuccess('ADMIN_MESSAGE_DATA_ADD_SUCCESS');
                return $this->_helper->redirector->gotoRouteAndExit(array(), 'admin-declaration');
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
        $model = new App_Model_Banners_DbTable();
        $id = $this->_getParam('id');
        $row = $model->getById($id);
        $form = new App_Form_Admin_Banners(array('image' => $row->images));
        
        if($request->isPost()){
            if($form->isValid($request->getPost())){
                $adapter = $model->getAdapter();
                $adapter->beginTransaction();
                try{
                    $data = $form->getValues();
                    if($data['deleted_image'] == 1){
                        $file = new Orion_File($row->images);
                        $file->delete();
                    }
                    if(!empty($data['images'])){
                        $file = new Orion_File($data['images']);
                        $file->copy();
                        $data['images'] = $file->getPath();
                    } else {
                        $data['images'] = $row->images;
                    }
                    unset($data['deleted_image']);
                    $model->updateSlider($data, $row->id);
                    $adapter->commit();
                    Orion_FlashMessenger::addSuccess('ADMIN_SLIDER_EDIT_SUCCESS');
                    return $this->_helper->redirector->gotoRouteAndExit(array(), 'admin-banners');
                } catch (Exception $e){
                    $adapter->rollBack();
                    Orion_Log::error($e);
                    Orion_FlashMessenger::addError('COMMON_SYSTEM_ERROR');
                }
            }
        } else {
            $form->populate($row->toArray());
        }
        $this->view->slider = $row;
        $this->view->form = $form;
    }

    public function updateAction()
    {
        $request = $this->getRequest();
        if($request->isPost()){
            $model = new App_Model_Declaration_DbTable();
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
                echo $e; die();
                $adapter->rollBack();
                Orion_Log::error($e);
                Orion_FlashMessenger::addError('COMMON_SYSTEM_ERROR');

            }
        }
        return $this->_helper->redirector->gotoRouteAndExit(array(), 'admin-declaration');
    }
}
