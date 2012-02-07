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
class Mage_Api2_Adminhtml_Api2_RoleController extends Mage_Adminhtml_Controller_Action
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
        /** @var $grid Mage_Api2_Block_Adminhtml_Roles_Tab_Users */
        $grid = $this->getLayout()->getBlock('adminhtml.role.edit.tab.users');
        $grid->setUsers($this->_getUsers($id));

        //NOTE: If we set role than we limit grid to show users possessing this role
        //$grid->setRole($role);

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
        $this->_addBreadcrumb($this->__('Web services'), $this->__('Web services'));
        $this->_addBreadcrumb($this->__('REST Roles'), $this->__('REST Roles'));
        $this->_addBreadcrumb($this->__('Roles'), $this->__('Roles'));

        $breadCrumb = $this->__('Add New Role');
        $breadCrumbTitle = $this->__('Add New Role');
        $this->_title($this->__('New Role'));

        $this->_addBreadcrumb($breadCrumb, $breadCrumbTitle);

        /** @var $head Mage_Page_Block_Html_Head */
        $head = $this->getLayout()->getBlock('head');
        $head->setCanLoadExtJs(true);

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

        /** @var $head Mage_Page_Block_Html_Head */
        $head = $this->getLayout()->getBlock('head');
        $head->setCanLoadExtJs(true);

        /** @var $tabs Mage_Api2_Block_Adminhtml_Roles_Tabs */
        $tabs = $this->getLayout()->getBlock('adminhtml.role.edit.tabs');
        $tabs->setRole($role);
        /** @var $child Mage_Adminhtml_Block_Template */
        foreach ($tabs->getChild() as $child) {
            $child->setData('role', $role);
        }

        /** @var $buttons Mage_Api2_Block_Adminhtml_Roles_Buttons */
        $buttons = $this->getLayout()->getBlock('adminhtml.roles.buttons');
        $buttons->setRole($role);

        /** @var $block Mage_Api2_Block_Adminhtml_Roles_Tab_Resources */
        $block = $this->getLayout()->getBlock('adminhtml.role.edit.tab.resources');

        $this->getLayout()->getBlock('adminhtml.role.edit.tab.users')->setUsers($this->_getUsers($id));

        $this->_addJs(
            $this->getLayout()->createBlock('adminhtml/template')->setTemplate('api2/role/users_grid_js.phtml')
        );

        //TODO remove debug
        ($block->getResTreeJson());
//        exit('EEE');

        $this->renderLayout();
    }

    /**
     * Save role
     *
     * @return mixed
     */
    public function saveAction()
    {
        $request = $this->getRequest();

        $id = $request->getParam('id', false);
        /** @var $role Mage_Api2_Model_Acl_Global_Role */
        $role = Mage::getModel('api2/acl_global_role')->load($id);

        if (!$role->getId() && $id) {
            Mage::getSingleton('adminhtml/session')->addError(
                $this->__('Role "%s" no longer exists', $role->getData('role_name')));
            $this->_redirect('*/*/');
            return;
        }

        $roleUsers  = $request->getParam('in_role_users', null);
        parse_str($roleUsers, $roleUsers);
        $roleUsers = array_keys($roleUsers);

        $oldRoleUsers = $this->getRequest()->getParam('in_role_users_old');
        parse_str($oldRoleUsers, $oldRoleUsers);
        $oldRoleUsers = array_keys($oldRoleUsers);

        /** @var $roleHelper Mage_Api2_Helper_Role */
        $roleHelper = Mage::helper('api2/role');


        /** @var $session Mage_Adminhtml_Model_Session */
        $session = Mage::getSingleton('adminhtml/session');

        try {
            $role->setRoleName($this->getRequest()->getParam('role_name', false))
                    ->save();

            foreach($oldRoleUsers as $oUid) {
                $this->_deleteUserFromRole($oUid, $role->getId());
            }

            foreach ($roleUsers as $nRuid) {
                $this->_addUserToRole($nRuid, $role->getId());
            }

            /** @var $rule Mage_Api2_Model_Acl_Global_Rule */
            $rule = Mage::getModel('api2/acl_global_rule');

            if ($id) {
                $collection = $rule->getCollection();
                $collection->addFilterByRoleId($role->getId());

                /** @var $model Mage_Api2_Model_Acl_Global_Rule */
                foreach ($collection as $model) {
                    $model->delete();
                }
            }

            $resources = $roleHelper->getPostResources();

            $id = $role->getId();
            foreach ($resources as $resourceId => $privileges) {
                foreach ($privileges as $privilege => $allow) {
                    if (!$allow) {
                        continue;
                    }

                    $rule->setId(null)
                            ->isObjectNew(true);

                    $rule->setRoleId($id)
                            ->setResourceId($resourceId)
                            ->setPrivilege($privilege)
                            ->setPermission(Mage_Api2_Model_Acl_Global_Rule_Permission::TYPE_ALLOW)
                            ->save();
                }

            }

            $session->addSuccess($this->__('The role has been saved.'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving role.'));
        }

        $this->_redirect('*/*/');
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

    /**
     * Get users possessing the role
     * @param $id
     * @param bool $json
     * @return array|mixed
     */
    protected function _getUsers($id, $json=false)
    {
        if ( $this->getRequest()->getParam('in_role_users') != "" ) {
            return $this->getRequest()->getParam('in_role_users');
        }

        /** @var $role Mage_Api2_Model_Acl_Global_Role */
        $role = Mage::getModel('api2/acl_global_role');
        $role->setId($id);

        /** @var $resource Mage_Api2_Model_Resource_Acl_Global_Role  */
        $resource = $role->getResource();
        $users = $resource->getRoleUsers($role);

        if (sizeof($users) == 0) {
            $users = array();
        }

        return $users;
    }

    /**
     * Take away user role
     *
     * @param $adminId
     * @param $roleId
     * @return bool
     * @throws Exception
     */
    protected function _deleteUserFromRole($adminId, $roleId)
    {
        try {
            /** @var $resourceModel Mage_Api2_Model_Resource_Acl_Global_Role */
            $resourceModel = Mage::getResourceModel('api2/acl_global_role');
            $resourceModel->deleteAdminToRoleRelation($adminId, $roleId);
        } catch (Exception $e) {
            throw $e;
            return false;
        }
        return true;
    }

    /**
     * Give user a role
     *
     * @param int $adminId
     * @param int $roleId
     * @return bool
     * @throws Exception
     */
    protected function _addUserToRole($adminId, $roleId)
    {
        try {
            /** @var $resourceModel Mage_Api2_Model_Resource_Acl_Global_Role */
            $resourceModel = Mage::getResourceModel('api2/acl_global_role');
            $resourceModel->saveAdminToRoleRelation($adminId, $roleId);
        } catch (Exception $e) {
            throw $e;
            return false;
        }
        return true;
    }
}
