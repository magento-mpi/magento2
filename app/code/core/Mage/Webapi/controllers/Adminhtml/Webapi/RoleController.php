<?php
/**
 * Controller for web API roles management in Magento admin panel.
 *
 * @copyright {}
 */
class Mage_Webapi_Adminhtml_Webapi_RoleController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init
     *
     * @return Mage_Webapi_Adminhtml_Webapi_RoleController
     */
    protected function _initAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('Mage_Webapi::system_api_webapi_roles');
        $this->_addBreadcrumb(
            Mage::helper('Mage_Webapi_Helper_Data')->__('Web Api'),
            Mage::helper('Mage_Webapi_Helper_Data')->__('Web Api')
        );
        $this->_addBreadcrumb(
            Mage::helper('Mage_Webapi_Helper_Data')->__('API Roles'),
            Mage::helper('Mage_Webapi_Helper_Data')->__('API Roles')
        );
        return $this;
    }

    /**
     * Web API Roles grid
     */
    public function indexAction()
    {
        $this->_title(Mage::helper('Mage_Webapi_Helper_Data')->__('System'))
            ->_title(Mage::helper('Mage_Webapi_Helper_Data')->__('Web Api'))
            ->_title(Mage::helper('Mage_Webapi_Helper_Data')->__('API Roles'));
        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * AJAX Web API Roles grid
     */
    public function rolegridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Grid in edit role form
     */
    public function usersgridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Edit Web API role
     */
    public function editAction()
    {
        $this->_initAction();
        $this->_title(Mage::helper('Mage_Webapi_Helper_Data')->__('System'))
            ->_title(Mage::helper('Mage_Webapi_Helper_Data')->__('Web Api'))
            ->_title(Mage::helper('Mage_Webapi_Helper_Data')->__('API Roles'));

        $roleId = $this->getRequest()->getParam('role_id');

        /** @var Mage_Webapi_Model_Acl_Role $role */
        $role = Mage::getModel('Mage_Webapi_Model_Acl_Role');
        if ($roleId) {
            $role->load($roleId);
            if (!$role->getId()) {
                $this->_getSession()->addError(
                    Mage::helper('Mage_Webapi_Helper_Data')->__('This API Role no longer exists.')
                );
                $this->_redirect('*/*/');
                return;
            }
            $this->_addBreadcrumb(
                Mage::helper('Mage_Webapi_Helper_Data')->__('Edit API Role'),
                Mage::helper('Mage_Webapi_Helper_Data')->__('Edit API Role')
            );
            $this->_title(Mage::helper('Mage_Webapi_Helper_Data')->__('Edit API Role'));
        } else {
            $this->_addBreadcrumb(
                Mage::helper('Mage_Webapi_Helper_Data')->__('Add New API Role'),
                Mage::helper('Mage_Webapi_Helper_Data')->__('Add New API Role')
            );
            $this->_title(Mage::helper('Mage_Webapi_Helper_Data')->__('New API Role'));
        }

        // Restore previously entered form data from session
        $data = $this->_getSession()->getWebapiUserData(true);
        if (!empty($data)) {
            $role->setData($data);
        }

        /** @var Mage_Webapi_Block_Adminhtml_Role_Edit $editBlock */
        $editBlock = $this->getLayout()->getBlock('webapi.role.edit');
        if ($editBlock) {
            $editBlock->setApiRole($role);
        }

        /** @var Mage_Webapi_Block_Adminhtml_Role_Edit_Tabs $tabsBlock */
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
            Mage::getModel('Mage_Webapi_Model_Acl_Role')->load($roleId)->delete();
            $this->_getSession()->addSuccess(
                Mage::helper('Mage_Webapi_Helper_Data')->__('The API role has been deleted.')
            );
        } catch (Exception $e) {
            $this->_getSession()->addError(
                Mage::helper('Mage_Webapi_Helper_Data')->__('An error occurred while deleting this role.')
            );
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
            /** @var Mage_Webapi_Model_Acl_Role $role */
            $role = Mage::getModel('Mage_Webapi_Model_Acl_Role')->load($roleId);
            if (!$role->getId() && $roleId) {
                $this->_getSession()->addError(
                    Mage::helper('Mage_Webapi_Helper_Data')->__('This Role no longer exists')
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
                    Mage::helper('Mage_Webapi_Helper_Data')->__('The API role has been saved.')
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
     * Validate Web API Role data
     *
     * @param Mage_Webapi_Model_Acl_Role $role
     * @return boolean
     * @throws Magento_Validator_Exception
     */
    protected function _validateRole($role)
    {
        $group = $role->isObjectNew() ? 'create' : 'update';
        $validator = $this->_objectManager->get('Mage_Core_Model_Validator_Factory')
            ->createValidator('api_role', $group);
        if (!$validator->isValid($role)) {
            throw new Magento_Validator_Exception($validator->getMessages());
        }
    }

    /**
     * Save Role resources
     *
     * @param integer $roleId
     * @param boolean $isNewRole
     */
    protected function _saveResources($roleId, $isNewRole)
    {
        // parse resource list
        $resources = explode(',', $this->getRequest()->getParam('resource', false));
        $isAll = $this->getRequest()->getParam('all');
        if ($isAll) {
            $resources = array(Mage_Webapi_Model_Acl_Rule::API_ACL_RESOURCES_ROOT_ID);
        } elseif (in_array(Mage_Webapi_Helper_Data::RESOURCES_TREE_ROOT_ID, $resources)) {
            unset($resources[array_search(
                Mage_Webapi_Helper_Data::RESOURCES_TREE_ROOT_ID,
                $resources
            )]);
        }

        $saveResourcesFlag = true;
        if (!$isNewRole) {
            // Check changes
            $rulesSet = Mage::getModel('Mage_Webapi_Model_Acl_Rule')->getByRole($roleId)->load();
            if ($rulesSet->count() == count($resources)) {
                $saveResourcesFlag = false;
                /** @var Mage_Webapi_Model_Acl_Rule $rule */
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
     * @param integer $roleId
     */
    protected function _saveUsers($roleId)
    {
        // parse users list
        $roleUsers = $this->_parseRoleUsers($this->getRequest()->getParam('in_role_user', null));
        $oldRoleUsers = $this->_parseRoleUsers($this->getRequest()->getParam('in_role_user_old', null));

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
     * Parse request string with users
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
     * Check access rights
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Webapi::webapi_roles');
    }

}
