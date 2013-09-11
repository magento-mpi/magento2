<?php
/**
 * Web API user edit page tabs.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @method \Magento\Object getApiUser() getApiUser()
 * @method \Magento\Webapi\Block\Adminhtml\User\Edit\Tabs setApiUser() setApiUser(\Magento\Object $apiUser)
 */
namespace Magento\Webapi\Block\Adminhtml\User\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Internal constructor.
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('User Information'));
    }

    /**
     * Before to HTML.
     *
     * @return \Magento\Core\Block\AbstractBlock
     */
    protected function _beforeToHtml()
    {
        /** @var \Magento\Webapi\Block\Adminhtml\User\Edit\Tab\Main $mainTab */
        $mainTab = $this->getLayout()->getBlock('webapi.user.edit.tab.main');
        $mainTab->setApiUser($this->getApiUser());
        $this->addTab('main_section', array(
            'label' => __('User Info'),
            'title' => __('User Info'),
            'content' => $mainTab->toHtml(),
            'active' => true
        ));

        $rolesGrid = $this->getLayout()->getBlock('webapi.user.edit.tab.roles.grid');
        $this->addTab('roles_section', array(
            'label' => __('User Role'),
            'title' => __('User Role'),
            'content' => $rolesGrid->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}
