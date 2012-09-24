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
 * @method Mage_Webapi_Block_Adminhtml_Role_Edit setApiRole(Mage_Webapi_Model_Acl_Role $role)
 * @method Mage_Webapi_Model_Acl_Role getApiRole()
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Block_Adminhtml_Role_Edit_Tabs extends Mage_Backend_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('Mage_Webapi_Helper_Data')->__('Role Information'));
    }

    protected function _beforeToHtml()
    {
        /** @var $mainBlock Mage_Webapi_Block_Adminhtml_Role_Edit_Tab_Main */
        $mainBlock = $this->getLayout()->getBlock('webapi.role.edit.tab.main');
        $mainBlock->setApiRole($this->getApiRole());
        $this->addTab('main_section', array(
            'label' => Mage::helper('Mage_Webapi_Helper_Data')->__('Role Info'),
            'title' => Mage::helper('Mage_Webapi_Helper_Data')->__('Role Info'),
            'content' => $mainBlock->toHtml(),
            'active' => true
        ));

        /** @var $resourceBlock Mage_Webapi_Block_Adminhtml_Role_Edit_Tab_Resource */
        $resourceBlock = $this->getLayout()->getBlock('webapi.role.edit.tab.resource');
        $resourceBlock->setApiRole($this->getApiRole());
        $this->addTab('resource_section', array(
            'label' => Mage::helper('Mage_Webapi_Helper_Data')->__('Resources'),
            'title' => Mage::helper('Mage_Webapi_Helper_Data')->__('Resources'),
            'content' => $resourceBlock->toHtml()
        ));

        /** @var $userBlock Mage_Webapi_Block_Adminhtml_Role_Edit_Tab_User */
        if ($this->getApiRole() && $this->getApiRole()->getRoleId() > 0) {
            $userBlock = $this->getLayout()->getBlock('webapi.role.edit.tab.user');
            $userBlock->setApiRole($this->getApiRole());
            $this->addTab('user_section', array(
                'label' => Mage::helper('Mage_Webapi_Helper_Data')->__('Users'),
                'title' => Mage::helper('Mage_Webapi_Helper_Data')->__('Users'),
                'content' => $userBlock->toHtml()
            ));
        }

        return parent::_beforeToHtml();
    }

}
