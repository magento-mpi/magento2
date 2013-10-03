<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Associated product resource collection
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Resource\Product\Collection;

class AssociatedProductUpdater
    implements \Magento\Core\Model\Layout\Argument\UpdaterInterface
{
    /**
     * Stock Item instance
     *
     * @var \Magento\CatalogInventory\Model\Resource\Stock\Item
     */
    protected $_stockItem;

    /**
     * Updater constructor
     *
     * @param \Magento\CatalogInventory\Model\Resource\Stock\Item $stockItem
     */
    public function __construct(\Magento\CatalogInventory\Model\Resource\Stock\Item $stockItem)
    {
        $this->_stockItem = $stockItem;
    }

    /**
     * Add filtration by qty and stock availability
     *
     * @param \Magento\Catalog\Model\Resource\Product\Collection\AssociatedProduct $collection
     * @return mixed
     */
    public function update($collection)
    {
        $this->_stockItem->addCatalogInventoryToProductCollection(
            $collection,
            array(
                'qty' => 'qty',
                'inventory_in_stock' => 'is_in_stock'
            )
        );
        return $collection;
    }
}
