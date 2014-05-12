<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CatalogInventory Stock source model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogInventory\Model\Source;

class Stock implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Retrieve option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => \Magento\CatalogInventory\Model\Stock::STOCK_IN_STOCK, 'label' => __('In Stock')),
            array('value' => \Magento\CatalogInventory\Model\Stock::STOCK_OUT_OF_STOCK, 'label' => __('Out of Stock'))
        );
    }
}
