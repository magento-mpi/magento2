<?php
/**
 * Web API Role edit page tabs.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @method \Magento\Webapi\Block\Adminhtml\Role\Edit\Tabs setApiRole() setApiRole(\Magento\Webapi\Model\Acl\Role $role)
 * @method \Magento\Webapi\Model\Acl\Role getApiRole() getApiRole()
 */
namespace Magento\Webapi\Block\Adminhtml\Role\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Internal Constructor.
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Role Information'));
    }

    /**
     * Prepare child blocks.
     *
     * @return \Magento\Core\Block\AbstractBlock
     */
    protected function _beforeToHtml()
    {
        /** @var \Magento\Webapi\Block\Adminhtml\Role\Edit\Tab\Main $mainBlock */
        $mainBlock = $this->getLayout()->getBlock('webapi.role.edit.tab.main');
        $mainBlock->setApiRole($this->getApiRole());
        $this->addTab('main_section', array(
            'label' => __('Role Info'),
            'title' => __('Role Info'),
            'content' => $mainBlock->toHtml(),
            'active' => true
        ));

        /** @var \Magento\Webapi\Block\Adminhtml\Role\Edit\Tab\Resource $resourceBlock */
        $resourceBlock = $this->getLayout()->getBlock('webapi.role.edit.tab.resource');
        $resourceBlock->setApiRole($this->getApiRole());
        $this->addTab('resource_section', array(
            'label' => __('Resources'),
            'title' => __('Resources'),
            'content' => $resourceBlock->toHtml()
        ));

        if ($this->getApiRole() && $this->getApiRole()->getRoleId() > 0) {
            $usersGrid = $this->getLayout()->getBlock('webapi.role.edit.tab.users.grid');
            $this->addTab('user_section', array(
                'label' => __('Users'),
                'title' => __('Users'),
                'content' => $usersGrid->toHtml()
            ));
        }

        return parent::_beforeToHtml();
    }

}
