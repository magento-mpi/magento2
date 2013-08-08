<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Associated product resource collection
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Product_Collection_AssociatedProductUpdater
    implements Magento_Core_Model_Layout_Argument_UpdaterInterface
{
    /**
     * Stock Item instance
     *
     * @var Mage_CatalogInventory_Model_Resource_Stock_Item
     */
    protected $_stockItem;

    /**
     * Updater constructor
     *
     * @param Mage_CatalogInventory_Model_Resource_Stock_Item $stockItem
     */
    public function __construct(Mage_CatalogInventory_Model_Resource_Stock_Item $stockItem)
    {
        $this->_stockItem = $stockItem;
    }

    /**
     * Add filtration by qty and stock availability
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection_AssociatedProduct $collection
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
