<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\AdminOrder\Product\Quote;

/**
 * Initializer test
  */
class InitializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Sales\Model\Quote|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteMock;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \Magento\Framework\Object|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\CatalogInventory\Service\V1\StockItemService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockItemServiceMock;

    /**
     * @var \Magento\Sales\Model\AdminOrder\Product\Quote\Initializer
     */
    protected $model;

    protected function setUp()
    {
        $this->quoteMock = $this->getMock(
            'Magento\Sales\Model\Quote',
            ['addProductAdvanced', '__wakeup'],
            [],
            '',
            false
        );

        $this->productMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['getId', 'setIsQtyDecimal', 'setCartQty', '__wakeup'],
            [],
            '',
            false
        );

        $this->configMock = $this->getMock(
            'Magento\Framework\Object',
            ['getQty', 'setQty'],
            [],
            '',
            false
        );

        $this->stockItemServiceMock = $this->getMock(
            'Magento\CatalogInventory\Service\V1\StockItemService',
            ['getStockItem', '__wakeup'],
            [],
            '',
            false
        );

        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $this->objectManager
            ->getObject(
                'Magento\Sales\Model\AdminOrder\Product\Quote\Initializer',
                ['stockItemService' => $this->stockItemServiceMock]
            );
    }

    public function testInitWithDecimalQty()
    {
        $quoteItemMock = $this->getMock(
            '\Magento\Sales\Model\Quote\Item',
            ['getStockId', 'getIsQtyDecimal', '__wakeup'],
            [],
            '',
            false
        );

        $this->stockItemServiceMock->expects($this->once())
            ->method('getStockItem')
            ->will($this->returnValue($this->getStockItemDo(true)));

        $this->productMock->expects($this->once())
            ->method('getId')
            ->will($this->returnSelf());

        $this->productMock->expects($this->once())
            ->method('setIsQtyDecimal')
            ->will($this->returnSelf());

        $this->productMock->expects($this->once())
            ->method('setCartQty')
            ->will($this->returnSelf());

        $this->configMock->expects($this->once())
            ->method('getQty')
            ->will($this->returnValue(20));

        $this->configMock->expects($this->never())
            ->method('setQty');

        $this->quoteMock->expects($this->once())
            ->method('addProductAdvanced')
            ->will($this->returnValue($quoteItemMock));

        $this->assertInstanceOf(
            'Magento\Sales\Model\Quote\Item',
            $this->model->init(
                $this->quoteMock,
                $this->productMock,
                $this->configMock
            )
        );
    }

    public function testInitWithNonDecimalQty()
    {
        $quoteItemMock = $this->getMock(
            '\Magento\Sales\Model\Quote\Item',
            ['getStockId', 'getIsQtyDecimal', '__wakeup'],
            [],
            '',
            false
        );

        $this->stockItemServiceMock->expects($this->once())
            ->method('getStockItem')
            ->will($this->returnValue($this->getStockItemDo(false)));

        $this->productMock->expects($this->once())
            ->method('getId')
            ->will($this->returnSelf());

        $this->productMock->expects($this->never())
            ->method('setIsQtyDecimal');

        $this->productMock->expects($this->once())
            ->method('setCartQty')
            ->will($this->returnSelf());

        $this->configMock->expects($this->exactly(2))
            ->method('getQty')
            ->will($this->returnValue(10));

        $this->configMock->expects($this->once())
            ->method('setQty')
            ->will($this->returnSelf());


        $this->quoteMock->expects($this->once())
            ->method('addProductAdvanced')
            ->will($this->returnValue($quoteItemMock));

        $this->assertInstanceOf(
            'Magento\Sales\Model\Quote\Item',
            $this->model->init(
                $this->quoteMock,
                $this->productMock,
                $this->configMock
            )
        );
    }

    /**
     * @param bool $isQtyDecimal
     * @return \Magento\CatalogInventory\Service\V1\Data\StockItem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getStockItemDo($isQtyDecimal)
    {
        $stockItemDoMock = $this->getMock(
            'Magento\CatalogInventory\Service\V1\Data\StockItem',
            ['getStockId', 'getIsQtyDecimal'],
            [],
            '',
            false
        );

        $stockItemDoMock->expects($this->once())
            ->method('getStockId')
            ->will($this->returnValue(5));

        $stockItemDoMock->expects($this->once())
            ->method('getIsQtyDecimal')
            ->will($this->returnValue($isQtyDecimal));

        return $stockItemDoMock;
    }
}
