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
 * @method Magento_Webapi_Block_Adminhtml_User_Edit_Tabs setApiUser() setApiUser(\Magento\Object $apiUser)
 */
class Magento_Webapi_Block_Adminhtml_User_Edit_Tabs extends Magento_Backend_Block_Widget_Tabs
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
     * @return Magento_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        /** @var Magento_Webapi_Block_Adminhtml_User_Edit_Tab_Main $mainTab */
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
