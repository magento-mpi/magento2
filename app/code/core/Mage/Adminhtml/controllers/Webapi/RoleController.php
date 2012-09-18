<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml roles controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Webapi_RoleController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('Mage_Api::system_webapi_roles');
        $this->_addBreadcrumb($this->__('Web Api'), $this->__('Web Api'));
        $this->_addBreadcrumb($this->__('Permissions'), $this->__('Permissions'));
        $this->_addBreadcrumb($this->__('Roles'), $this->__('Roles'));
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Web Api'))
             ->_title($this->__('Roles'));

        $this->_initAction();

        $this->_addContent($this->getLayout()->createBlock('Mage_Adminhtml_Block_Webapi_Roles'));

        $this->renderLayout();
    }

    public function roleGridAction()
    {
        $this->getResponse()
            ->setBody($this->getLayout()
            ->createBlock('Mage_Adminhtml_Block_Webapi_Grid_Role')
            ->toHtml()
        );
    }

    public function editRoleAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Web Services'))
             ->_title($this->__('Roles'));

        $this->_initAction();

        $roleId = $this->getRequest()->getParam('rid');
        if( intval($roleId) > 0 ) {
            $breadCrumb = $this->__('Edit Role');
            $breadCrumbTitle = $this->__('Edit Role');
            $this->_title($this->__('Edit Role'));
        } else {
            $breadCrumb = $this->__('Add New Role');
            $breadCrumbTitle = $this->__('Add New Role');
            $this->_title($this->__('New Role'));
        }
        $this->_addBreadcrumb($breadCrumb, $breadCrumbTitle);

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addLeft(
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Webapi_Editrole')
        );

        $this->_addContent(
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Webapi_Buttons')
                ->setRoleId($roleId)
                ->setRoleInfo(Mage::getModel('Mage_Webapi_Model_Acl_Role')->load($roleId))
                ->setTemplate('api/roleinfo.phtml')
        );
        $this->_addJs(
            $this->getLayout()
                ->createBlock('Mage_Adminhtml_Block_Template')
                ->setTemplate('api/role_users_grid_js.phtml')
        );
        $this->renderLayout();
    }

    public function deleteAction()
    {
        $rid = $this->getRequest()->getParam('rid', false);

        try {
            Mage::getModel('Mage_Webapi_Model_Role')->load($rid)->delete();
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess($this->__('The role has been deleted.'));
        } catch (Exception $e) {
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($this->__('An error occurred while deleting this role.'));
        }

        $this->_redirect("*/*/");
    }

    public function saveRoleAction()
    {
        $rid = $this->getRequest()->getParam('role_id', false);
        $role = Mage::getModel('Mage_Webapi_Model_Role')->load($rid);
        if (!$role->getId() && $rid) {
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($this->__('This Role no longer exists'));
            $this->_redirect('*/*/');
            return;
        }



        $this->_redirect('*/*/editrole', array('rid' => $rid));
        return;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Webapi::roles');
    }
}
