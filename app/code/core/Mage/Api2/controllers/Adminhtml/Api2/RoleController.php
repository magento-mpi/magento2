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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml roles controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Adminhtml_Api2_RolesController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Show grid
     */
    public function indexAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Web Services'))
             ->_title($this->__('REST Roles'));

        $this->loadLayout()->_setActiveMenu('system/services/roles');
        $this->_addBreadcrumb($this->__('Web services'), $this->__('Web services'));
        $this->_addBreadcrumb($this->__('REST Roles'), $this->__('REST Roles'));
        $this->_addBreadcrumb($this->__('Roles'), $this->__('Roles'));

        $this->renderLayout();
    }

    /**
     * Updating grid by ajax
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Updating users grid by ajax
     */
    public function usersGridAction()
    {
        $id = $this->getRequest()->getParam('id', false);

        /** @var $role Mage_Api2_Model_Acl_Global_Role */
        $role = Mage::getModel('api2/acl_global_role')->load($id);

        $this->loadLayout();
        $this->getLayout()->getBlock('api2_role_tab_users.grid')->setRole($role);

        $this->renderLayout();
    }

    /**
     * Create new role
     */
    public function newAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Web Services'))
             ->_title($this->__('Rest Roles'));

        $this->loadLayout()->_setActiveMenu('system/services/roles');
        /*$this->_addBreadcrumb($this->__('Web services'), $this->__('Web services'));
        $this->_addBreadcrumb($this->__('REST Roles'), $this->__('REST Roles'));
        $this->_addBreadcrumb($this->__('Roles'), $this->__('Roles'));*/

        $breadCrumb = $this->__('Add New Role');
        $breadCrumbTitle = $this->__('Add New Role');
        $this->_title($this->__('New Role'));

        $this->_addBreadcrumb($breadCrumb, $breadCrumbTitle);

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addLeft(
            $this->getLayout()->createBlock('api2/adminhtml_roles_tabs')
        );
        $this->_addContent($this->getLayout()->createBlock('api2/adminhtml_roles_buttons'));
        $this->_addJs(
            $this->getLayout()->createBlock('adminhtml/template')
                ->setTemplate('api/role_users_grid_js.phtml')
        );
        $this->renderLayout();
    }

    /**
     * Save role
     *
     * @return mixed
     */
    public function saveAction()
    {
        $id = $this->getRequest()->getParam('id', false);
        /** @var $role Mage_Api2_Model_Acl_Global_Role */
        $role = Mage::getModel('api2/acl_global_role')->load($id);

        if (!$role->getId() && $id) {
            Mage::getSingleton('adminhtml/session')->addError(
                $this->__('Role "%s" no longer exists', $role->getData('role_name')));
            $this->_redirect('*/*/');
            return;
        }

        try {
            $role->setRoleName($this->getRequest()->getParam('role_name', false))->save();
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The role has been saved.'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this role.'));
        }

        $this->_redirect('*/*/');
    }

    /**
     * Edit role
     */
    public function editAction()
    {
        $id = (int) $this->getRequest()->getParam('id');
        /** @var $role Mage_Api2_Model_Acl_Global_Role */
        $role = Mage::getModel('api2/acl_global_role')->load($id);

        $this->loadLayout()->_setActiveMenu('system/services/roles');

        $this->_title($this->__('System'))
             ->_title($this->__('Web Services'))
             ->_title($this->__('Rest Roles'));

        $breadCrumb = $this->__('Edit Role');
        $breadCrumbTitle = $this->__('Edit Role');
        $this->_title($this->__('Edit Role'));
        $this->_addBreadcrumb($breadCrumb, $breadCrumbTitle);

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addLeft(
            $this->getLayout()->createBlock('api2/adminhtml_roles_tabs')->setRole($role)

        );

        $this->_addContent(
            $this->getLayout()->createBlock('api2/adminhtml_roles_buttons')->setRole($role)
        );

        $this->renderLayout();
    }

    /**
     * Delete role
     */
    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id', false);

        try {
            /** @var $model Mage_Api2_Model_Acl_Global_Role */
            $model = Mage::getModel("api2/acl_global_role");
            $model->load($id)->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Role "%s" has been deleted.', $model->getRoleName()));
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while deleting the role.'));
        }

        $this->_redirect("*/*/");
    }

    /**
     * Check against ACL
     *
     * @return mixed
     */
    protected function _isAllowed()
    {
            return Mage::getSingleton('admin/session')->isAllowed('system/api/roles_rest');
        }

    /**
     * Get API2 roles ajax grid action
     */
    public function rolesGridAction()
    {
        /** @var $model Mage_Admin_Model_User */
        $model = Mage::getModel('admin/user');
        $model->load($this->getRequest()->getParam('user_id'));

        Mage::register('permissions_user', $model);
        $this->getResponse()
            ->setBody($this->getLayout()->createBlock('api2/adminhtml_permissions_user_edit_tab_roles')->toHtml());
    }
}
