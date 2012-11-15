<?php
/**
 * Web API Role tabs
 *
 * @copyright {}
 *
 * @method Mage_Webapi_Block_Adminhtml_Role_Edit_Tabs setApiRole() setApiRole(Mage_Webapi_Model_Acl_Role $role)
 * @method Mage_Webapi_Model_Acl_Role getApiRole() getApiRole()
 */
class Mage_Webapi_Block_Adminhtml_Role_Edit_Tabs extends Mage_Backend_Block_Widget_Tabs
{
    /**
     * Internal Constructor
     */
    protected function _construct()
    {
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle($this->__('Role Information'));
        parent::_construct();
    }

    /**
     * Prepare child blocks
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        /** @var Mage_Webapi_Block_Adminhtml_Role_Edit_Tab_Main $mainBlock */
        $mainBlock = $this->getLayout()->getBlock('webapi.role.edit.tab.main');
        $mainBlock->setApiRole($this->getApiRole());
        $this->addTab('main_section', array(
            'label' => $this->__('Role Info'),
            'title' => $this->__('Role Info'),
            'content' => $mainBlock->toHtml(),
            'active' => true
        ));

        /** @var Mage_Webapi_Block_Adminhtml_Role_Edit_Tab_Resource $resourceBlock */
        $resourceBlock = $this->getLayout()->getBlock('webapi.role.edit.tab.resource');
        $resourceBlock->setApiRole($this->getApiRole());
        $this->addTab('resource_section', array(
            'label' => $this->__('Resources'),
            'title' => $this->__('Resources'),
            'content' => $resourceBlock->toHtml()
        ));

        if ($this->getApiRole() && $this->getApiRole()->getRoleId() > 0) {
            /** @var Mage_Webapi_Block_Adminhtml_Role_Edit_Tab_User $userBlock */
            $userBlock = $this->getLayout()->getBlock('webapi.role.edit.tab.user');
            $userBlock->setApiRole($this->getApiRole());
            $this->addTab('user_section', array(
                'label' => $this->__('Users'),
                'title' => $this->__('Users'),
                'content' => $userBlock->toHtml()
            ));
        } else {
            /** @var Mage_Core_Block_Template $usersJsBlock */
            $usersJsBlock = $this->getLayout()->getBlock('roles-users-grid-js');
            $usersJsBlock->setTemplate('');
        }

        return parent::_beforeToHtml();
    }

}
