<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Model\Product\CopyConstructor;

class CatalogInventoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogInventory\Model\Product\CopyConstructor\CatalogInventory
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $duplicateMock;

    /**
     * @var \Magento\CatalogInventory\Service\V1\Data\StockItem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockItemDoMock;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\CatalogInventory\Service\V1\StockItemService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockItemServiceMock;

    protected function setUp()
    {
        $this->productMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            array('__wakeup'),
            array(),
            '',
            false
        );

        $this->duplicateMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            array('setStockData', '__wakeup'),
            array(),
            '',
            false
        );

        $this->stockItemDoMock = $this->getMock(
            'Magento\CatalogInventory\Service\V1\Data\StockItem',
            [
                'getStockId',
                'isUseConfigEnableQtyInc',
                'isEnableQtyIncrements',
                'isUseConfigQtyIncrements',
                'getQtyIncrements'
            ],
            [],
            '',
            false
        );

        $this->stockItemServiceMock = $this->getMock(
            'Magento\CatalogInventory\Service\V1\StockItemService',
            ['getStockItem'],
            [],
            '',
            false
        );

        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $this->objectManager->getObject(
            'Magento\CatalogInventory\Model\Product\CopyConstructor\CatalogInventory',
            ['stockItemService' => $this->stockItemServiceMock]
        );
    }

    public function testBuildWithoutCurrentProductStockItem()
    {
        $expectedData = array(
            'use_config_min_qty' => 1,
            'use_config_min_sale_qty' => 1,
            'use_config_max_sale_qty' => 1,
            'use_config_backorders' => 1,
            'use_config_notify_stock_qty' => 1
        );
        $this->stockItemDoMock->expects($this->any())->method('getStockId')->will($this->returnValue(false));

        $this->stockItemServiceMock->expects($this->once())
            ->method('getStockItem')
            ->will($this->returnValue($this->stockItemDoMock));

        $this->duplicateMock->expects($this->once())->method('setStockData')->with($expectedData);
        $this->model->build($this->productMock, $this->duplicateMock);
    }

    public function testBuildWithCurrentProductStockItem()
    {
        $expectedData = array(
            'use_config_min_qty' => 1,
            'use_config_min_sale_qty' => 1,
            'use_config_max_sale_qty' => 1,
            'use_config_backorders' => 1,
            'use_config_notify_stock_qty' => 1,
            'use_config_enable_qty_inc' => 'use_config_enable_qty_inc',
            'enable_qty_increments' => 'enable_qty_increments',
            'use_config_qty_increments' => 'use_config_qty_increments',
            'qty_increments' => 'qty_increments'
        );
        $this->stockItemServiceMock->expects($this->once())
            ->method('getStockItem')
            ->will($this->returnValue($this->stockItemDoMock));

        $this->stockItemDoMock->expects($this->any())->method('getStockId')->will($this->returnValue(50));

        $this->stockItemDoMock->expects($this->any())
            ->method('isUseConfigEnableQtyInc')
            ->will($this->returnValue('use_config_enable_qty_inc'));
        $this->stockItemDoMock->expects($this->any())
            ->method('isEnableQtyIncrements')
            ->will($this->returnValue('enable_qty_increments'));
        $this->stockItemDoMock->expects($this->any())
            ->method('isUseConfigQtyIncrements')
            ->will($this->returnValue('use_config_qty_increments'));
        $this->stockItemDoMock->expects($this->any())
            ->method('getQtyIncrements')
            ->will($this->returnValue('qty_increments'));

        $this->duplicateMock->expects($this->once())->method('setStockData')->with($expectedData);
        $this->model->build($this->productMock, $this->duplicateMock);
    }
}
