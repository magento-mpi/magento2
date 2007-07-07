<?php
class Mage_Adminhtml_PermissionsController extends Mage_Adminhtml_Controller_Action 
{
    public function indexAction() {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('catalog');
        $this->_addBreadcrumb(__('catalog'), __('catalog title'));
            
        $this->_addContent($this->getLayout()->createBlock('adminhtml/permissions_usernroles'));
        
        $this->renderLayout();
    }
    
    public function edituserAction() {
        $this->loadLayout('baseframe');
        
        $this->getLayout()->getBlock('left')->append($this->getLayout()->createBlock('adminhtml/permissions_edituser'));
        
        $block = $this->getLayout()->createBlock('core/template')->setTemplate('adminhtml/permissions/userinfo.phtml')->assign('uid', $this->getRequest()->getParam('uid', false));
        $this->_addContent($block);
        
        $this->_addBreadcrumb(__('customers'), __('customers title'));

        $this->renderLayout();
    }
    
    public function editrolesAction() {
        $this->loadLayout('baseframe');
        
        $this->getLayout()->getBlock('left')->append($this->getLayout()->createBlock('adminhtml/permissions_editroles'));
        
        $block = $this->getLayout()->createBlock('core/template')->setTemplate('adminhtml/permissions/roleinfo.phtml')->assign('rid', $this->getRequest()->getParam('rid', false));
        $this->_addContent($block);
        
        $this->_addBreadcrumb(__('customers'), __('customers title'));

        $this->renderLayout();
    }
    
    public function deleteroleAction() {
    	$rid = $this->getRequest()->getParam('rid', false);
    	Mage::getModel("permissions/roles")->setId($rid)->delete();
    	
    	$this->_redirect("adminhtml/permissions");
    }
    
    public function deleteuserAction() {
    	$uid = $this->getRequest()->getParam('uid', false);
    	Mage::getModel("permissions/users")->setId($uid)->delete();
    	
    	$this->_redirect("adminhtml/permissions");
    }
    
    public function saveroleAction() {
    	$rid = $this->getRequest()->getParam('role_id', false);
    	
    	$rid = Mage::getModel("permissions/roles")
	    		->setId($rid)
	    		->setName($this->getRequest()->getParam('role_name', false))
	    		->setPid($this->getRequest()->getParam('parent_id', false))
	    		->save();
		
    	Mage::getModel("permissions/rules")
    		->setRoleId($rid)
    		->setResources($this->getRequest()->getParam('resource', false))
    		->saveRel();
    	
    		
    	$rid = explode(",", $rid);
    	$rid = $rid[0];
    	$this->_redirect("adminhtml/permissions/editroles/rid/$rid");
    }
    
    public function saveuserAction() {
    	$uid = $this->getRequest()->getParam('user_id', false);
    	$uid = Mage::getModel("permissions/users")
	    		->setId($uid)
	    		->setFirstname($this->getRequest()->getParam('firstname', false))
	    		->setEmail($this->getRequest()->getParam('email', false))
	    		->setPassword($this->getRequest()->getParam('password', false))
	    		->save();
    		
    	Mage::getModel("permissions/users")
    		->setIds($this->getRequest()->getParam('roles', false))
    		->setUid($this->getRequest()->getParam('user_id', false))
    		->saveRel(); 
    		
    	$uid = explode(",", $uid);
    	$uid = $uid[0];
    	$this->_redirect("adminhtml/permissions/edituser/uid/$uid");
    }
}
