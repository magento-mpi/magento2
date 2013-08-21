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
 * Products mass update inventory tab
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Edit_Action_Attribute_Tab_Inventory
    extends Magento_Adminhtml_Block_Widget
    implements Magento_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Retrieve Backorders Options
     *
     * @return array
     */
    public function getBackordersOption()
    {
        return Mage::getSingleton('Magento_CatalogInventory_Model_Source_Backorders')->toOptionArray();
    }

    /**
     * Retrieve field suffix
     *
     * @return string
     */
    public function getFieldSuffix()
    {
        return 'inventory';
    }

    /**
     * Retrieve current store id
     *
     * @return int
     */
    public function getStoreId()
    {
        $storeId = $this->getRequest()->getParam('store');
        return intval($storeId);
    }

    /**
     * Get default config value
     *
     * @param string $field
     * @return mixed
     */
    public function getDefaultConfigValue($field)
    {
        return Mage::getStoreConfig(Magento_CatalogInventory_Model_Stock_Item::XML_PATH_ITEM . $field, $this->getStoreId());
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return __('Advanced Inventory');
    }

    public function getTabTitle()
    {
        return __('Advanced Inventory');
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
