<?php

class Admin_AuthController extends Zend_Controller_Action
{
    public function loginAction()
    {                    
        //powolanie formularza
        $form = new App_Form_Login();
        //dodanie action do formularza z routingu 
        $form->setAction($this->view->url(array(),'admin-auth-login'));
        //powolanie modelu
        $userModel = new App_Model_Users_DbTable();
        //
        $request = $this->getRequest();
        // ajax validate
        if($request->isXmlHttpRequest()){
            if(!$form->isValidPartial($request->getPost())){
                    $errorsMessages = $form->getMessages();
                    $this->_helper->json($errorsMessages, array('enableJsonExprFinder' => true));   
            } else {          
                $this->_helper->json('ok', array('enableJsonExprFinder' => true)); 
            }
        }
        // sprawdza czy jest przeslane postem
        if($request->isPost()) {   
            if($request->isPost() && $form->isValid($request->getPost())){
                try 
                {   
                    // pobiera wartosci formularza do $data
                    $data = $form->getValues();
                    $db = Zend_Db_Table::getDefaultAdapter();

                    $authAdapter = new Zend_Auth_Adapter_DbTable($db, 'users', 'email', 'password');
                        
                    $authAdapter->setIdentity($data['login']);
                    $authAdapter->setCredential(sha1($data['password']));

                    $result = $authAdapter->authenticate();
                    if ($result->isValid()) {
                        $auth = Zend_Auth::getInstance();
                        $storage = $auth->getStorage();
                        $user = $authAdapter->getResultRowObject(array(
                            'id', 'email', 'status', 'role',
                        ));
                        if($user->status == App_Model_Users_DbTable::STATUS_ACTIVE){
							
							$adapter = $userModel->getDefaultAdapter();
							$adapter->beginTransaction();
							try{
							   $userModel->SetLastLogIn($data);
							   $adapter->commit();
							} catch (exception $e){
								$adapter->rollBack();
							}
							
                            $storage->write($user);
                            return $this->_helper->redirector->gotoRouteAndExit(array(),'admin-anastomosis');
                        } else {
                            Orion_FlashMessenger::addError('YOUR_ACCOUNT_IS_BLOCK');
                        }
                    } else {
                        Orion_FlashMessenger::addError('INAVLID_LOGIN_OR_PASSWORD');

						$adapter = $userModel->getDefaultAdapter();
              			$adapter->beginTransaction();
                        try{
                    	   $userModel->SetFailedLogIn($data);
                    	   $adapter->commit();
                        } catch (exception $e){
                            $adapter->rollBack();
                        }
                    }
                    
 
                } catch (exception $e) {   
                    Orion_FlashMessenger::addError('COOMON_SYSTEM_ERROR');
                    Orion_Log::error($e);
                }
                return $this->_helper->redirector->gotoRouteAndExit(array(),'admin-auth-login');
            }
        }
        // przeslanie formularza do widoku (zawsze uzyawsz $this->view)
        $this->view->form = $form; 
        
    }
    

    public function logoutAction()
    {
      $auth = Zend_Auth::getInstance();
      $auth->clearIdentity();
      Orion_FlashMessenger::addSuccess('LOGOUT_IS_SUCCESSFULL');
      return $this->_helper->redirector->gotoRouteAndExit(array(),'admin-auth-login');
    }
    
    public function forgetPasswordAction()
    {
        $form = new App_Form_ForgetPassword();
        $request = $this->getRequest();
        if($request->isXmlHttpRequest()){
            if(!$form->isValidPartial($request->getPost())){
                    $errorsMessages = $form->getMessages();
                    $this->_helper->json($errorsMessages, array('enableJsonExprFinder' => true));   
            } else {          
                $this->_helper->json('ok', array('enableJsonExprFinder' => true)); 
            }
        }
        if($request->isPost() && $form->isValid($request->getPost())){
            $email = $form->getValue('email');
            $userModel = new App_Model_Users_DbTable();
            $user = $userModel->getByEmail($email);
            if($user){
                $tokensModel = new App_Model_Tokens_DbTable();
                $token = $tokensModel->setForgetPasswordToken($user);
                $mailSendQueueModel = new App_Model_MailSendQueue_DbTable();
                $options = array(
                    'key_reminder' => $token,
                    'title' => 'Resetowanie hasla',
                );
                $data = array(
                    'email' => $user->email,
                    'template' => 'forgetpassword',
                    'options' => serialize($options),
                );
                $id  = $mailSendQueueModel->setEmailToSend($data);
                $mailSendQueueModel->sendEmail($id);
                Orion_FlashMessenger::addInformation('ACCOUNT_EMAIL_SEND_INFO');
            } else {
                Orion_FlashMessenger::addInformation('ACCOUNT_EMAIL_NON_EXIST');
            }
            return $this->_helper->redirector->gotoRouteAndExit(array(),'admin-auth-login'); 
        }
        $this->view->form = $form;
    }
    
    public function resetPasswordAction()
    {
        $code = $this->_getParam('code');
        $tokensModel = new App_Model_Tokens_DbTable();
        $token = $tokensModel->getByKey($code);
        if(!$token || $token->status != App_Model_Tokens_DbTable::STATUS_NEW){
            Orion_FlashMessenger::addInformation('ADMIN_PASSWORD_RESET_TOKEN_IS_NOT_ACTIVE');
            return $this->_helper->redirector->gotoRouteAndExit(array(),'admin-auth-login');   
        }
        $form = new App_Form_ResetPassword();
        $request=$this->getRequest();
        if($request->isXmlHttpRequest()){
            if(!$form->isValidPartial($request->getPost())){
                $errorsMessages = $form->getMessages();
                $this->_helper->json($errorsMessages, array('enableJsonExprFinder' => true));   
            } else {          
                $this->_helper->json('ok', array('enableJsonExprFinder' => true)); 
            }
        }
        if($request->isPost() && $form->isValid($request->getPost())){
            $user_id = $token->type_id;
            $userModel = new App_Model_Users_DbTable();
            $adapter = $userModel->getAdapter();
            $adapter->beginTransaction();
            try{
                $where = $adapter->quoteInto('id = ?',$user_id);
                $data = array(
                    'password' => sha1($form->getValue('password')),
                );
                $userModel->update($data, $where);
                $adapter->commit();
                Orion_FlashMessenger::addSuccess('ACCOUNT_ADMIN_PASSWORD_CHANGE_SUCCESS');
            } catch (exception $e) {
                $adapter->rollBack();
                Orion_FlashMessenger::addError('COOMON_SYSTEM_ERROR');
                Orion_Log::error($e);
            }
            return $this->_helper->redirector->gotoRouteAndExit(array(),'admin-auth-login'); 
        }
        $this->view->form = $form;
    }
}