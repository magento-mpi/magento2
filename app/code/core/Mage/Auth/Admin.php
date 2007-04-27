<?php

class Mage_Auth_Admin
{
    static public function action_preDispatch()
    {
        $auth = Mage::getSingleton('auth', 'session');

#$auth->acl = null;
        $request = Mage::registry('controller')->getRequest();

        if (!$auth->getUser() && $request->getPost('login')) {
            extract($request->getPost('login'));
            if (!empty($username) && !empty($password)) {
                $auth->setUser(Mage::getModel('auth_resource', 'auth')->authenticate($username, $password));
                header('Location: '.$request->getRequestUri());
                die();
            }
        }
        
        if (!$auth->getUser()) {
            echo Mage::createBlock('tpl', 'root')
                ->setTemplate('auth/login.phtml')
                ->assign('username', '')
                ->toString();
            exit;
        }
       
        if (!$auth->getAcl()) {
            $auth->setAcl(Mage::getModel('auth_resource', 'acl')->loadUserAcl($auth->getUser()->user_id));
        }
        
        Mage::register('acl', $auth->getAcl());
    }


/*
Example of acl query: Mage::getSingleton('auth', 'session')->getAcl()->isAllowed('U2', 'system/websites')
                  or: Mage::registry('acl')->isAllowed('U2', 'system/websites')
*/

}