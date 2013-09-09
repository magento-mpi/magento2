<?php
/**
 * Controller for web API roles management in Magento admin panel.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Adminhtml_Webapi_Role extends Magento_Adminhtml_Controller_Action
{
    /**
     * Init.
     *
     * @return Magento_Webapi_Controller_Adminhtml_Webapi_Role
     */
    protected function _initAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('Magento_Webapi::system_api_webapi_roles');
        $this->_addBreadcrumb(
            __('Web Api'),
            __('Web Api')
        );
        $this->_addBreadcrumb(
            __('API Roles'),
            __('API Roles')
        );
        return $this;
    }

    /**
     * Web API roles grid.
     */
    public function indexAction()
    {
        $this->_title(__('API Roles'));
        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * AJAX Web API roles grid.
     */
    public function rolegridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Grid in edit role form.
     */
    public function usersgridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Edit Web API role.
     */
    public function editAction()
    {
        $this->_initAction();
        $this->_title(__('API Roles'));

        $roleId = $this->getRequest()->getParam('role_id');

        /** @var Magento_Webapi_Model_Acl_Role $role */
        $role = $this->_objectManager->create('Magento_Webapi_Model_Acl_Role');
        if ($roleId) {
            $role->load($roleId);
            if (!$role->getId()) {
                $this->_getSession()->addError(
                    __('This API role no longer exists.')
                );
                $this->_redirect('*/*/');
                return;
            }
            $this->_addBreadcrumb(
                __('Edit API Role'),
                __('Edit API Role')
            );
            $this->_title(__('Edit API Role'));
        } else {
            $this->_addBreadcrumb(
                __('Add New API Role'),
                __('Add New API Role')
            );
            $this->_title(__('New API Role'));
        }

        // Restore previously entered form data from session
        $data = $this->_getSession()->getWebapiUserData(true);
        if (!empty($data)) {
            $role->setData($data);
        }

        /** @var Magento_Webapi_Block_Adminhtml_Role_Edit $editBlock */
        $editBlock = $this->getLayout()->getBlock('webapi.role.edit');
        if ($editBlock) {
            $editBlock->setApiRole($role);
        }

        /** @var Magento_Webapi_Block_Adminhtml_Role_Edit_Tabs $tabsBlock */
        $tabsBlock = $this->getLayout()->getBlock('webapi.role.edit.tabs');
        if ($tabsBlock) {
            $tabsBlock->setApiRole($role);
        }

        $this->renderLayout();
    }

    /**
     * Remove role.
     */
    public function deleteAction()
    {
        $roleId = $this->getRequest()->getParam('role_id', false);

        try {
            $this->_objectManager->create('Magento_Webapi_Model_Acl_Role')->load($roleId)->delete();
            $this->_getSession()->addSuccess(
                __('The API role has been deleted.')
            );
        } catch (Exception $e) {
            $this->_getSession()->addError(
                __('An error occurred while deleting this role.')
            );
        }

        $this->_redirect("*/*/");
    }

    /**
     * Save role.
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            $roleId = $this->getRequest()->getPost('role_id', false);
            /** @var Magento_Webapi_Model_Acl_Role $role */
            $role = $this->_objectManager->create('Magento_Webapi_Model_Acl_Role')->load($roleId);
            if (!$role->getId() && $roleId) {
                $this->_getSession()->addError(
                    __('This role no longer exists.')
                );
                $this->_redirect('*/*/');
                return;
            }
            $role->setData($data);

            try {
                $this->_validateRole($role);
                $role->save();

                $isNewRole = empty($roleId);
                $this->_saveResources($role->getId(), $isNewRole);
                $this->_saveUsers($role->getId());

                $this->_getSession()->addSuccess(
                    __('The API role has been saved.')
                );
                $this->_getSession()->setWebapiRoleData(false);

                if ($roleId && !$this->getRequest()->has('continue')) {
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
     * Validate Web API role data.
     *
     * @param Magento_Webapi_Model_Acl_Role $role
     * @throws Magento_Validator_Exception
     */
    protected function _validateRole($role)
    {
        $group = $role->isObjectNew() ? 'create' : 'update';
        /** @var Magento_Core_Model_Validator_Factory $validatorFactory */
        $validatorFactory = $this->_objectManager->get('Magento_Core_Model_Validator_Factory');
        $validator = $validatorFactory->createValidator('api_role', $group);
        if (!$validator->isValid($role)) {
            throw new Magento_Validator_Exception($validator->getMessages());
        }
    }

    /**
     * Save role resources.
     *
     * @param integer $roleId
     * @param boolean $isNewRole
     */
    protected function _saveResources($roleId, $isNewRole)
    {
        // parse resource list
        $resources = explode(',', $this->getRequest()->getParam('resource', false));
        $isAll = $this->getRequest()->getParam('all');
        $rootResource = $this->_objectManager->get('Magento_Core_Model_Acl_RootResource');
        if ($isAll) {
            $resources = array($rootResource->getId());
        } elseif (in_array($rootResource->getId(), $resources)) {
            unset($resources[array_search(
                $rootResource->getId(),
                $resources
            )]);
        }

        $saveResourcesFlag = true;
        if (!$isNewRole) {
            // Check changes
            /** @var Magento_Webapi_Model_Resource_Acl_Rule $ruleResource */
            $ruleResource = $this->_objectManager->get('Magento_Webapi_Model_Resource_Acl_Rule');
            $oldResources = $ruleResource->getResourceIdsByRole($roleId);
            if (count($oldResources) == count($resources) && !array_diff($oldResources, $resources)) {
                $saveResourcesFlag = false;
            }
        }

        if ($saveResourcesFlag) {
            $this->_objectManager->create('Magento_Webapi_Model_Acl_Rule')
                ->setRoleId($roleId)
                ->setResources($resources)
                ->saveResources();
        }
    }

    /**
     * Save linked users.
     *
     * @param integer $roleId
     */
    protected function _saveUsers($roleId)
    {
        // parse users list
        $roleUsers = $this->_parseRoleUsers($this->getRequest()->getParam('in_role_user'));
        $oldRoleUsers = $this->_parseRoleUsers($this->getRequest()->getParam('in_role_user_old'));

        if ($roleUsers != $oldRoleUsers) {
            foreach ($oldRoleUsers as $userId) {
                $user = $this->_objectManager->create('Magento_Webapi_Model_Acl_User')->load($userId);
                $user->setRoleId(null)->save();
            }

            foreach ($roleUsers as $userId) {
                $user = $this->_objectManager->create('Magento_Webapi_Model_Acl_User')->load($userId);
                $user->setRoleId($roleId)->save();
            }
        }
    }

    /**
     * Parse request string with users.
     *
     * @param string $roleUsers
     * @return array
     */
    protected function _parseRoleUsers($roleUsers)
    {
        parse_str($roleUsers, $roleUsers);
        if ($roleUsers && count($roleUsers)) {
            return array_keys($roleUsers);
        }

        return array();
    }

    /**
     * Check access rights.
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Webapi::webapi_roles');
    }

}
