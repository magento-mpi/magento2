<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer;

class StockItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StockItem
     */
    protected $stockItem;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $qtyProcessorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemMock;

    public function setUp()
    {
        $this->configMock = $this->getMockBuilder('Magento\Catalog\Model\ProductTypes\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $this->qtyProcessorMock = $this->getMockBuilder(
            'Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\QtyProcessor'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $this->stockItem = new StockItem($this->configMock, $this->qtyProcessorMock);
        $this->itemMock = $this->getMockBuilder('Magento\Sales\Model\Quote\Item')
            ->disableOriginalConstructor()
            ->setMethods(['getParentItem', 'getProduct'])
            ->getMock();
    }

    public function testInitialize()
    {
        $qty = 1;
        $rowQty = 2;
        $qtyForCheck = 3;

        $stockItemMock = $this->getMockBuilder('Magento\CatalogInventory\Model\Stock\Item')
            ->disableOriginalConstructor()
            ->setMethods(['hasIsChildItem', 'checkQuoteItemQty', 'setProduct'])
            ->getMock();
        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->setMethods(['getTypeInstance', 'getForceChildItemQtyChanges', 'getCustomOption', 'getName'])
            ->getMock();

        $customOptionMock = $this->getMockBuilder('Magneto\Framework\Object')
            ->disableOriginalConstructor()
            ->setMethods(['getValue'])
            ->getMock();

        $productMock->expects($this->any())
            ->method('getCustomOption')
            ->with('product_type')
            ->willReturn($customOptionMock);
        $productMock->expects($this->any())
            ->method('getName')
            ->willReturn('product_name');
        $productMock->expects($this->any())
            ->method('getTypeInstance')
            ->willReturn('product_name');

        $parentItemMock = $this->getMockBuilder('Magento\Sales\Model\Quote\Item')
            ->disableOriginalConstructor()
            ->getMock();
        $parentItemMock->expects($this->any())
            ->method('getProduct')
            ->willReturn($productMock);

        $itemMock = $this->getMockBuilder('Magento\Sales\Model\Quote\Item')
            ->disableOriginalConstructor()
            ->getMock();
        $itemMock->expects($this->any())
            ->method('getProduct')
            ->willReturn($productMock);
        $itemMock->expects($this->any())
            ->method('getParentItem')
            ->willReturn($parentItemMock);
        $this->qtyProcessorMock->expects($this->once())
            ->method('getRowQty')
            ->with($qty)
            ->willReturn($rowQty);
        $this->qtyProcessorMock->expects($this->once())
            ->method('getQtyForCheck')
            ->with($qty)
            ->willReturn($qtyForCheck);

        $this->configMock->expects($this->any())
            ->method('isProductSet')
            ->willReturn(true);

        $stockItemMock->expects($this->any())
            ->method('hasIsChildItem')
            ->willReturn(true);
        $resultMock = $this->getMockBuilder('Magento\Framework\Object')
            ->disableOriginalConstructor()
            ->setMethods(['getItemIsQtyDecimal', 'getHasQtyOptionUpdate'])
            ->getMock();
        $resultMock->expects($this->any())
            ->method('getItemIsQtyDecimal')
            ->willReturn(true);
        $stockItemMock->expects($this->any())
            ->method('checkQuoteItemQty')
            ->willReturn($resultMock);

        $this->assertInstanceOf(
            'Magento\Framework\Object',
            $this->stockItem->initialize($stockItemMock, $itemMock, $qty)
        );
    }
}
