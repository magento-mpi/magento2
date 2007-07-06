<?php
class Mage_Adminhtml_Block_Permissions_Tab_Rolesusers extends Mage_Adminhtml_Block_Widget_Tabs {   
    public function __construct() {
        parent::__construct();
        
        $roles = Mage::getModel("permissions/roles")->getCollection()->load();
        $roles_users = array();
        foreach ($roles->getItems() as $role) {
        	$users = Mage::getModel("permissions/users")->getCollection()->addRoleFilter($role->getRole_id())->load();
        	$role->setUsers($users->getItems());
        	$role_users[] = $role;
        }
        
        $users = Mage::getModel("permissions/users")->getCollection()->load();
        $this->setTemplate('adminhtml/permissions/rolesusers.phtml')
        	->assign('roles', $role_users)
        	->assign('users', $users->getItems());
    }
}