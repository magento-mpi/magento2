<?php
class Mage_Adminhtml_Block_Permissions_Tab_Userroles extends Mage_Adminhtml_Block_Widget_Tabs {   
    public function __construct() {
        parent::__construct();
        
        $uid = Mage::registry('controller')->getRequest()->getParam('uid', false);
        $uid = !empty($uid) ? $uid : 0;
        $user_roles = Mage::getModel("permissions/roles")        	
        	->getCollection()
        	->addUserRel($uid)
        	->load();
        						  
        $this->setTemplate('adminhtml/permissions/userroles.phtml')        	
        	->assign('user_roles', $user_roles->getItems());
    }
}