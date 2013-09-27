<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product mass attribute update websites tab
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Edit_Action_Attribute_Tab_Websites
    extends Magento_Adminhtml_Block_Widget
    implements Magento_Adminhtml_Block_Widget_Tab_Interface
{
    public function getWebsiteCollection()
    {
        return $this->_storeManager->getWebsites();
    }

    public function getGroupCollection(Magento_Core_Model_Website $website)
    {
        return $website->getGroups();
    }

    public function getStoreCollection(Magento_Core_Model_Store_Group $group)
    {
        return $group->getStores();
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return __('Websites');
    }

    public function getTabTitle()
    {
        return __('Websites');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
