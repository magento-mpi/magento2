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
 * Web API User page left menu
 *
 * @method Mage_Webapi_Block_Adminhtml_User_Edit setApiUser(Mage_Webapi_Model_Acl_User $user)
 * @method Mage_Webapi_Model_Acl_User getApiUser()
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Block_Adminhtml_User_Edit_Tabs extends Mage_Backend_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('Mage_Webapi_Helper_Data')->__('User Information'));
    }

    protected function _beforeToHtml()
    {
        /** @var $mainBlock Mage_Webapi_Block_Adminhtml_User_Edit_Tab_Main */
        $mainBlock = $this->getLayout()->getBlock('webapi.user.edit.tab.main');
        $mainBlock->setApiUser($this->getApiUser());
        $this->addTab('main_section', array(
            'label' => Mage::helper('Mage_Webapi_Helper_Data')->__('User Info'),
            'title' => Mage::helper('Mage_Webapi_Helper_Data')->__('User Info'),
            'content' => $mainBlock->toHtml(),
            'active' => true
        ));

        /** @var $roleBlock Mage_Webapi_Block_Adminhtml_User_Edit_Tab_Roles */
        $roleBlock = $this->getLayout()->getBlock('webapi.user.edit.tab.roles');
        $roleBlock->setApiUser($this->getApiUser());
        $this->addTab('roles_section', array(
            'label' => Mage::helper('Mage_Webapi_Helper_Data')->__('User Role'),
            'title' => Mage::helper('Mage_Webapi_Helper_Data')->__('User Role'),
            'content' => $roleBlock->toHtml(),
        ));
        return parent::_beforeToHtml();
    }

}
