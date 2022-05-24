<?php

class Admin_TranslateController extends Zend_Controller_Action
{
    public function indexAction()
    {
       $request = $this->getRequest();
       $model = new App_Model_Language_DbTable();
       $listview = new Orion_ListView('language', array(
            'select' => $model->getAvaliable(true),
            'columns' => array(
                'checkbox' => array('__special__', new Orion_ListView_SpecialColumn_Checkbox(array('id' => '{id}','name' => 'ids'))),
                'prefix' => array('prefix'),
                'name' => array('name'),
                'default' => array('default',array(new Orion_ListView_Transformer_Translate('ADMIN_LANGUAGE_DEFAULT'))),
                'status' => array('status', array(new Orion_ListView_Transformer_Status()), array()),
                'created_at' => array('created_at',array(new Orion_ListView_Transformer_Date('Y-m-d'))),
                'edit' => array('__special__', new Orion_ListView_SpecialColumn_Edit(array('id' => '{id}'), 'admin-language-edit')),
                'translate' => array('__special__', new Orion_ListView_SpecialColumn_Edit(array('id' => '{id}'), 'admin-language-view','ADMIN_LISTVIEW_LANGUAGE_TRANSLATE')),
            ),
            'invisibleColumns' => array(
                'id' => array('id')
            ),
            'buttons' => array(
                array('add', 'btn','admin-language-add')
            ),
            'forms' => array(
                'options' => array('method' => 'Post', 'action' => 'admin-language-update','class' => 'autoVisible', 'params' => array()),
                'submits' => array(
                    array('active',array(),'btn btn-success'),
                    array('deactive',array(),'btn btn-danger'),
                    array('delete',array(),'btn btn-inverse'),
                )
            ),
            'filters' => array(
                new Orion_ListView_Filter_Select('status', 'status', App_Model_Language_DbTable::getAvaliableStatus(), 'COMMON_FILTER_STATUS_SELECT'),
                new Orion_ListView_Filter_Between_Date('created_at', 'created_at', 13, 'DATE')
            ),
            'search' => array('name','prefix'),
            'defaultSort' => array('created_at', false),
            'sortableColumns' => array('status','prefix','name','created_at','default'),
            'pagesPerPage' => 25,
            'itemsPerPageOptions' => array(5,10,15,20,25),
            'numberColumn' => array()
        ));
        $listview->init($request->getPost());
        $this->view->listview = $listview->build();
    }
    
    public function addAction()
    {
        $request = $this->getRequest();
        $form = new App_Form_Admin_Language();
        if($request->isPost() && $form->isValid($request->getPost())){
            $model = new App_Model_Language_DbTable();
            $adapter = $model->getAdapter();
            $adapter->beginTransaction();
            try{
                $model->addLanguage($form->getValues());
                $adapter->commit();
                Orion_FlashMessenger::addSuccess('ADMIN_MESSAGE_DATA_ADD_SUCCESS');
                return $this->_helper->redirector->gotoRouteAndExit(array(),'admin-language');
            }  catch (Exception $e){
                $adapter->rollBack();
                Orion_FlashMessenger::addError('COOMON_SYSTEM_ERROR');
                Orion_Log::error($e);
            }
        }
        $this->view->form = $form;
    }
    
    public function editAction()
    {
        $request = $this->getRequest();
        $id = $this->_getParam('id');
        
        $model = new App_Model_Language_DbTable();
        $language = $model->findById($id);
        if(!$language){
            return $this->_helper->redirector->gotoRouteAndExit(array(),'admin-language');
        }
        $form = new App_Form_Admin_Language(array('formType' => $language));
        
        if($request->isPost()){
            if($form->isValid($request->getPost())){
                $adapter = $model->getAdapter();
                $adapter->beginTransaction();
                try{
                    $model->updateLanguage($form->getValues(), $language);
                    $adapter->commit();
                    Orion_FlashMessenger::addSuccess('ADMIN_MESSAGE_DATA_ADD_SUCCESS');
                    return $this->_helper->redirector->gotoRouteAndExit(array(),'admin-language');
                } catch (Exception $e){
                    $adapter->rollBack();
                    Orion_FlashMessenger::addError('COOMON_SYSTEM_ERROR');
                    Orion_Log::error($e);
                }
            }
        } else {
            $form->populate($language->toArray());
        }
        $this->view->language = $language;
        $this->view->form = $form;
    }
    
    public function viewAction()
    {   
        $language = $this->_getParam('id');
        $languageSwitcher = new Orion_Language();
        
        $model = new App_Model_Language_DbTable();
        $language = $model->findById($language);
        $this->view->csv_file = $languageSwitcher->getCsvTranslate($language->prefix);
        $this->view->language = $language;
        
    }
    
    public function translateAction()
    {
        $id = $this->_getParam('id');
        $model = new App_Model_Language_DbTable();
        $language = $model->findById($id);
        $row = $this->_getParam('row');
        $request = $this->getRequest();
        $form = new App_Form_Admin_Translate();
        
        $languageSwitcher = new Orion_Language();
        $file = $languageSwitcher->getCsvTranslate($language->prefix);
        
        if($request->isPost()){
            if($form->isValid($request->getPost())){
                $file[$row][1] = $form->getValue('1');
                $languageSwitcher->setCsvTranslate($file, $language->prefix);
            }
            return $this->_helper->redirector->gotoRouteAndExit(array('id' => $language->id),'admin-language-view');
        } else {
            $form->populate($file[$row]);
        }
        
        $this->view->language = $language;
        $this->view->form = $form;
    }
    
    public function updateAction()
    {
        $request = $this->getRequest();
        if($request->isPost()){
            $model = new App_Model_Language_DbTable();
            $data = $request->getPost();
            $action = array_keys($this->_getParam('actiontype'));
            $adapter = $model->getAdapter();
            $adapter->beginTransaction();
            try{
                switch($action[0]){
                    case 'active':
                        $model->setStatusActive($data['ids']);
                        Orion_FlashMessenger::addSuccess('ADMIN_MESSAGE_DATA_ADD_SUCCESS');
                        break;
                    case 'deactive':
                        $model->setStatusInactive($data['ids']);
                        Orion_FlashMessenger::addSuccess('ADMIN_MESSAGE_DATA_ADD_SUCCESS');
                        break;
                    case 'delete':
                        $model->setDeleted($data['ids']);
                        Orion_FlashMessenger::addSuccess('ADMIN_MESSAGE_DATA_ADD_SUCCESS');
                        break;
                }
                $adapter->commit();
            } catch (Exception $e){
                $adapter->rollBack();
                Yeti_Log::error($e);
                Yeti_Messenger::addError('COMMON_SYSTEM_ERROR');
            }
        }
        return $this->_helper->redirector->gotoRouteAndExit(array(), 'admin-language');
    }
} 