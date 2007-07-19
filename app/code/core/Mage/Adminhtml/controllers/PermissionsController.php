<?php
class Mage_Adminhtml_PermissionsController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('system/acl');
        $this->_addBreadcrumb(__('System'), __('System Title'));
        $this->_addBreadcrumb(__('Permissions'), __('Permissions Title'));

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

    public function editUserAction()
    {
        $userId = $this->getRequest()->getParam('id');
        $this->loadLayout('baseframe');
        $this->_initLayoutMessages('adminhtml/session');

        $this->_addBreadcrumb(__('System'), __('System Title'), Mage::getUrl('adminhtml/system'));
        $this->_addBreadcrumb(__('Permission'), __('Permission Title'), Mage::getUrl('*/*/index'));
        $this->_addBreadcrumb(__('Users'), __('Users Title'));

        $this->_addLeft(
            $this->getLayout()->createBlock('adminhtml/permissions_edituser')
                 ->setUserId($userId)
        );

        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/permissions_buttons')
                ->setTemplate('adminhtml/permissions/userinfo.phtml')
                ->setUserId($userId)
                ->assign('userData', Mage::getModel('permissions/users')->load($userId))
        );

        $this->renderLayout();
    }

    public function editRoleAction()
    {
        $roleId = $this->getRequest()->getParam('rid');
        if( intval($roleId) > 0 ) {
            $breadCrumb = __('Edit Role');
            $breadCrumbTitle = __('Edit Role');
        } else {
            $breadCrumb = __('Add new Role');
            $breadCrumbTitle = __('Add new Role');
        }

        $this->loadLayout('baseframe');
        $this->_addBreadcrumb(__('System'), __('System Title'), Mage::getUrl('adminhtml/system'));
        $this->_addBreadcrumb(__('Permission'), __('Permission Title'), Mage::getUrl('*/*/index'));
        $this->_addBreadcrumb($breadCrumb, $breadCrumbTitle);

        $this->_addLeft(
            $this->getLayout()->createBlock('adminhtml/permissions_editroles')
        );

        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/permissions_buttons')
                ->setRoleId($roleId)
                ->setTemplate('adminhtml/permissions/roleinfo.phtml')
        );

        $this->renderLayout();
    }

    public function deleteRoleAction()
    {
    	$rid = $this->getRequest()->getParam('id', false);
    	Mage::getModel("permissions/roles")->setId($rid)->delete();

    	$this->_redirect("adminhtml/permissions");
    }

    public function deleteUserAction()
    {
    	$uid = $this->getRequest()->getParam('id', false);
    	Mage::getModel("permissions/users")->setId($uid)->delete();

    	$this->_redirect("adminhtml/permissions");
    }

    public function saveRoleAction()
    {
    	$rid = $this->getRequest()->getParam('role_id', false);

    	$role = Mage::getModel("permissions/roles")
	    		->setId($rid)
	    		->setName($this->getRequest()->getParam('role_name', false))
	    		->setPid($this->getRequest()->getParam('parent_id', false))
	    		->setRoleType('G')
	    		->save();

    	Mage::getModel("permissions/rules")
    		->setRoleId($role->getId())
    		->setResources($this->getRequest()->getParam('resource', false))
    		->saveRel();


    	$rid = $role->getId();
    	$this->_redirect("adminhtml/permissions/editrole/rid/$rid");
    }

    public function saveUserAction()
    {
    	$uid = $this->getRequest()->getParam('user_id', false);
    	$user = Mage::getModel("permissions/users")
	    		->setId($uid)
	    		->setUsername($this->getRequest()->getParam('username', false))
	    		->setFirstname($this->getRequest()->getParam('firstname', false))
	    		->setLastname($this->getRequest()->getParam('lastname', false))
	    		->setEmail($this->getRequest()->getParam('email', false))
	    		->setPassword($this->getRequest()->getParam('password', false));

	    if( !$user->userExists() ) {
	        $user->save();
        	Mage::getModel("permissions/users")
        		->setIds($this->getRequest()->getParam('roles', false))
        		->setUid($this->getRequest()->getParam('user_id', false))
        		->setFirstname($this->getRequest()->getParam('firstname', false))
        		->saveRel();

            $uid = $user->getId();
	    } else {
            Mage::getSingleton('adminhtml/session')->addError('User with this login aleady exists.');
	    }
        $this->_redirect("adminhtml/permissions/edituser/id/$uid");
    }

    public function deleteuserfromroleAction()
    {
    	Mage::getModel("permissions/users")
    		->setUserId($this->getRequest()->getParam('user_id', false))
    		->deleteFromRole();
    	echo json_encode(array('error' => 0, 'error_message' => 'test message'));
    }

    public function adduser2roleAction()
    {
        if( Mage::getModel("permissions/users")
        		->setRoleId($this->getRequest()->getParam('role_id', false))
        		->setUserId($this->getRequest()->getParam('user_id', false))
                ->roleUserExists() === true ) {
            echo json_encode(array('error' => 1, 'error_message' => __('This user already added to the role.')));
        } else {
        	Mage::getModel("permissions/users")
        		->setRoleId($this->getRequest()->getParam('role_id', false))
        		->setUserId($this->getRequest()->getParam('user_id', false))
        		->setFirstname($this->getRequest()->getParam('firstname', false))
        		->add();
       		echo json_encode(array('error' => 0, 'error_message' => 'test message'));
        }
    }

    public function editrolegridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/permissions_role_grid_user')->toHtml());
    }
}
