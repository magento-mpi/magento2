<?php

class Mage_Auth_Setup extends Mage_Core_Module_Setup
{
    static public function load_admin_action_preDispatch()
    {
        Zend_Session::setOptions(array('save_path'=>Mage::getBaseDir('var').DS.'session'));
        Zend_Session::start();

        Mage::register('auth', $auth = new Zend_Session_Namespace('Mage_Auth'));
                
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
            $auth->acl = Mage::getModel('auth', 'Acl')->load();
        }
        if (empty($auth->acl)) {
            self::createAuthBase();
        }
    }
    
    static public function createAuthBase()
    {
        $acl = new Mage_Auth_Acl();
        
        $acl->addRole($user = new Mage_Auth_Acl_Role_Group('user'));
        $acl->addRole($admin = new Mage_Auth_Acl_Role_Group('admin'));
        $acl->addRole($dev = new Mage_Auth_Acl_Role_Group('dev'));
        
        $acl->addRole($moshe = new Mage_Auth_Acl_Role_User('moshe'), 'dev');
        $acl->addRole($andrey = new Mage_Auth_Acl_Role_User('andrey'), 'dev');
        $acl->addRole($dmitriy = new Mage_Auth_Acl_Role_User('dmitriy'), 'dev');
        
        $acl->add(new Mage_Auth_Acl_Resource('admin'));
        
        $acl->allow('dev', 'admin');
        
        Mage::getModel('auth', 'Acl')->save($acl);
    }
}