<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Adminhtml_Permissions_UserController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout('baseframe')
            ->_setActiveMenu('system/acl')
            ->_addBreadcrumb(__('System'), __('System'))
            ->_addBreadcrumb(__('Permissions'), __('Permissions'))
            ->_addBreadcrumb(__('Users'), __('Users'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('adminhtml/permissions_user'))
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('user_id');
        $model = Mage::getModel('admin/permissions_user');

        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(__('This user no longer exists'));
                $this->_redirect('*/*/');
                return;
            }
        }
		// Restore previously entered form data from session
        $data = Mage::getSingleton('adminhtml/session')->getUserData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('permissions_user', $model);

        $this->_initAction()
            ->_addBreadcrumb($id ? __('Edit User') : __('New User'), $id ? __('Edit User') : __('New User'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/permissions_user_edit')->setData('action', Mage::getUrl('*/permissions_user/save')))
            ->_addLeft($this->getLayout()->createBlock('adminhtml/permissions_user_edit_tabs'))
            ->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('admin/permissions_user');
            $model->setData($data);
            try {
            	$model->save();
				if ( $uRoles = $this->getRequest()->getParam('roles', false) ) {
				    if ( 1 == sizeof($uRoles) ) {
				        $model->setRoleIds( $uRoles )->setRoleUserId( $model->getUserId() )->saveRelations();
				    }
				}
                Mage::getSingleton('adminhtml/session')->addSuccess(__('User was saved succesfully'));
                Mage::getSingleton('adminhtml/session')->setUserData(false);
                $this->_redirect('*/*/edit', array('user_id' => $model->getUserId()));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setUserData($data);
                $this->_redirect('*/*/edit', array('user_id' => $model->getUserId()));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('user_id')) {
            try {
                $model = Mage::getModel('admin/permissions_user');
                $model->setId($id);
                /*
                $loggedUser = Mage::getSingleton('admin/session')->getUser();
                */
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(__('User was deleted succesfully'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('user_id' => $this->getRequest()->getParam('user_id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(__('Unable to find a user to delete'));
        $this->_redirect('*/*/');
    }

    public function rolesGridAction()
    {
        $id = $this->getRequest()->getParam('user_id');
        $model = Mage::getModel('admin/permissions_user');

        if ($id) {
            $model->load($id);
        }

        Mage::register('permissions_user', $model);
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/permissions_user_edit_tab_roles')->toHtml());
    }

    public function roleGridAction()
    {
        $this->getResponse()
        	->setBody($this->getLayout()
        	->createBlock('adminhtml/permissions_user_grid')
        	->toHtml()
        );
    }

    public function deleteuserfromroleAction()
    {
        Mage::getModel("admin/permissions_user")
            ->setUserId($this->getRequest()->getParam('user_id', false))
            ->deleteFromRole();
        echo json_encode(array('error' => 0, 'error_message' => 'test message'));
    }

    public function adduser2roleAction()
    {
        if (!$this->getRequest()->getParam('user_id', false)) {
        	echo json_encode(array('error' => 1, 'error_message' => __('Invalid request.')));
        	return false;
        }

    	if( Mage::getModel("admin/permissions_user")
                ->setRoleId($this->getRequest()->getParam('role_id', false))
                ->setUserId($this->getRequest()->getParam('user_id', false))
                ->roleUserExists() === true ) {
            echo json_encode(array('error' => 1, 'error_message' => __('This user already added to the role.')));
        } else {
            Mage::getModel("admin/permissions_user")
                ->setRoleId($this->getRequest()->getParam('role_id', false))
                ->setUserId($this->getRequest()->getParam('user_id', false))
                ->setFirstname($this->getRequest()->getParam('firstname', false))
                ->add();
            echo json_encode(array('error' => 0, 'error_message' => 'test message'));
        }
    }

    protected function _isAllowed()
    {
	    return Mage::getSingleton('admin/session')->isAllowed('system/acl/users');
    }

}
