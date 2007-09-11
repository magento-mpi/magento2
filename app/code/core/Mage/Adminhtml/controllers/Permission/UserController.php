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

/**
 * Adminhtml permissions users controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Adminhtml_Permission_UserController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('system/acl');
        $this->_addBreadcrumb(__('System'), __('System'));
        $this->_addBreadcrumb(__('Permissions'), __('Permissions'));
        $this->_addBreadcrumb(__('Users'), __('Users'));

        $this->_addContent($this->getLayout()->createBlock('adminhtml/permissions_users'));
        $this->renderLayout();
    }

    public function userGridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/permissions_grid_user')->toHtml());
    }

    public function editUserAction()
    {
        $userId = $this->getRequest()->getParam('id');
        $this->loadLayout('baseframe');

        if( intval($userId) > 0 ) {
            $breadCrumb = __('Edit User');
            $breadCrumbTitle = __('Edit User');
        } else {
            $breadCrumb = __('Add new User');
            $breadCrumbTitle = __('Add new User');
        }

        $this->_addBreadcrumb(__('System'), __('System'));
        $this->_addBreadcrumb(__('Permission'), __('Permission'));
        $this->_addBreadcrumb(__('Users'), __('Users'), Mage::getUrl('*/*/'));
        $this->_addBreadcrumb($breadCrumb, $breadCrumbTitle);
        $this->_setActiveMenu('system/acl');

        $this->_addLeft(
            $this->getLayout()->createBlock('adminhtml/permissions_edituser')
                 ->setUserId($userId)
        );

        if ($formData =  Mage::getSingleton('adminhtml/session')->getFormData(true) ) {
            $data = Mage::getModel('permissions/users')->setData($formData);
        } else {
            $data = Mage::getModel('permissions/users')->load($userId);
        }

        Mage::register('user_data', $data);

        $this->_addContent($this->getLayout()->createBlock('adminhtml/permissions_buttons'));

        $this->renderLayout();
    }

    public function deleteUserAction()
    {
        $uid = $this->getRequest()->getParam('id', false);
        try {
            Mage::getModel("permissions/users")->setId($uid)->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess('User successfully deleted.');
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError('Error while deleting this user. Please, try again later.');
        }
        $this->getResponse()->setRedirect(Mage::getUrl("*/permission_user"));
    }

    public function saveUserAction()
    {
        $uid = $this->getRequest()->getParam('user_id', false);
        $user = Mage::getModel("permissions/users")
                ->setId($uid)
                ->setUsername($this->getRequest()->getParam('username', false))
                ->setFirstname($this->getRequest()->getParam('firstname', false))
                ->setLastname($this->getRequest()->getParam('lastname', false))
                ->setPassword($this->getRequest()->getParam('new_password', false))
                ->setEmail(strtolower($this->getRequest()->getParam('email', false)))
                ->setIsActive($this->getRequest()->getParam('is_active', false));

        if( !$user->userExists() ) {
            try {
                $user->save();
                Mage::getModel("permissions/users")
                    ->setIds($this->getRequest()->getParam('roles', false))
                    ->setUid($this->getRequest()->getParam('user_id', false))
                    ->setFirstname($this->getRequest()->getParam('firstname', false))
                    ->saveRel();

                $uid = $user->getId();

                if ( !$user->hasAssigned2Role() or !$this->getRequest()->getParam('roles', false) ) {
                	$user->setIsActive('0')->save();
                	Mage::getSingleton('adminhtml/session')->addError("No roles were assigned to this user. Account won't be active.");
                	$this->getResponse()->setRedirect(Mage::getUrl("*/*/edituser/id/{$uid}"));
                } else {
	                Mage::getSingleton('adminhtml/session')->addSuccess('User successfully saved.');
    	            $this->getResponse()->setRedirect(Mage::getUrl("*/*/edituser/id/{$uid}"));
                }

            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError('Error while saving this user. Please try again later.');
                $this->getResponse()->setRedirect(Mage::getUrl("*/*/edituser"));
            }
        } else {
            Mage::getSingleton('adminhtml/session')->setFormData($this->getRequest()->getPost());
            Mage::getSingleton('adminhtml/session')->addError('User with the same login or email aleady exists.');
            $this->getResponse()->setRedirect(Mage::getUrl("*/*/edituser"));
        }
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

    protected function _isAllowed()
    {
	    return Mage::getSingleton('admin/session')->isAllowed('system/acl/users');
    }
}
