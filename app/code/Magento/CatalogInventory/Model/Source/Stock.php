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
namespace Magento\CatalogInventory\Model\Source;

class Stock
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
                'value' => \Magento\CatalogInventory\Model\Stock::STOCK_IN_STOCK,
                'label' => __('In Stock')
            ),
            array(
                'value' => \Magento\CatalogInventory\Model\Stock::STOCK_OUT_OF_STOCK,
                'label' => __('Out of Stock')
            ),
        );
    }
}
