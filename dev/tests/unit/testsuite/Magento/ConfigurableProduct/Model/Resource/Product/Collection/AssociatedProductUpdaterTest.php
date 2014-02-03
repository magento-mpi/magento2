<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Model\Resource\Product\Collection;

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
            ->setMethods(array('addCatalogInventoryToProductCollection', '__wakeup'))
            ->getMock();
        $stockItem->expects($this->any())
            ->method('addCatalogInventoryToProductCollection')
            ->with($collection, $inventory);

        $model = new \Magento\ConfigurableProduct\Model\Resource\Product\Collection\AssociatedProductUpdater(
            $stockItem
        );
        $model->update($collection);
    }
}
