<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Controller\Adminhtml\Group;

use Magento\Customer\Controller\RegistryConstants;

class NewAction extends \Magento\Customer\Controller\Adminhtml\Group
{
    /**
     * Initialize current group and set it in the registry.
     *
     * @return int
     */
    protected function _initGroup()
    {
        $this->_title->add(__('Customer Groups'));

        $groupId = $this->getRequest()->getParam('id');
        $this->_coreRegistry->register(RegistryConstants::CURRENT_GROUP_ID, $groupId);

        return $groupId;
    }

    /**
     * Edit or create customer group.
     *
     * @return void
     */
    public function execute()
    {
        $groupId = $this->_initGroup();

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Customer::customer_group');
        $this->_addBreadcrumb(__('Customers'), __('Customers'));
        $this->_addBreadcrumb(__('Customer Groups'), __('Customer Groups'), $this->getUrl('customer/group'));

        if (is_null($groupId)) {
            $this->_addBreadcrumb(__('New Group'), __('New Customer Groups'));
            $this->_title->add(__('New Customer Group'));
        } else {
            $this->_addBreadcrumb(__('Edit Group'), __('Edit Customer Groups'));
            $this->_title->add($this->_groupService->getGroup($groupId)->getCode());
        }

        $this->_view->getLayout()->addBlock(
            'Magento\Customer\Block\Adminhtml\Group\Edit',
            'group',
            'content'
        )->setEditMode(
            (bool)$groupId
        );

        $this->_view->renderLayout();
    }
}
