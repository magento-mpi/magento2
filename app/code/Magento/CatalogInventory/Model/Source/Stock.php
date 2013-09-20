<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CatalogInventory Stock source model
 *
 * @category   Magento
 * @package    Magento_CatalogInventory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogInventory_Model_Source_Stock implements Magento_Core_Model_Option_ArrayInterface
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
                'value' => Magento_CatalogInventory_Model_Stock::STOCK_IN_STOCK,
                'label' => __('In Stock')
            ),
            array(
                'value' => Magento_CatalogInventory_Model_Stock::STOCK_OUT_OF_STOCK,
                'label' => __('Out of Stock')
            ),
        );
    }
}
