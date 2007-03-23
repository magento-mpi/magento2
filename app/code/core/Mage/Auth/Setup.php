<?php

class Mage_Auth_Setup extends Mage_Core_Module_Setup
{
    static public function load_admin_action_preDispatch()
    {
        Zend_Session::setOptions(array('save_path'=>Mage::getBaseDir('var').DS.'session'));
        Zend_Session::start();
        
        Mage::register('auth_session', $auth = new Zend_Session_Namespace('Mage_Auth'));

#$auth->acl = null;

        if (empty($auth->user) && isset($_POST['login'])) {
            extract($_POST['login']);
            if (!empty($username) && !empty($password)) {
                $auth->user = Mage::getModel('auth', 'Auth')->authenticate($username, $password);
            }
        }
        
        if (empty($auth->user)) {
            echo Mage::createBlock('tpl', 'root')
                ->setViewName('Mage_Auth', 'Admin/login')
                ->assign('username', '')
                ->toString();
            exit;
        }
       
        if (empty($auth->acl)) {
            $auth->acl = Mage::getModel('auth', 'Acl')->loadUserAcl($auth->user->user_id);
        }
    }


/*
Example of acl query: Mage::registry('auth_session')->acl->isAllowed('U2', 'system/websites')
*/

}