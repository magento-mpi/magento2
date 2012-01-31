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
class Mage_Api2_Adminhtml_RolesController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Web Services'))
             ->_title($this->__('REST Roles'));

        $this->loadLayout()->_setActiveMenu('system/services/roles');
        /*$this->_addBreadcrumb($this->__('Web services'), $this->__('Web services'));
        $this->_addBreadcrumb($this->__('REST Roles'), $this->__('REST Roles'));
        $this->_addBreadcrumb($this->__('Roles'), $this->__('Roles'));*/

        //$this->_addContent($this->getLayout()->createBlock('adminhtml/api2_roles'));

        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();

        /*$this->getResponse()
            ->setBody($this->getLayout()
            ->createBlock('api2/adminhtml_api_grid_role')
            ->toHtml()
        );*/
    }

    public function newAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Web Services'))
             ->_title($this->__('Rest Roles'));

        $this->loadLayout()->_setActiveMenu('system/services/roles');
        /*$this->_addBreadcrumb($this->__('Web services'), $this->__('Web services'));
        $this->_addBreadcrumb($this->__('REST Roles'), $this->__('REST Roles'));
        $this->_addBreadcrumb($this->__('Roles'), $this->__('Roles'));*/

        $roleId = null;
        $breadCrumb = $this->__('Add New Role');
        $breadCrumbTitle = $this->__('Add New Role');
        $this->_title($this->__('New Role'));

        $this->_addBreadcrumb($breadCrumb, $breadCrumbTitle);

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addLeft(
            $this->getLayout()->createBlock('api2/adminhtml_roles_tabs')
        );
        //$resources = Mage::getModel('api/roles')->getResourcesList();
        $this->_addContent($this->getLayout()->createBlock('api2/adminhtml_roles_buttons'));
        $this->_addJs(
            $this->getLayout()->createBlock('adminhtml/template')
                    ->setTemplate('api/role_users_grid_js.phtml')
        );
        $this->renderLayout();
    }

    public function saveAction()
    {
        $id = $this->getRequest()->getParam('id', false);
        $role = Mage::getModel('api2/acl_global_role')->load($id);

        if (!$role->getId() && $id) {
            Mage::getSingleton('adminhtml/session')->addError(
                $this->__('Role "%s" no longer exists', $role->getData('role_name')));
            $this->_redirect('*/*/');
            return;
        }

        /*$resource = explode(',', $this->getRequest()->getParam('resource', false));
        $roleUsers = $this->getRequest()->getParam('in_role_user', null);
        parse_str($roleUsers, $roleUsers);
        $roleUsers = array_keys($roleUsers);

        $oldRoleUsers = $this->getRequest()->getParam('in_role_user_old');
        parse_str($oldRoleUsers, $oldRoleUsers);
        $oldRoleUsers = array_keys($oldRoleUsers);

        $isAll = $this->getRequest()->getParam('all');
        if ($isAll) {
            $resource = array("all");
        }*/

        try {
            $role = $role
                    ->setRoleName($this->getRequest()->getParam('role_name', false))
                    ->save();

            /*Mage::getModel("api/rules")
                ->setRoleId($role->getId())
                ->setResources($resource)
                ->saveRel();

            /*foreach($oldRoleUsers as $oUid) {
                $this->_deleteUserFromRole($oUid, $role->getId());
            }

            foreach ($roleUsers as $nRuid) {
                $this->_addUserToRole($nRuid, $role->getId());
            }*/

            $id = $role->getId();
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The role has been saved.'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this role.'));
        }

        $this->_redirect('*/*/');
        //$this->_redirect('*/*/edit', array('id' => $id));
        return;
    }

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

    protected function _deleteUserFromRole($userId, $roleId)
    {
        try {
            Mage::getModel("api/user")
                ->setRoleId($roleId)
                ->setUserId($userId)
                ->deleteFromRole();
        } catch (Exception $e) {
            throw $e;
            return false;
        }
        return true;
    }

    protected function _addUserToRole($userId, $roleId)
    {
        $user = Mage::getModel("api/user")->load($userId);
        $user->setRoleId($roleId)->setUserId($userId);

        if( $user->roleUserExists() === true ) {
            return false;
        } else {
            $user->add();
            return true;
        }
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/api/roles_rest');
    }


    /**
     * Get form data
     *
     * @return array
     */
    protected function _getFormData()
    {
        return $this->_getSession()->getData('role_data', true);
    }

    /**
     * Set form data
     *
     * @param $data
     * @return Mage_Api2_Adminhtml_RolesController
     */
    protected function _setFormData($data)
    {
        $this->_getSession()->setData('role_data', $data);
        return $this;
    }
}
