<?php

class Mage_Auth_Admin
{
    static public function action_preDispatch()
    {
        Mage::register('auth_session', $auth = new Zend_Session_Namespace('Mage_Auth'));

#$auth->acl = null;

        if (empty($auth->user) && isset($_POST['login'])) {
            extract($_POST['login']);
            if (!empty($username) && !empty($password)) {
                $auth->user = Mage::getResourceModel('auth', 'auth')->authenticate($username, $password);
            }
        }
        
        if (empty($auth->user)) {
            echo Mage::createBlock('tpl', 'root')
                ->setViewName('Mage_Auth', 'Admin/login.phtml')
                ->assign('username', '')
                ->toString();
            exit;
        }
       
        if (empty($auth->acl)) {
            $auth->acl = Mage::getResourceModel('auth', 'acl')->loadUserAcl($auth->user->user_id);
        }
        
        Mage::register('acl', $auth->acl);
    }


/*
Example of acl query: Mage::registry('acl')->isAllowed('U2', 'system/websites')
*/

}