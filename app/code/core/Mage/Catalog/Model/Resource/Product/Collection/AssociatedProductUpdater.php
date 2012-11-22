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
    implements Mage_Core_Model_Layout_Argument_UpdaterInterface
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
        $this->_stockItem = isset($stockItem) ? $stockItem
            : Mage::getModel('Mage_CatalogInventory_Model_Resource_Stock_Item');
    }

    /**
     * Add filtration by qty and stock availability
     *
     * @param Varien_Data_Collection_Db $argument
     * @return mixed
     */
    public function update($argument)
    {
        $this->_stockItem->addCatalogInventoryToProductCollection(
            $argument,
            array(
                'qty' => 'qty',
                'inventory_in_stock' => 'is_in_stock'
            )
        );
        return $argument;
    }
}
