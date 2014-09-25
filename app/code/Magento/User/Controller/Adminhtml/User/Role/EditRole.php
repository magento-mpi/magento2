<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\User\Controller\Adminhtml\User\Role;

class EditRole extends \Magento\User\Controller\Adminhtml\User\Role
{
    /**
     * Edit role action
     *
     * @return void
     */
    public function execute()
    {
        $role = $this->_initRole();
        $this->_initAction();

        if ($role->getId()) {
            $breadCrumb = __('Edit Role');
            $breadCrumbTitle = __('Edit Role');
        } else {
            $breadCrumb = __('Add New Role');
            $breadCrumbTitle = __('Add New Role');
        }

        $this->_title->add($role->getId() ? $role->getRoleName() : __('New Role'));

        $this->_addBreadcrumb($breadCrumb, $breadCrumbTitle);

        $this->_view->getLayout()->getBlock(
            'adminhtml.user.role.buttons'
        )->setRoleId(
            $role->getId()
        )->setRoleInfo(
            $role
        );

        $this->_view->renderLayout();
    }
}
