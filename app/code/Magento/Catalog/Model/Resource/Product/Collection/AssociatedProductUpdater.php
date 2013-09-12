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
class Magento_Catalog_Model_Resource_Product_Collection_AssociatedProductUpdater
    implements Magento_Core_Model_Layout_Argument_UpdaterInterface
{
    /**
     * Stock Item instance
     *
     * @var Magento_CatalogInventory_Model_Resource_Stock_Item
     */
    protected $_stockItem;

    /**
     * Updater constructor
     *
     * @param Magento_CatalogInventory_Model_Resource_Stock_Item $stockItem
     */
    public function __construct(Magento_CatalogInventory_Model_Resource_Stock_Item $stockItem)
    {
        $this->_stockItem = $stockItem;
    }

    /**
     * Add filtration by qty and stock availability
     *
     * @param Magento_Catalog_Model_Resource_Product_Collection_AssociatedProduct $collection
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
