<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Model\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * CatalogInventory Stock source model
 */
class Stock extends AbstractSource
{
    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getAllOptions()
    {
        return array(
            array('value' => \Magento\CatalogInventory\Model\Stock::STOCK_IN_STOCK, 'label' => __('In Stock')),
            array('value' => \Magento\CatalogInventory\Model\Stock::STOCK_OUT_OF_STOCK, 'label' => __('Out of Stock'))
        );
    }
}
