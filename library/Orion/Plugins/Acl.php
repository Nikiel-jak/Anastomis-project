<?php

class Orion_Plugins_Acl extends Zend_Controller_Plugin_Abstract
{
    /** @var Zend_Acl  */
    private $acl;

    /**
     * Orion_Plugins_Acl constructor.
     * @throws Zend_Acl_Exception
     */
    public function __construct()
    {
        $acl = new Zend_Acl();

        $acl->addRole(new Zend_Acl_Role('guest'));
        $acl->addRole(new Zend_Acl_Role('member'), 'guest');
        $acl->addRole(new Zend_Acl_Role('admin'), 'member');
        $acl->addRole(new Zend_Acl_Role('sadmin'), 'admin');
        $acl->addRole(new Zend_Acl_Role('developer'), 'sadmin');

        $acl->addResource(new Zend_Acl_Resource('admin'));
        $acl->addResource(new Zend_Acl_Resource('admin:auth'), 'admin');
        $acl->addResource(new Zend_Acl_Resource('admin:error'), 'admin');
        $acl->addResource(new Zend_Acl_Resource('admin:index'), 'admin');
        $acl->addResource(new Zend_Acl_Resource('admin:user'), 'admin');

        $acl->addResource(new Zend_Acl_Resource('api'));

        $acl->addResource(new Zend_Acl_Resource('site'));
        $acl->addResource(new Zend_Acl_Resource('site:profile'));

        $acl->allow(null, 'site', null);

        $acl->allow(null, 'admin:error', null);
        $acl->allow(null, 'admin:auth', array('login', 'logout', 'forget-password', 'reset-password'));

        $acl->allow('admin', 'admin', null);
        $acl->allow('developer', null, null);

        $this->acl = $acl;
    }

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @throws Zend_Acl_Exception
     * @throws Zend_Session_Exception
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        $resource = "{$module}:{$controller}";
        $role = Orion_Auth::getProfileRole();
        if ($module == 'site') {
            $configurationModel = new App_Model_Configuration_DbTable();
            $site_status = $configurationModel->getValue('site_status');
            if (!$site_status) {
                die('strona w przebudowie');
            }
        }
        if (!$this->acl->has($resource)) {
            $this->acl->addResource(new Zend_Acl_Resource($resource), $module);
        }
        if ($this->acl->isAllowed($role, $resource, $action)) {
            return;
        }

        if (!Orion_Auth::isLogged()) {
            Orion_FlashMessenger::addInformation('COMMON_STE_DONT_HAVE_ACCESS');
            $request->setDispatched(false);
            $request->setControllerName('auth');
            $request->setActionName('login');
        }

    }
}
