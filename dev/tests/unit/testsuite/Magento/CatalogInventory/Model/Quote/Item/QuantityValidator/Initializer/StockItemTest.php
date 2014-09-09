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
     * @var \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\StockItem
     */
    protected $model;

    /**
     * @var \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\QuoteItemQtyList| \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteItemQtyList;

    /**
     * @var \Magento\Catalog\Model\ProductTypes\ConfigInterface| \PHPUnit_Framework_MockObject_MockObject
     */
    protected $typeConfig;

    protected function setUp()
    {
        $this->quoteItemQtyList = $this
            ->getMockBuilder('Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\QuoteItemQtyList')
            ->disableOriginalConstructor()
            ->getMock();

        $this->typeConfig = $this
            ->getMockBuilder('Magento\Catalog\Model\ProductTypes\ConfigInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->model = $objectManagerHelper->getObject(
            'Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\StockItem',
            [
                'quoteItemQtyList' => $this->quoteItemQtyList,
                'typeConfig'       => $this->typeConfig
            ]
        );
    }

    public function testInitializeWithSubitem()
    {
        $qty = 1;
        $parentItemQty = 3;
        $productId = 1;
        $quoteItemId = 2;
        $quoteId = 3;
        $qtyForCheck = 10;

        $stockItem = $this->getMockBuilder('Magento\CatalogInventory\Model\Stock\Item')
            ->disableOriginalConstructor()
            ->getMock();

        $quoteItem = $this->getMockBuilder('Magento\Sales\Model\Quote\Item')
            ->disableOriginalConstructor()
            ->getMock();

        $parentItem = $this->getMockBuilder('Magento\Sales\Model\Quote\Item')
            ->disableOriginalConstructor()
            ->getMock();

        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        $parentProduct = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        $productTypeInstance = $this->getMockBuilder('Magento\Catalog\Model\Product\Type\AbstractType')
            ->disableOriginalConstructor()
            ->getMock();

        $productTypeCustomOption = $this->getMockBuilder('Magento\Catalog\Model\Product\Configuration\Item\Option')
            ->disableOriginalConstructor()
            ->getMock();

        $result = $this->getMockBuilder('Magento\Framework\Object')
            ->disableOriginalConstructor()
            ->getMock();

        $quoteItem->expects($this->any())->method('getParentItem')->will($this->returnValue($parentItem));
        $parentItem->expects($this->once())->method('getQty')->will($this->returnValue($parentItemQty));
        $quoteItem->expects($this->any())->method('getProduct')->will($this->returnValue($product));
        $product->expects($this->any())->method('getId')->will($this->returnValue($productId));
        $quoteItem->expects($this->once())->method('getId')->will($this->returnValue($quoteItemId));
        $quoteItem->expects($this->at(4))->method('__call')->with('getQuoteId')->will($this->returnValue($quoteId));

        $this->quoteItemQtyList->expects($this->any())
            ->method('getQty')
            ->with($productId, $quoteItemId, $quoteId, 0)
            ->will($this->returnValue($qtyForCheck));

        $stockItem->expects($this->once())
            ->method('checkQuoteItemQty')
            ->with($parentItemQty * $qty, $qtyForCheck, $qty)
            ->will($this->returnValue($result));

        $product->expects($this->once())
            ->method('getCustomOption')
            ->with('product_type')
            ->will($this->returnValue($productTypeCustomOption));
        $productTypeCustomOption->expects($this->once())
            ->method('getValue')
            ->will(($this->returnValue('option_value')));
        $this->typeConfig->expects($this->once())
            ->method('isProductSet')
            ->with('option_value')
            ->will($this->returnValue(true));
        $product->expects($this->once())->method('getName')->will($this->returnValue('product_name'));
        $stockItem->expects($this->at(0))
            ->method('__call')
            ->with('setProductName', ['product_name'])
            ->will($this->returnSelf());
        $stockItem->expects($this->at(1))->method('__call')->with('setIsChildItem', [true])->will($this->returnSelf());
        $stockItem->expects($this->at(3))->method('__call')->with('hasIsChildItem')->will($this->returnValue(true));
        $stockItem->expects($this->at(4))->method('__call')->with('unsIsChildItem');
        $result->expects($this->at(0))->method('__call')->with('getItemIsQtyDecimal')->will($this->returnValue(true));
        $result->expects($this->at(1))->method('__call')->with('getItemIsQtyDecimal')->will($this->returnValue(true));
        $quoteItem->expects($this->at(7))->method('__call')->with('setIsQtyDecimal', [true])->will($this->returnSelf());
        $result->expects($this->at(2))->method('__call')->with('getItemIsQtyDecimal')->will($this->returnValue(true));
        $parentItem->expects($this->at(1))
            ->method('__call')
            ->with('setIsQtyDecimal', [true])
            ->will($this->returnSelf());
        $parentItem->expects($this->any())->method('getProduct')->will($this->returnValue($parentProduct));
        $result->expects($this->at(3))->method('__call')->with('getHasQtyOptionUpdate')->will($this->returnValue(true));
        $parentProduct->expects($this->once())
            ->method('getTypeInstance')
            ->will($this->returnValue($productTypeInstance));
        $productTypeInstance->expects($this->once())
            ->method('getForceChildItemQtyChanges')
            ->with($product)->will($this->returnValue(true));
        $result->expects($this->at(4))->method('__call')->with('getOrigQty')->will($this->returnValue('orig_qty'));
        $quoteItem->expects($this->once())->method('setData')->with('qty', 'orig_qty')->will($this->returnSelf());
        $result->expects($this->at(5))->method('__call')->with('getItemUseOldQty')->will($this->returnValue('item'));
        $result->expects($this->at(6))->method('__call')->with('getItemUseOldQty')->will($this->returnValue('item'));
        $quoteItem->expects($this->at(14))->method('__call')->with('setUseOldQty', ['item'])->will($this->returnSelf());
        $result->expects($this->at(7))->method('__call')->with('getMessage')->will($this->returnValue('message'));
        $result->expects($this->at(8))->method('__call')->with('getMessage')->will($this->returnValue('message'));
        $quoteItem->expects($this->once())->method('setMessage')->with('message')->will($this->returnSelf());
        $result->expects($this->at(9))
            ->method('__call')
            ->with('getItemBackorders')
            ->will($this->returnValue('backorders'));
        $result->expects($this->at(10))
            ->method('__call')
            ->with('getItemBackorders')
            ->will($this->returnValue('backorders'));
        $quoteItem->expects($this->at(16))
            ->method('__call')
            ->with('setBackorders', ['backorders'])
            ->will($this->returnSelf());

        $this->model->initialize($stockItem, $quoteItem, $qty);
    }

    public function testInitializeWithoutSubitem()
    {
        $qty = 1;
        $productId = 1;
        $quoteItemId = 2;
        $quoteId = 3;
        $qtyForCheck = 55;

        $stockItem = $this->getMockBuilder('Magento\CatalogInventory\Model\Stock\Item')
            ->disableOriginalConstructor()
            ->getMock();

        $quoteItem = $this->getMockBuilder('Magento\Sales\Model\Quote\Item')
            ->disableOriginalConstructor()
            ->getMock();

        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        $productTypeCustomOption = $this->getMockBuilder('Magento\Catalog\Model\Product\Configuration\Item\Option')
            ->disableOriginalConstructor()
            ->getMock();

        $result = $this->getMockBuilder('Magento\Framework\Object')
            ->disableOriginalConstructor()
            ->getMock();

        $quoteItem->expects($this->any())->method('getParentItem')->will($this->returnValue(false));
        $quoteItem->expects($this->at(1))->method('__call')->with('getQtyToAdd')->will($this->returnValue(1));
        $quoteItem->expects($this->at(2))->method('__call')->with('getQtyToAdd')->will($this->returnValue(1));
        $quoteItem->expects($this->any())->method('getProduct')->will($this->returnValue($product));
        $product->expects($this->any())->method('getId')->will($this->returnValue($productId));
        $quoteItem->expects($this->once())->method('getId')->will($this->returnValue($quoteItemId));
        $quoteItem->expects($this->at(5))->method('__call')->with('getQuoteId')->will($this->returnValue($quoteId));
        $this->quoteItemQtyList->expects($this->any())
            ->method('getQty')
            ->with($productId, $quoteItemId, $quoteId, $qty)
            ->will($this->returnValue($qtyForCheck));
        $stockItem->expects($this->once())
            ->method('checkQuoteItemQty')
            ->with($qty, $qtyForCheck, $qty)
            ->will($this->returnValue($result));
        $product->expects($this->once())
            ->method('getCustomOption')
            ->with('product_type')
            ->will($this->returnValue($productTypeCustomOption));
        $productTypeCustomOption->expects($this->once())
            ->method('getValue')
            ->will(($this
                    ->returnValue('option_value')));
        $this->typeConfig->expects($this->once())
            ->method('isProductSet')
            ->with('option_value')
            ->will($this->returnValue(true));
        $product->expects($this->once())->method('getName')->will($this->returnValue('product_name'));
        $stockItem->expects($this->at(0))
            ->method('__call')
            ->with('setProductName', ['product_name'])
            ->will($this->returnSelf());
        $stockItem->expects($this->at(1))->method('__call')->with('setIsChildItem', [true])->will($this->returnSelf());
        $stockItem->expects($this->at(3))->method('__call')->with('hasIsChildItem')->will($this->returnValue(false));
        $result->expects($this->at(0))->method('__call')->with('getItemIsQtyDecimal')->will($this->returnValue(null));
        $result->expects($this->at(1))
            ->method('__call')
            ->with('getHasQtyOptionUpdate')
            ->will($this->returnValue(false));
        $result->expects($this->at(2))->method('__call')->with('getItemUseOldQty')->will($this->returnValue(null));
        $result->expects($this->at(3))->method('__call')->with('getMessage')->will($this->returnValue(null));
        $result->expects($this->at(4))->method('__call')->with('getItemBackorders')->will($this->returnValue(null));

        $this->model->initialize($stockItem, $quoteItem, $qty);
    }
}
