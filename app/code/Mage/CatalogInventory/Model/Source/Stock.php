<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CatalogInventory Stock source model
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogInventory_Model_Source_Stock
{
    /**
     * Retrieve option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage_CatalogInventory_Model_Stock::STOCK_IN_STOCK,
                'label' => Mage::helper('Mage_CatalogInventory_Helper_Data')->__('In Stock')
            ),
            array(
                'value' => Mage_CatalogInventory_Model_Stock::STOCK_OUT_OF_STOCK,
                'label' => Mage::helper('Mage_CatalogInventory_Helper_Data')->__('Out of Stock')
            ),
        );
    }
}
