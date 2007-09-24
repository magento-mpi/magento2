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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml roles controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Adminhtml_Permissions_RoleController extends Mage_Adminhtml_Controller_Action
{

	public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system/acl');
        $this->_addBreadcrumb(__('System'), __('System'));
        $this->_addBreadcrumb(__('Permissions'), __('Permissions'));
        $this->_addBreadcrumb(__('Roles'), __('Roles'));

        $this->_addContent($this->getLayout()->createBlock('adminhtml/permissions_roles'));

        $this->renderLayout();
    }

    public function roleGridAction()
    {
        $this->getResponse()
        	->setBody($this->getLayout()
        	->createBlock('adminhtml/permissions_grid_role')
        	->toHtml()
        );
    }

    public function editRoleAction()
    {
        $roleId = $this->getRequest()->getParam('rid');
        if( intval($roleId) > 0 ) {
            $breadCrumb = __('Edit Role');
            $breadCrumbTitle = __('Edit Role');
        } else {
            $breadCrumb = __('Add new Role');
            $breadCrumbTitle = __('Add new Role');
        }
        $this->loadLayout();
        $this->_addBreadcrumb(__('System'), __('System'));
        $this->_addBreadcrumb(__('Permission'), __('Permission'));
        $this->_addBreadcrumb(__('Roles'), __('Roles'), Mage::getUrl('*/*/'));
        $this->_addBreadcrumb($breadCrumb, $breadCrumbTitle);
        $this->_setActiveMenu('system/acl');
        $this->getLayout()->getBlock('root')->setCanLoadExtJs(true);

        $this->_addLeft(
            $this->getLayout()->createBlock('adminhtml/permissions_editroles')
        );

        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/permissions_buttons')
                ->setRoleId($roleId)
                ->setRoleInfo(Mage::getModel('admin/permissions_roles')->load($roleId))
                ->setTemplate('permissions/roleinfo.phtml')
        );

        $this->renderLayout();
    }

    public function deleteAction()
    {
        $rid = $this->getRequest()->getParam('rid', false);
        $currentUser = Mage::getModel('admin/permissions_user')->setId(Mage::getSingleton('admin/session')->getUser()->getId());
        if ( in_array($rid, $currentUser->getRoles()) ) {
            Mage::getSingleton('adminhtml/session')->addError(__('You can not delete self assigned roles.'));
            $this->_redirect('*/*/editrole', array('rid' => $rid));
            return;
        }
       
        try {
            Mage::getModel("admin/permissions_roles")->setId($rid)->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess(__('Role successfully deleted.'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(__('Error while deleting this role. Please try again later.'));
        }

        $this->_redirect("*/*/");
    }

    public function saveRoleAction()
    {
        $rid = $this->getRequest()->getParam('role_id', false);
        $resource = explode(',', $this->getRequest()->getParam('resource', false));
        try {
            $role = Mage::getModel("admin/permissions_roles")
                    ->setId($rid)
                    ->setName($this->getRequest()->getParam('rolename', false))
                    ->setPid($this->getRequest()->getParam('parent_id', false))
                    ->setRoleType('G')
                    ->save();

            Mage::getModel("admin/permissions_rules")
                ->setRoleId($role->getId())
                ->setResources($resource)
                ->saveRel();
            Mage::getSingleton('adminhtml/session')->addSuccess(__('Role successfully saved.'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(__('Error while saving this role. Please try again later.'));
        }

        $rid = $role->getId();
        $this->getResponse()->setRedirect(Mage::getUrl("*/*/editrole/rid/$rid"));
    }

    public function editrolegridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/permissions_role_grid_user')->toHtml());
    }

    protected function _isAllowed()
    {
	    return Mage::getSingleton('admin/session')->isAllowed('system/acl/roles');
    }
}