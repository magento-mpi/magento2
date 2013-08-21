<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml roles controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Controller_Api_Role extends Magento_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('Magento_Api::system_legacy_api_roles');
        $this->_addBreadcrumb(__('Web services'), __('Web services'));
        $this->_addBreadcrumb(__('Permissions'), __('Permissions'));
        $this->_addBreadcrumb(__('Roles'), __('Roles'));
        return $this;
    }

    public function indexAction()
    {
        $this->_title(__('Roles'));

        $this->_initAction();

        $this->_addContent($this->getLayout()->createBlock('Magento_Adminhtml_Block_Api_Roles'));

        $this->renderLayout();
    }

    public function roleGridAction()
    {
        $this->getResponse()
            ->setBody($this->getLayout()
            ->createBlock('Magento_Adminhtml_Block_Api_Grid_Role')
            ->toHtml()
        );
    }

    public function editRoleAction()
    {
        $this->_title(__('Roles'));

        $this->_initAction();

        $roleId = $this->getRequest()->getParam('rid');
        if( intval($roleId) > 0 ) {
            $breadCrumb = __('Edit Role');
            $breadCrumbTitle = __('Edit Role');
            $this->_title(__('Edit Role'));
        } else {
            $breadCrumb = __('Add New Role');
            $breadCrumbTitle = __('Add New Role');
            $this->_title(__('New Role'));
        }
        $this->_addBreadcrumb($breadCrumb, $breadCrumbTitle);

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addLeft(
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Api_Editroles')
        );
        $resources = Mage::getModel('Magento_Api_Model_Roles')->getResourcesList();
        $this->_addContent(
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Api_Buttons')
                ->setRoleId($roleId)
                ->setRoleInfo(Mage::getModel('Magento_Api_Model_Roles')->load($roleId))
                ->setTemplate('api/roleinfo.phtml')
        );
        $this->_addJs(
            $this->getLayout()
                ->createBlock('Magento_Adminhtml_Block_Template')
                ->setTemplate('api/role_users_grid_js.phtml')
        );
        $this->renderLayout();
    }

    public function deleteAction()
    {
        $rid = $this->getRequest()->getParam('rid', false);

        try {
            Mage::getModel('Magento_Api_Model_Roles')->load($rid)->delete();
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(__('The role has been deleted.'));
        } catch (Exception $e) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(__('An error occurred while deleting this role.'));
        }

        $this->_redirect("*/*/");
    }

    public function saveRoleAction()
    {

        $rid        = $this->getRequest()->getParam('role_id', false);
        $role = Mage::getModel('Magento_Api_Model_Roles')->load($rid);
        if (!$role->getId() && $rid) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(__('This role no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        $resource   = $this->getRequest()->getParam('resource', false);
        $roleUsers  = $this->getRequest()->getParam('in_role_user', null);
        parse_str($roleUsers, $roleUsers);
        $roleUsers = array_keys($roleUsers);

        $oldRoleUsers = $this->getRequest()->getParam('in_role_user_old');
        parse_str($oldRoleUsers, $oldRoleUsers);
        $oldRoleUsers = array_keys($oldRoleUsers);

        $isAll = $this->getRequest()->getParam('all');
        if ($isAll) {
            $resource = array("all");
        }

        try {
            $role = $role
                    ->setName($this->getRequest()->getParam('rolename', false))
                    ->setPid($this->getRequest()->getParam('parent_id', false))
                    ->setRoleType('G')
                    ->save();

            Mage::getModel('Magento_Api_Model_Rules')
                ->setRoleId($role->getId())
                ->setResources($resource)
                ->saveRel();

            foreach($oldRoleUsers as $oUid) {
                $this->_deleteUserFromRole($oUid, $role->getId());
            }

            foreach ($roleUsers as $nRuid) {
                $this->_addUserToRole($nRuid, $role->getId());
            }

            $rid = $role->getId();
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(__('You saved the role.'));
        } catch (Exception $e) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(__('An error occurred while saving this role.'));
        }

        //$this->getResponse()->setRedirect($this->getUrl("*/*/editrole/rid/$rid"));
        $this->_redirect('*/*/editrole', array('rid' => $rid));
        return;
    }

    public function editrolegridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Api_Role_Grid_User')->toHtml()
        );
    }

    protected function _deleteUserFromRole($userId, $roleId)
    {
        try {
            Mage::getModel('Magento_Api_Model_User')
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
        $user = Mage::getModel('Magento_Api_Model_User')->load($userId);
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
        return $this->_authorization->isAllowed('Magento_Api::roles');
    }
}
