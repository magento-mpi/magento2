<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Adminhtml\Order\Create\Items;

class GridTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Sales\Block\Adminhtml\Order\Create\Items\Grid
     */
    protected $_block;

    /**
     * Initialize required data
     */
    protected function setUp()
    {
        $orderCreateMock = $this->getMock(
            'Magento\Sales\Model\AdminOrder\Create',
            array('__wakeup'),
            array(),
            '',
            false
        );

        $taxData = $this->getMockBuilder('Magento\Tax\Helper\Data')->disableOriginalConstructor()->getMock();

        $coreData = $this->getMockBuilder('Magento\Core\Helper\Data')->disableOriginalConstructor()->getMock();

        $sessionMock = $this->getMockBuilder(
            'Magento\Backend\Model\Session\Quote'
        )->disableOriginalConstructor()->setMethods(
            array('getQuote', '__wakeup')
        )->getMock();

        $quoteMock = $this->getMockBuilder(
            'Magento\Sales\Model\Quote'
        )->disableOriginalConstructor()->setMethods(
            array('getStore', '__wakeup')
        )->getMock();

        $storeMock = $this->getMockBuilder(
            'Magento\Store\Model\Store'
        )->disableOriginalConstructor()->setMethods(
            array('__wakeup', 'convertPrice')
        )->getMock();
        $storeMock->expects($this->any())->method('convertPrice')->will($this->returnArgument(0));

        $quoteMock->expects($this->any())->method('getStore')->will($this->returnValue($storeMock));

        $sessionMock->expects($this->any())->method('getQuote')->will($this->returnValue($quoteMock));

        $wishlistFactoryMock = $this->getMockBuilder(
            'Magento\Wishlist\Model\WishlistFactory'
        )->setMethods(
            array('methods', '__wakeup')
        )->getMock();

        $giftMessageSave = $this->getMockBuilder(
            'Magento\Giftmessage\Model\Save'
        )->setMethods(
            array('__wakeup')
        )->disableOriginalConstructor()->getMock();

        $taxConfig = $this->getMockBuilder('Magento\Tax\Model\Config')->disableOriginalConstructor()->getMock();

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_block = $helper->getObject(
            'Magento\Sales\Block\Adminhtml\Order\Create\Items\Grid',
            array(
                'wishlistFactory' => $wishlistFactoryMock,
                'giftMessageSave' => $giftMessageSave,
                'taxConfig' => $taxConfig,
                'taxData' => $taxData,
                'sessionQuote' => $sessionMock,
                'orderCreate' => $orderCreateMock,
                'coreData' => $coreData
            )
        );
    }

    /**
     * @param array $itemData
     * @param string $expectedMessage
     * @param string $productType
     * @dataProvider tierPriceDataProvider
     */
    public function testTierPriceInfo($itemData, $expectedMessage, $productType)
    {
        $itemMock = $this->_prepareItem($itemData, $productType);
        $result = $this->_block->getTierHtml($itemMock);

        $this->assertEquals($expectedMessage, $result);
    }

    /**
     * Provider for test
     *
     * @return array
     */
    public function tierPriceDataProvider()
    {
        return array(
            array(
                array(array('price' => 100, 'price_qty' => 1)),
                '1 with 100% discount each',
                \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE
            ),
            array(
                array(array('price' => 100, 'price_qty' => 1), array('price' => 200, 'price_qty' => 2)),
                '1 with 100% discount each<br/>2 with 200% discount each',
                \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE
            ),
            array(
                array(array('price' => 50, 'price_qty' => 2)),
                '2 for 50',
                \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
            ),
            array(
                array(array('price' => 50, 'price_qty' => 2), array('price' => 150, 'price_qty' => 3)),
                '2 for 50<br/>3 for 150',
                \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
            ),
            array(0, '', \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
        );
    }

    /**
     * @param array|int $tierPrices
     * @param string $productType
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Sales\Model\Quote\Item
     */
    protected function _prepareItem($tierPrices, $productType)
    {
        $product = $this->getMockBuilder(
            'Magento\Catalog\Model\Product'
        )->disableOriginalConstructor()->setMethods(
            array('getTierPrice', '__wakeup')
        )->getMock();
        $product->expects($this->once())->method('getTierPrice')->will($this->returnValue($tierPrices));
        $item = $this->getMock(
            'Magento\Sales\Model\Quote\Item',
            array(),
            array('getProduct', 'getProductType'),
            '',
            false
        );
        $item->expects($this->once())->method('getProduct')->will($this->returnValue($product));

        $calledTimes = $tierPrices ? 'once' : 'never';
        $item->expects($this->{$calledTimes}())->method('getProductType')->will($this->returnValue($productType));
        return $item;
    }

    /**
     * @covers \Magento\Sales\Block\Adminhtml\Order\Create\Items\Grid::getItems
     */
    public function testGetItems()
    {
        $layoutMock = $this->getMock('\Magento\Framework\View\LayoutInterface');
        $blockMock = $this->getMock(
            '\Magento\Framework\View\Element\AbstractBlock',
            array('getItems'), array(), '', false
        );


        $itemMock = $this->getMock(
            '\Magento\Sales\Model\Quote\Item',
            array('getProduct', 'setHasError', 'setQty', 'getQty', '__sleep', '__wakeup'), array(), '', false
        );
        $productMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            array('getStockItem', 'getStatus', '__sleep', '__wakeup'), array(), '', false
        );
        $stockItemMock = $this->getMock(
            '\Magento\CatalogInventory\Model\Stock\Item',
            array(), array(), '', false
        );
        $checkMock = $this->getMock(
            '\Magento\Framework\Object',
            array('getMessage', 'getHasError'), array(), '', false
        );

        $layoutMock->expects($this->once())->method('getParentName')->will($this->returnValue('parentBlock'));
        $layoutMock->expects($this->once())->method('getBlock')->with('parentBlock')
            ->will($this->returnValue($blockMock));

        $blockMock->expects($this->once())->method('getItems')->will($this->returnValue(array($itemMock)));

        $itemMock->expects($this->any())->method('getChildren')->will($this->returnValue(array($itemMock)));
        $itemMock->expects($this->any())->method('getProduct')->will($this->returnValue($productMock));

        $productMock->expects($this->any())->method('getStockItem')->will($this->returnValue($stockItemMock));
        $productMock->expects($this->any())->method('getStatus')
            ->will($this->returnValue(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED));

        $checkMock->expects($this->any())->method('getMessage')->will($this->returnValue('Message'));
        $checkMock->expects($this->any())->method('getHasError')->will($this->returnValue(false));

        $stockItemMock->expects($this->once())->method('checkQuoteItemQty')->will($this->returnValue($checkMock));

        $this->_block->getQuote()->setIsSuperMode(true);
        $items = $this->_block->setLayout($layoutMock)->getItems();

        $this->assertEquals('Message', $items[0]->getMessage());
        $this->assertEquals(true, $this->_block->getQuote()->getIsSuperMode());
    }
}
