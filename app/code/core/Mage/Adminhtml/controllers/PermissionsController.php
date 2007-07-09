<?php
class Mage_Adminhtml_PermissionsController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction() {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('system/acl');
        $this->_addBreadcrumb(__('System'), __('System title'), Mage::getUrl('adminhtml/system'));
        $this->_addBreadcrumb(__('Permission'), __('Permission title'));

        $this->_addContent($this->getLayout()->createBlock('adminhtml/permissions_users'));
        $this->_addLeft($this->getLayout()->createBlock('adminhtml/permissions_roles', 'group'));

        $this->renderLayout();
    }
    
    public function userGridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/permissions_grid_user')->toHtml());
    }
    
    public function roleGridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/permissions_grid_role')->toHtml());
    }

    public function editUserAction() {
        $this->loadLayout('baseframe');
        $this->_addBreadcrumb(__('System'), __('System title'), Mage::getUrl('adminhtml/system'));
        $this->_addBreadcrumb(__('Permission'), __('Permission title'), Mage::getUrl('*/*/index'));
        $this->_addBreadcrumb(__('Users'), __('Users title'));
        
        $this->_addLeft(
            $this->getLayout()->createBlock('adminhtml/permissions_edituser')
        );
        $this->_addContent(
            $this->getLayout()->createBlock('core/template')->setTemplate('adminhtml/permissions/userinfo.phtml')
        );
        $this->renderLayout();
    }

    public function editRoleAction() {
        $this->loadLayout('baseframe');
        $this->_addBreadcrumb(__('System'), __('System title'), Mage::getUrl('adminhtml/system'));
        $this->_addBreadcrumb(__('Permission'), __('Permission title'), Mage::getUrl('*/*/index'));
        $this->_addBreadcrumb(__('Roles'), __('Roles title'));
        
        $this->_addLeft(
            $this->getLayout()->createBlock('adminhtml/permissions_editroles')
        );

        $this->_addContent(
            $this->getLayout()->createBlock('core/template')->setTemplate('adminhtml/permissions/roleinfo.phtml')
        );

        $this->renderLayout();
    }

    public function deleteRoleAction() {
    	$rid = $this->getRequest()->getParam('rid', false);
    	Mage::getModel("permissions/roles")->setId($rid)->delete();

    	$this->_redirect("adminhtml/permissions");
    }

    public function deleteUserAction() {
    	$uid = $this->getRequest()->getParam('uid', false);
    	Mage::getModel("permissions/users")->setId($uid)->delete();

    	$this->_redirect("adminhtml/permissions");
    }

    public function saveRoleAction() {
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

    public function saveUserAction() {
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
