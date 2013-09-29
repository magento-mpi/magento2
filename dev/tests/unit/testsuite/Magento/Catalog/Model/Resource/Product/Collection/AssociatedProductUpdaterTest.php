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

namespace Magento\Catalog\Model\Resource\Product\Collection;

class AssociatedProductUpdaterTest extends \PHPUnit_Framework_TestCase
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
        $collection = $this->getMockBuilder('Magento\Data\Collection\Db')
            ->disableOriginalConstructor()
            ->getMock();
        $stockItem = $this->getMockBuilder('Magento\CatalogInventory\Model\Resource\Stock\Item')
            ->disableOriginalConstructor()
            ->setMethods(array('addCatalogInventoryToProductCollection'))
            ->getMock();
        $stockItem->expects($this->any())
            ->method('addCatalogInventoryToProductCollection')
            ->with($collection, $inventory);

        $model = new \Magento\Catalog\Model\Resource\Product\Collection\AssociatedProductUpdater($stockItem);
        $model->update($collection);
    }
}
