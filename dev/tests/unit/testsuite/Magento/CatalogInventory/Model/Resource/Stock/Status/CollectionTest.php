<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogInventory\Model\Resource\Stock\Status;

/**
 * Test for \Magento\CatalogInventory\Model\Resource\Stock\Status\Collection
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $connection;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $select;

    /**
     * @var \Magento\CatalogInventory\Model\Resource\Stock\Status\Collection
     */
    protected $model;

    protected function setUp()
    {
        $this->select = $this->getMock('Magento\Framework\DB\Select', [], [], '', false);
        $this->connection = $this->getMock('Magento\Framework\DB\Adapter\Pdo\Mysql', [], [], '', false);
        $this->connection->expects($this->atLeastOnce())->method('select')->will($this->returnValue($this->select));
        $this->connection->expects($this->atLeastOnce())->method('quoteIdentifier')->will($this->returnArgument(0));
        $this->resource = $this->getMock('Magento\CatalogInventory\Model\Resource\Stock\Status', [], [], '', false);
        $this->resource->expects($this->any())->method('getReadConnection')
            ->will($this->returnValue($this->connection));

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManagerHelper->getObject(
            'Magento\CatalogInventory\Model\Resource\Stock\Status\Collection',
            [
                'resource' => $this->resource,
            ]
        );
    }

    /**
     * @covers \Magento\CatalogInventory\Model\Resource\Stock\Status\Collection::__construct
     * @covers \Magento\CatalogInventory\Model\Resource\Stock\Status\Collection::_construct
     * @covers \Magento\CatalogInventory\Model\Resource\Stock\Status\Collection::addWebsiteFilter
     */
    public function testAddingWebsiteFilter()
    {
        $website = $this->getMock('Magento\Store\Model\Website', ['getWebsiteId', '__wakeup'], [], '', false);
        $website->expects($this->atLeastOnce())->method('getWebsiteId')->will($this->returnValue(1));
        $this->connection->expects($this->atLeastOnce())->method('prepareSqlCondition')->with('website_id', 1)
            ->will($this->returnValue('condition_string'));
        $this->select->expects($this->atLeastOnce())->method('where')
            ->with('condition_string', $this->anything(), $this->anything());
        $this->model->addWebsiteFilter($website);
    }

    /**
     * @covers \Magento\CatalogInventory\Model\Resource\Stock\Status\Collection::__construct
     * @covers \Magento\CatalogInventory\Model\Resource\Stock\Status\Collection::_construct
     * @covers \Magento\CatalogInventory\Model\Resource\Stock\Status\Collection::addQtyFilter
     */
    public function testAddingQtyFilter()
    {
        $qty = 3;
        $this->connection->expects($this->atLeastOnce())
            ->method('prepareSqlCondition')
            ->with('main_table.qty', ['lteq' => $qty])
            ->will($this->returnValue('condition_string'));
        $this->select->expects($this->atLeastOnce())->method('where')
            ->with('condition_string', $this->anything(), $this->anything());
        $this->model->addQtyFilter($qty);
    }
}
