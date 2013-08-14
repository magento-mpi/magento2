<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * User page left menu
 *
 * @category   Mage
 * @package    Magento_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_User_Block_User_Edit_Tabs extends Magento_Backend_Block_Widget_Tabs
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('Magento_User_Helper_Data')->__('User Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('main_section', array(
            'label'     => Mage::helper('Magento_User_Helper_Data')->__('User Info'),
            'title'     => Mage::helper('Magento_User_Helper_Data')->__('User Info'),
            'content'   => $this->getLayout()
                ->createBlock('Magento_User_Block_User_Edit_Tab_Main')->toHtml(),
            'active'    => true
        ));

        $this->addTab('roles_section', array(
            'label'     => Mage::helper('Magento_User_Helper_Data')->__('User Role'),
            'title'     => Mage::helper('Magento_User_Helper_Data')->__('User Role'),
            'content'   => $this->getLayout()
                ->createBlock('Magento_User_Block_User_Edit_Tab_Roles', 'user.roles.grid')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }

}
