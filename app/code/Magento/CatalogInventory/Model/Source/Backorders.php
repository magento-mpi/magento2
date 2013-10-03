<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogInventory\Model\Source;

class Backorders implements \Magento\Core\Model\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value' => \Magento\CatalogInventory\Model\Stock::BACKORDERS_NO, 'label'=>__('No Backorders')),
            array('value' => \Magento\CatalogInventory\Model\Stock::BACKORDERS_YES_NONOTIFY, 'label'=>__('Allow Qty Below 0')),
            array('value' => \Magento\CatalogInventory\Model\Stock::BACKORDERS_YES_NOTIFY , 'label'=>__('Allow Qty Below 0 and Notify Customer')),
        );
    }
}
