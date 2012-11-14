<?php
/**
 * Controller for web API users management in Magento admin panel.
 *
 * @copyright {}
 */
class Mage_Webapi_Adminhtml_Webapi_UserController extends Mage_Backend_Controller_ActionAbstract
{
    /**
     * Initialize breadcrumbs
     *
     * @return Mage_Webapi_Adminhtml_Webapi_UserController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Mage_Webapi::system_api_webapi_users')
            ->_addBreadcrumb(
            Mage::helper('Mage_Webapi_Helper_Data')->__('Web Services'),
            Mage::helper('Mage_Webapi_Helper_Data')->__('Web Services')
        )
            ->_addBreadcrumb(
            Mage::helper('Mage_Webapi_Helper_Data')->__('API Users'),
            Mage::helper('Mage_Webapi_Helper_Data')->__('API Users')
        );

        return $this;
    }

    /**
     * Show web API users grid
     */
    public function indexAction()
    {
        $this->_initAction();
        $this->_title(Mage::helper('Mage_Webapi_Helper_Data')->__('System'))
            ->_title(Mage::helper('Mage_Webapi_Helper_Data')->__('Web Services'))
            ->_title(Mage::helper('Mage_Webapi_Helper_Data')->__('API Users'));

        $this->renderLayout();
    }

    /**
     * Create New Web API User
     */
    public function newAction()
    {
        $this->getRequest()->setParam('user_id', null);
        $this->_forward('edit');
    }

    /**
     * Edit Web API User
     */
    public function editAction()
    {
        $this->_initAction();
        $this->_title(Mage::helper('Mage_Webapi_Helper_Data')->__('System'))
            ->_title(Mage::helper('Mage_Webapi_Helper_Data')->__('Web Services'))
            ->_title(Mage::helper('Mage_Webapi_Helper_Data')->__('API Users'));

        $userId = (int)$this->getRequest()->getParam('user_id');
        $user = $this->_loadApiUser($userId);
        if (!$user) {
            return;
        }

        // Update title and breadcrumb record
        $actionTitle = $user->getId()
            ? Mage::helper('Mage_Webapi_Helper_Data')->escapeHtml($user->getApiKey())
            : Mage::helper('Mage_Webapi_Helper_Data')->__('New API User');
        $this->_title($actionTitle);
        $this->_addBreadcrumb($actionTitle, $actionTitle);

        // Restore previously entered form data from session
        $data = $this->_getSession()->getWebapiUserData(true);
        if (!empty($data)) {
            $user->setData($data);
        }

        /** @var Mage_Webapi_Block_Adminhtml_User_Edit $editBlock */
        $editBlock = $this->getLayout()->getBlock('webapi.user.edit');
        if ($editBlock) {
            $editBlock->setApiUser($user);
        }
        /** @var Mage_Webapi_Block_Adminhtml_User_Edit_Tabs $tabsBlock */
        $tabsBlock = $this->getLayout()->getBlock('webapi.user.edit.tabs');
        if ($tabsBlock) {
            $tabsBlock->setApiUser($user);
        }

        $this->renderLayout();
    }

    /**
     * Save Web API User
     */
    public function saveAction()
    {
        $userId = (int)$this->getRequest()->getPost('user_id');
        $data = $this->getRequest()->getPost();
        $redirectBack = false;
        if ($data) {
            $user = $this->_loadApiUser($userId);
            if (!$user) {
                return;
            }

            $user->setData($data);
            try {
                $this->_validateUserData($user);
                $user->save();
                $userId = $user->getId();

                $this->_getSession()
                    ->setWebapiUserData(null)
                    ->addSuccess(Mage::helper('Mage_Webapi_Helper_Data')->__('The API user has been saved.'));
                $redirectBack = $this->getRequest()->has('back');
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()
                    ->setWebapiUserData($data)
                    ->addError($e->getMessage());
                $redirectBack = true;
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()
                    ->setWebapiUserData($data)
                    ->addError($e->getMessage());
                $redirectBack = true;
            }
        }
        if ($redirectBack) {
            $this->_redirect('*/*/edit', array('user_id' => $userId));
        } else {
            $this->_redirect('*/*/');
        }
    }

    /**
     * Delete User
     */
    public function deleteAction()
    {
        $userId = (int)$this->getRequest()->getParam('user_id');
        if ($userId) {
            $user = $this->_loadApiUser($userId);
            if (!$user) {
                return;
            }
            try {
                $user->delete();

                $this->_getSession()->addSuccess(
                    Mage::helper('Mage_Webapi_Helper_Data')->__('The API user has been deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('user_id' => $userId));
                return;
            }
        }
        $this->_getSession()->addError(
            Mage::helper('Mage_Webapi_Helper_Data')->__('Unable to find a user to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * AJAX Web API Users grid
     */
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Web API User Roles grid
     */
    public function rolesgridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Check ACL
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Webapi::webapi_users');
    }

    /**
     * Validate Web API User data
     *
     * @param Mage_Webapi_Model_Acl_User $user
     * @return boolean
     * @throws Magento_Validator_Exception
     */
    protected function _validateUserData($user)
    {
        $group = $user->isObjectNew() ? 'create' : 'update';
        $validator = $this->_objectManager->get('Mage_Core_Model_Validator_Factory')
            ->createValidator('api_user', $group);
        if (!$validator->isValid($user)) {
            throw new Magento_Validator_Exception($validator->getMessages());
        }
    }

    /**
     * Load Web API User
     *
     * @param int $userId
     * @return bool|Mage_Webapi_Model_Acl_User
     */
    protected function _loadApiUser($userId)
    {
        /** @var Mage_Webapi_Model_Acl_User $user */
        $user = Mage::getModel('Mage_Webapi_Model_Acl_User')->load($userId);
        if (!$user->getId() && $userId) {
            $this->_getSession()->addError(
                Mage::helper('Mage_Webapi_Helper_Data')->__('This user no longer exists.')
            );
            $this->_redirect('*/*/');
            return false;
        }
        return $user;
    }
}
