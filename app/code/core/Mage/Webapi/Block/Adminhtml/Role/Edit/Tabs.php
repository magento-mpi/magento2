<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Web API Role tabs
 *
 * @method Mage_Webapi_Block_Adminhtml_Role_Edit_Tabs setApiRole(Mage_Webapi_Model_Acl_Role $role)
 * @method Mage_Webapi_Model_Acl_Role getApiRole()
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Block_Adminhtml_Role_Edit_Tabs extends Mage_Backend_Block_Widget_Tabs
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('Mage_Webapi_Helper_Data')->__('Role Information'));
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
            'label' => Mage::helper('Mage_Webapi_Helper_Data')->__('Role Info'),
            'title' => Mage::helper('Mage_Webapi_Helper_Data')->__('Role Info'),
            'content' => $mainBlock->toHtml(),
            'active' => true
        ));

        /** @var Mage_Webapi_Block_Adminhtml_Role_Edit_Tab_Resource $resourceBlock */
        $resourceBlock = $this->getLayout()->getBlock('webapi.role.edit.tab.resource');
        $resourceBlock->setApiRole($this->getApiRole());
        $this->addTab('resource_section', array(
            'label' => Mage::helper('Mage_Webapi_Helper_Data')->__('Resources'),
            'title' => Mage::helper('Mage_Webapi_Helper_Data')->__('Resources'),
            'content' => $resourceBlock->toHtml()
        ));

        if ($this->getApiRole() && $this->getApiRole()->getRoleId() > 0) {
            /** @var Mage_Webapi_Block_Adminhtml_Role_Edit_Tab_User $userBlock */
            $userBlock = $this->getLayout()->getBlock('webapi.role.edit.tab.user');
            $userBlock->setApiRole($this->getApiRole());
            $this->addTab('user_section', array(
                'label' => Mage::helper('Mage_Webapi_Helper_Data')->__('Users'),
                'title' => Mage::helper('Mage_Webapi_Helper_Data')->__('Users'),
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
