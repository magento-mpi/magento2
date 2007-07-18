<?php
class Mage_Adminhtml_Block_Permissions_Tab_Userroles extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();

        $uid = $this->getRequest()->getParam('id', false);
        $uid = !empty($uid) ? $uid : 0;
        $roles = Mage::getModel("permissions/roles")
        	->getCollection()
        	->load();

        $user_roles = Mage::getModel("permissions/roles")
        	->getUsersCollection()
        	->setUserFilter($uid)
        	->load();


        $this->setTemplate('adminhtml/permissions/userroles.phtml')
        	->assign('roles', $roles)
        	->assign('user_roles', $user_roles);
    }
}