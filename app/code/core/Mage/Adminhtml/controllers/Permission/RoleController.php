<?php
/**
 * Adminhtml roles controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Permission_RoleController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('system/acl');
        $this->_addBreadcrumb(__('System'), __('System Title'));
        $this->_addBreadcrumb(__('Permissions'), __('Permissions Title'));
        $this->_addBreadcrumb(__('Roles'), __('Roles Title'));

        $this->_addContent($this->getLayout()->createBlock('adminhtml/permissions_roles'));

        $this->renderLayout();
    }

    public function roleGridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/permissions_grid_role')->toHtml());
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

        $this->loadLayout('baseframe');
        $this->_addBreadcrumb(__('System'), __('System Title'));
        $this->_addBreadcrumb(__('Permission'), __('Permission Title'));
        $this->_addBreadcrumb(__('Roles'), __('Roles Title'), Mage::getUrl('*/*/'));
        $this->_addBreadcrumb($breadCrumb, $breadCrumbTitle);

        $this->_addLeft(
            $this->getLayout()->createBlock('adminhtml/permissions_editroles')
        );

        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/permissions_buttons')
                ->setRoleId($roleId)
                ->setTemplate('permissions/roleinfo.phtml')
        );

        $this->renderLayout();
    }

    public function deleteRoleAction()
    {
        $rid = $this->getRequest()->getParam('id', false);
        Mage::getModel("permissions/roles")->setId($rid)->delete();

        $this->_redirect("adminhtml/permission_role");
    }

    public function saveRoleAction()
    {
        $rid = $this->getRequest()->getParam('role_id', false);

        $role = Mage::getModel("permissions/roles")
                ->setId($rid)
                ->setName($this->getRequest()->getParam('role_name', false))
                ->setPid($this->getRequest()->getParam('parent_id', false))
                ->setRoleType('G')
                ->save();

        Mage::getModel("permissions/rules")
            ->setRoleId($role->getId())
            ->setResources($this->getRequest()->getParam('resource', false))
            ->saveRel();


        $rid = $role->getId();
        $this->getResponse()->setRedirect(Mage::getUrl("*/*/editrole/rid/$rid"));
    }

    public function editrolegridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/permissions_role_grid_user')->toHtml());
    }
}