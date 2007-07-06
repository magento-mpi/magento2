<?php
class Mage_Adminhtml_Block_Permissions_UsernRoles extends Mage_Core_Block_Template {
    public function __construct() {
        parent::__construct();
		/*
        $user_collection = array(array('id' => 1, 'username' => 'azazel'),
        						 array('id' => 2, 'username' => 'moshe'),
        						 array('id' => 3, 'username' => 'begemot'));
               
        $roles_collection = array(array('id' => 1, 'title' => 'admin'),
        						  array('id' => 2, 'title' => 'teacher'),
        						  array('id' => 3, 'title' => 'driver'));
        */
		$user_collection = Mage::getModel("permissions/users")->getCollection()->load();
		$roles_collection = Mage::getModel("permissions/roles")->getCollection()->load();
		
        $this->setTemplate('adminhtml/permissions/usernroles.phtml')
        	->assign('users', $user_collection->getItems())
        	->assign('roles', $roles_collection->getItems());
    }
}