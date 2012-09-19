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
 * Adminhtml roles controller
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Adminhtml_Webapi_RoleController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('Mage_Webapi::system_api_webapi_roles');
        $this->_addBreadcrumb($this->__('Web Api'), $this->__('Web Api'));
        $this->_addBreadcrumb($this->__('Roles'), $this->__('Roles'));
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Web Api'))
             ->_title($this->__('Roles'));
        $this->_initAction();
        $this->renderLayout();
    }

    public function roleGridAction()
    {
        $this->getResponse()
            ->setBody($this->getLayout()
            ->createBlock('Mage_Webapi_Block_Adminhtml_Grid_Role')
            ->toHtml()
        );
    }

    /**
     * Edit Web API role
     */
    public function editAction()
    {
        $this->_initAction();
        $this->_title($this->__('System'))
             ->_title($this->__('Web Services'))
             ->_title($this->__('API Roles'));

        $roleId = $this->getRequest()->getParam('role_id');

        /** @var $user Mage_Webapi_Model_Acl_User */
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


        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->_addJs(
            $this->getLayout()
                ->createBlock('Mage_Adminhtml_Block_Template')
                ->setTemplate('api/role_users_grid_js.phtml')
        );


        // Restore previously entered form data from session
        $data = $this->_getSession()->getWebapiUserData(true);
        if (!empty($data)) {
            $user->setData($data);
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

    public function deleteAction()
    {
        $role_id = $this->getRequest()->getParam('role_id', false);

        try {
            Mage::getModel('Mage_Webapi_Model_Role')->load($role_id)->delete();
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess($this->__('The role has been deleted.'));
        } catch (Exception $e) {
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($this->__('An error occurred while deleting this role.'));
        }

        $this->_redirect("*/*/");
    }

    public function saveAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            $role_id = $this->getRequest()->getParam('role_id', false);
            $role = Mage::getModel('Mage_Webapi_Model_Acl_Role')->load($role_id);
            if (!$role->getId() && $role_id) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($this->__('This Role no longer exists'));
                $this->_redirect('*/*/');
                return;
            }


            $role->setData($data);
            try {
                $role->save();

                $this->_getSession()->addSuccess(
                    Mage::helper('Mage_Webapi_Helper_Data')->__('The API role has been saved.'));
                $this->_getSession()->setWebapiRoleData(false);
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setWebapiRoleData($data);
                $this->_redirect('*/*/edit', array('role_id' => $role->getId()));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Webapi::webapi_roles');
    }
}
