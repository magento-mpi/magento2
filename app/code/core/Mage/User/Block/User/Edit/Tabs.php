<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin page left menu
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Permissions_User_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('Mage_Adminhtml_Helper_Data')->__('User Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('main_section', array(
            'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('User Info'),
            'title'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('User Info'),
            'content'   => $this->getLayout()
                ->createBlock('Mage_Adminhtml_Block_Permissions_User_Edit_Tab_Main')->toHtml(),
            'active'    => true
        ));

        $this->addTab('roles_section', array(
            'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('User Role'),
            'title'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('User Role'),
            'content'   => $this->getLayout()
                ->createBlock('Mage_Adminhtml_Block_Permissions_User_Edit_Tab_Roles', 'user.roles.grid')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }

}
