<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Web API Adminhtml roles controller
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Adminhtml_Webapi_RoleController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init
     * @return Mage_Webapi_Adminhtml_Webapi_RoleController
     */
    protected function _initAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('Mage_Webapi::system_api_webapi_roles');
        $this->_addBreadcrumb($this->__('Web Api'), $this->__('Web Api'));
        $this->_addBreadcrumb($this->__('Roles'), $this->__('Roles'));
        return $this;
    }

    /**
     * Web API Roles grid
     */
    public function indexAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Web Api'))
             ->_title($this->__('Roles'));
        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * AJAX Web API Roles grid
     */
    public function roleGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Edit Web API role
     */
    public function editAction()
    {
        $this->_initAction();
        $this->_title($this->__('System'))
             ->_title($this->__('Web Api'))
             ->_title($this->__('API Roles'));

        $roleId = $this->getRequest()->getParam('role_id');

        /** @var $role Mage_Webapi_Model_Acl_Role */
        $role = Mage::getModel('Mage_Webapi_Model_Acl_Role');
        if ($roleId) {
            $role->load($roleId);
            if (!$role->getId()) {
                $this->_getSession()->addError(
                    Mage::helper('Mage_Webapi_Helper_Data')->__('This API Role no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
            $this->_addBreadcrumb('Edit Role', 'Edit Role');
            $this->_title($this->__('Edit Role'));
        } else {
            $this->_addBreadcrumb('Add New Role', 'Add New Role');
            $this->_title($this->__('New Role'));
        }

        // Restore previously entered form data from session
        $data = $this->_getSession()->getWebapiUserData(true);
        if (!empty($data)) {
            $role->setData($data);
        }

        /** @var $editBlock Mage_Webapi_Block_Adminhtml_Role_Edit */
        $editBlock = $this->getLayout()->getBlock('webapi.role.edit');
        if ($editBlock) {
            $editBlock->setApiRole($role);
        }

        /** @var $tabsBlock Mage_Webapi_Block_Adminhtml_Role_Edit_Tabs */
        $tabsBlock = $this->getLayout()->getBlock('webapi.role.edit.tabs');
        if ($tabsBlock) {
            $tabsBlock->setApiRole($role);
        }

        $this->renderLayout();
    }

    /**
     * Remove role
     */
    public function deleteAction()
    {
        $roleId = $this->getRequest()->getParam('role_id', false);

        try {
            Mage::getModel('Mage_Webapi_Model_Role')->load($roleId)->delete();
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess($this->__('The role has been deleted.'));
        } catch (Exception $e) {
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(
                $this->__('An error occurred while deleting this role.'));
        }

        $this->_redirect("*/*/");
    }

    /**
     * Save role
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            $roleId = $this->getRequest()->getPost('role_id', false);
            /** @var $role Mage_Webapi_Model_Acl_Role */
            $role = Mage::getModel('Mage_Webapi_Model_Acl_Role')->load($roleId);
            if (!$role->getId() && $roleId) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($this->__('This Role no longer exists'));
                $this->_redirect('*/*/');
                return;
            }
            $role->setData($data);

            try {
                $this->_validateRole($role);
                $role->save();

                $this->_saveResources($role->getId(), $roleId);
                $this->_saveUsers($role->getId());

                $this->_getSession()->addSuccess(
                    Mage::helper('Mage_Webapi_Helper_Data')->__('The API role has been saved.'));
                $this->_getSession()->setWebapiRoleData(false);

                if ($roleId) {
                    $this->_redirect('*/*/');
                } else {
                    $this->_redirect('*/*/edit', array('role_id' => $role->getId()));
                }
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setWebapiRoleData($data);
                $this->_redirect('*/*/edit', array('role_id' => $role->getId()));
            }
        }
    }

    /**
     * Validate Web API Role data
     *
     * @throws Mage_Core_Exception
     *
     * @param Mage_Webapi_Model_Acl_Role $role
     * @return bool
     */
    protected function _validateRole($role)
    {
        if (!$role->getRoleName()) {
            Mage::throwException(Mage::helper('Mage_Webapi_Helper_Data')->__('Role name is required.'));
        }
        return true;
    }

    /**
     * Save Role resources
     *
     * @param $roleId
     * @param $oldRoleId
     */
    protected function _saveResources($roleId, $oldRoleId)
    {
        // parse resource list
        $resources = explode(',', $this->getRequest()->getParam('resource', false));
        $isAll = $this->getRequest()->getParam('all');
        if ($isAll) {
            $resources = array(Mage_Webapi_Model_Acl_Rule::API_ACL_RESOURCES_ROOT_ID);
        } else if (in_array('__root__', $resources)) {
            unset($resources[array_search('__root__', $resources)]);
        }

        $saveResourcesFlag = true;
        if ($oldRoleId) {
            // Check changes
            $rulesSet = Mage::getResourceModel('Mage_Webapi_Model_Resource_Acl_Rule_Collection')
                ->getByRoles($oldRoleId)->load();
            if ($rulesSet->count() == count($resources)) {
                $saveResourcesFlag = false;
                /** @var $rule Mage_Webapi_Model_Acl_Rule */
                foreach ($rulesSet as $rule) {
                    if (!in_array($rule->getResourceId(), $resources)) {
                        $saveResourcesFlag = true;
                        break;
                    }
                }
            }
        }

        if ($saveResourcesFlag) {
            Mage::getModel('Mage_Webapi_Model_Acl_Rule')
                ->setRoleId($roleId)
                ->setResources($resources)
                ->saveResources();
        }
    }

    /**
     * Save linked users
     *
     * @param $roleId
     */
    protected function _saveUsers($roleId)
    {
        // parse users list
        $roleUsers  = $this->getRequest()->getParam('in_role_user', null);
        parse_str($roleUsers, $roleUsers);
        $roleUsers = array_keys($roleUsers);

        $oldRoleUsers = $this->getRequest()->getParam('in_role_user_old');
        parse_str($oldRoleUsers, $oldRoleUsers);
        $oldRoleUsers = array_keys($oldRoleUsers);

        if ($roleUsers != $oldRoleUsers) {
            foreach ($oldRoleUsers as $userId) {
                $user = Mage::getModel('Mage_Webapi_Model_Acl_User')->load($userId);
                $user->setRoleId(null)->save();
            }

            foreach ($roleUsers as $userId) {
                $user = Mage::getModel('Mage_Webapi_Model_Acl_User')->load($userId);
                $user->setRoleId($roleId)->save();
            }
        }
    }

    /**
     * Grid in edit role form
     */
    public function editrolegridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('Mage_Webapi_Block_Adminhtml_Role_Grid_User')->toHtml()
        );
    }

    /**
     * Check access rights
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Webapi::webapi_roles');
    }

}
