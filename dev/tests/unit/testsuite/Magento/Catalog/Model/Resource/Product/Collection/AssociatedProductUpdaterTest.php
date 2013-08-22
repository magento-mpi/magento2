<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Catalog_Model_Resource_Product_Collection_AssociatedProductUpdaterTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test adding filtration by qty and stock availability to collection
     */
    public function testUpdate()
    {
        $inventory = array(
            'qty' => 'qty',
            'inventory_in_stock' => 'is_in_stock'
        );
        $collection = $this->getMockBuilder('Magento_Data_Collection_Db')
            ->disableOriginalConstructor()
            ->getMock();
        $stockItem = $this->getMockBuilder('Magento_CatalogInventory_Model_Resource_Stock_Item')
            ->disableOriginalConstructor()
            ->setMethods(array('addCatalogInventoryToProductCollection'))
            ->getMock();
        $stockItem->expects($this->any())
            ->method('addCatalogInventoryToProductCollection')
            ->with($collection, $inventory);

        $model = new Magento_Catalog_Model_Resource_Product_Collection_AssociatedProductUpdater($stockItem);
        $model->update($collection);
    }
}
