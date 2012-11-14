<?php
/**
 * Web API User page left menu
 *
 * @copyright {}
 *
 * @method Varien_Object getApiUser() getApiUser()
 * @method Mage_Webapi_Block_Adminhtml_User_Edit_Tabs setApiUser() setApiUser(Varien_Object $apiUser)
 */
class Mage_Webapi_Block_Adminhtml_User_Edit_Tabs extends Mage_Backend_Block_Widget_Tabs
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle($this->__('User Information'));
        parent::_construct();
    }

    /**
     * Before to HTML
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        /** @var Mage_Webapi_Block_Adminhtml_User_Edit_Tab_Main $mainTab */
        $mainTab = $this->getLayout()->getBlock('webapi.user.edit.tab.main');
        $mainTab->setApiUser($this->getApiUser());
        $this->addTab('main_section', array(
            'label' => $this->__('User Info'),
            'title' => $this->__('User Info'),
            'content' => $mainTab->toHtml(),
            'active' => true
        ));

        /** @var Mage_Backend_Block_Widget_Grid $rolesGrid */
        $rolesGridContainer = $this->getLayout()->getBlock('webapi.user.edit.tab.roles.grid.container');
        /** @var Mage_Backend_Block_Widget_Grid_Column $roleIdColumn */
        $roleIdColumn = $this->getLayout()->getBlock('webapi.user.edit.tab.roles.grid.columnSet.role_id');
        $roleIdColumn->setValue($this->getApiUser()->getRoleId());
        $this->addTab('roles_section', array(
            'label' => $this->__('User Role'),
            'title' => $this->__('User Role'),
            'content' => $rolesGridContainer->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}
