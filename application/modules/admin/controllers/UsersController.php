<?php

class Admin_UsersController extends Zend_Controller_Action
{
	public function indexAction()
	{   
            $request = $this->getRequest();
            $model = new App_Model_Users_DbTable();
            $listview = new Orion_ListView('users', array(
                    'select' => $model->getAll(true),
                    'columns' => array(
                        'checkbox' => array('__special__', new Orion_ListView_SpecialColumn_Checkbox(array('id' => '{id}','name' => 'ids'))),
                        'email' => array('email'),
                        'status' => array('status', array(new Orion_ListView_Transformer_Status()), array()),
                        'created_at' => array('created_at',array(new Orion_ListView_Transformer_Date('Y-m-d'))),
                        'edit' => array('__special__', new Orion_ListView_SpecialColumn_Edit(array('id' => '{id}'), 'admin-users-edit')),
                    ),
                    'invisibleColumns' => array(
                        'id' => array('id')
                    ),
                    'buttons' => array(
                        array('add', 'btn','admin-users-add')
                    ),
                    'forms' => array(
                        'options' => array('method' => 'Post', 'action' => 'admin-users-update','class' => 'autoVisible', 'params' => array()),
                        'submits' => array(
                            array('active',array(),'btn btn-success'),
                            array('deactive',array(),'btn btn-danger'),
                            array('delete',array(),'btn btn-inverse'),
                        )
                    ),
                    'filters' => array(
                        new Orion_ListView_Filter_Select('status', 'status', App_Model_Users_DbTable::getAvaliableStatus(), 'COMMON_FILTER_STATUS_SELECT'),
                        new Orion_ListView_Filter_Between_Date('created_at', 'created_at', 13, 'DATE')
                    ),
                    'search' => array('fristname','lastname','email'),
                    'defaultSort' => array('created_at', false),
                    'sortableColumns' => array('status','firstname','email','lastname','created_at'),
                    'pagesPerPage' => 25,
                    'itemsPerPageOptions' => array(5,10,15,20,25),
                    'numberColumn' => array()
                ));
                $listview->init($request->getPost());
                $this->view->listview = $listview->build();
	}
   
    public function addAction()
    {
        $form = new App_Form_Admin_User(array('formType' => 'add'));
        $form->setAction($this->view->url(array(),'admin-users-add'));
        $request = $this->getRequest();
        $userModel = new App_Model_Users_DbTable();
        if($request->isPost() && $form->isValid($request->getPost()))
        {   
        
            $adapter = $userModel->getAdapter();
            $adapter->beginTransaction();
            try{
                $data = $form->getValues();
                unset($data['confirm_password']);
                $data['password'] = sha1($data['password']);
                $id =  $userModel->insert($data);
                Orion_FlashMessenger::addSuccess('USER_ADD_WAS_SUCESSFULL');
                $adapter->commit();
                return $this->_helper->redirector->gotoRouteAndExit(array(),'admin-users');
            } catch(exception $e) {
                $adapter->rollBack();
                Orion_FlashMessenger::addError('COOMON_SYSTEM_ERROR');
                Orion_Log::error($e);
            }
        }
        $this->view->form = $form;
    }

    public function editAction()
    {
        $id = (int) $this->_getParam('id');
        $usersModel = new App_Model_Users_DbTable();
        $user = $usersModel->findById($id);
        if(!$user){
            Orion_FlashMessenger::addSuccess('Wrong acion params');
            return $this->_helper->redirector->gotoRouteAndExit(array(),'admin-users');
        }

        $form = new App_Form_Admin_User(array('formType' => 'edit', 'userId' => $id));
        $request = $this->getRequest();

        if($request->isXmlHttpRequest()){
            if(!$form->isValidPartial($request->getPost())){
                    $errorsMessages = $form->getMessages();
                    $this->_helper->json($errorsMessages, array('enableJsonExprFinder' => true));
            } else {
                $this->_helper->json('ok', array('enableJsonExprFinder' => true));
            }
        }

        if($request->isPost() && $form->isValid($request->getPost()))
        {
                $adapter = $usersModel->getAdapter();
                $adapter->beginTransaction();
                try{

                $data = $form->getValues();
                unset($data['confirm_password']);
                if(empty($data['password'])){
                    unset($data['password']);
                } else {
                    $data['password'] = sha1($data['password']);
                }
                $where = $adapter->quoteInto('id = ?',$id);
                $usersModel->update($data , $where);
                Orion_FlashMessenger::addSuccess('ADMIN_USERS_EDIT_WAS_SUCCESSFUL');
                $adapter->commit();

                } catch(exception $e) {
                    $adapter->rollBack();
                    Orion_FlashMessenger::addError('System Error');
                    Orion_Log::error($e);
                }
                return $this->_helper->redirector->gotoRouteAndExit(array(),'admin-users');
        } else {
            $form->populate($user->toArray());
        }
        
        $this->view->user = $user;
        $this->view->form = $form;

    }

    public function updateAction()
    {
        $request = $this->getRequest();
        if($request->isPost()){
            $model = new App_Model_Users_DbTable();
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
        return $this->_helper->redirector->gotoRouteAndExit(array(), 'admin-users');
    }
}