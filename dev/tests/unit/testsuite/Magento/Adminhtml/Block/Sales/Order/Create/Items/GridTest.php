<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Block_Sales_Order_Create_Items_GridTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\Magento\Adminhtml\Block\Sales\Order\Create\Items\Grid
     */
    protected $_block;

    /**
     * Initialize required data
     */
    public function setUp()
    {
        $helperFactory = $this->getMockBuilder('Magento\Core\Model\Factory\Helper')
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();

        $contextMock = $this->getMockBuilder('Magento\Backend\Block\Template\Context')
            ->disableOriginalConstructor()
            ->setMethods(array('getHelperFactory'))
            ->getMock();
        $contextMock->expects($this->any())->method('getHelperFactory')->will($this->returnValue($helperFactory));
        $this->_block = $this->getMockBuilder('Magento\Adminhtml\Block\Sales\Order\Create\Items\Grid')
            ->setConstructorArgs(array($contextMock))
            ->setMethods(array('_getSession'))
            ->getMock();
        $sessionMock = $this->getMockBuilder('Magento\Adminhtml\Model\Session\Quote')
            ->disableOriginalConstructor()
            ->setMethods(array('getQuote'))
            ->getMock();
        $quoteMock = $this->getMockBuilder('Magento\Sales\Model\Quote')
            ->disableOriginalConstructor()
            ->setMethods(array('getStore'))
            ->getMock();

        $storeMock = $this->getMockBuilder('Magento\Core\Model\Store')
            ->disableOriginalConstructor()
            ->setMethods(array('convertPrice'))
            ->getMock();
        $storeMock->expects($this->any())->method('convertPrice')->will($this->returnArgument(0));

        $quoteMock->expects($this->any())->method('getStore')->will($this->returnValue($storeMock));

        $sessionMock->expects($this->any())->method('getQuote')->will($this->returnValue($quoteMock));
        $this->_block->expects($this->any())->method('_getSession')->will($this->returnValue($sessionMock));
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
            array(
                0,
                '',
                \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
            ),
        );
    }

    /**
     * @param array|int $tierPrices
     * @param string $productType
     * @return PHPUnit_Framework_MockObject_MockObject|\Magento\Sales\Model\Quote\Item
     */
    protected function _prepareItem($tierPrices, $productType)
    {
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->setMethods(array('getTierPrice'))
            ->getMock();
        $product->expects($this->once())
            ->method('getTierPrice')
            ->will($this->returnValue($tierPrices));
        $item = $this->getMockBuilder('Magento\Sales\Model\Quote\Item')
            ->disableOriginalConstructor()
            ->setMethods(array('getProduct', 'getProductType'))
            ->getMock();
        $item->expects($this->once())
            ->method('getProduct')
            ->will($this->returnValue($product));

        $calledTimes = $tierPrices ? 'once' : 'never';
        $item->expects($this->$calledTimes())
            ->method('getProductType')
            ->will($this->returnValue($productType));
        return $item;
    }
}
