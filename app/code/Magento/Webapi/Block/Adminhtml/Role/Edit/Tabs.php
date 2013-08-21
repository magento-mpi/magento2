<?php
/**
 * Web API Role edit page tabs.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @method Magento_Webapi_Block_Adminhtml_Role_Edit_Tabs setApiRole() setApiRole(Magento_Webapi_Model_Acl_Role $role)
 * @method Magento_Webapi_Model_Acl_Role getApiRole() getApiRole()
 */
class Magento_Webapi_Block_Adminhtml_Role_Edit_Tabs extends Magento_Backend_Block_Widget_Tabs
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
     * @return Magento_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        /** @var Magento_Webapi_Block_Adminhtml_Role_Edit_Tab_Main $mainBlock */
        $mainBlock = $this->getLayout()->getBlock('webapi.role.edit.tab.main');
        $mainBlock->setApiRole($this->getApiRole());
        $this->addTab('main_section', array(
            'label' => __('Role Info'),
            'title' => __('Role Info'),
            'content' => $mainBlock->toHtml(),
            'active' => true
        ));

        /** @var Magento_Webapi_Block_Adminhtml_Role_Edit_Tab_Resource $resourceBlock */
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
