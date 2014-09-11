<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Model\Quote;

/**
 * Test class for \Magento\GiftWrapping\Model\Quote\Giftwrapping
 */
class GiftWrappingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\GiftWrapping\Model\Wrapping
     */
    protected $_wrappingMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Sales\Model\Quote\Address
     */
    protected $_addressMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * Test for collect method
     *
     * @param bool $withProduct
     * @dataProvider collectQuoteDataProvider
     */
    public function testCollectQuote($withProduct)
    {
        $addressMock = $this->_prepareData($withProduct);
        $helperMock = $this->getMock('Magento\GiftWrapping\Helper\Data', array(), array(), '', false);
        $factoryMock = $this->getMock(
            'Magento\GiftWrapping\Model\WrappingFactory',
            array('create'),
            array(),
            '',
            false
        );
        $factoryMock->expects($this->any())->method('create')->will($this->returnValue($this->_wrappingMock));

        $model = new \Magento\GiftWrapping\Model\Total\Quote\Giftwrapping(
            $helperMock,
            $factoryMock,
            $this->priceCurrency
        );
        $model->collect($addressMock);
    }

    /**
     * Prepare mocks for test
     *
     * @param bool $withProduct
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Sales\Model\Quote\Address
     */
    protected function _prepareData($withProduct)
    {
        $product = $this->getMockBuilder(
            'Magento\Catalog\Model\Product'
        )->disableOriginalConstructor()->setMethods(
            array('isVirtual', '__wakeup')
        )->getMock();
        $storeMock = $this->getMockBuilder(
            'Magento\Store\Model\Store'
        )->disableOriginalConstructor()->setMethods(
            array('getId', '__wakeup')
        )->getMock();
        $this->_wrappingMock = $this->getMock(
            'Magento\GiftWrapping\Model\Wrapping',
            array('load', 'setStoreId', 'getBasePrice', '__wakeup'),
            array(),
            '',
            false
        );
        $this->_addressMock = $this->getMock(
            'Magento\Sales\Model\Quote\Address',
            array(
                'getAddressType',
                'getQuote',
                'getAllNonNominalItems',
                'setGwItemsBasePrice',
                'setGwItemsPrice',
                '__wakeup'
            ),
            array(),
            '',
            false
        );

        $this->priceCurrency = $this->getMockBuilder('Magento\Framework\Pricing\PriceCurrencyInterface')->getMock();
        $this->priceCurrency->expects($this->any())->method('convert')->will($this->returnValue(10));
        $product->expects($this->any())->method('isVirtual')->will($this->returnValue(false));
        $quote = new \Magento\Framework\Object(array('isMultishipping' => false, 'store' => $storeMock));

        $this->_wrappingMock->expects($this->any())->method('load')->will($this->returnSelf());
        $this->_wrappingMock->expects($this->any())->method('getBasePrice')->will($this->returnValue(6));

        $item = new \Magento\Framework\Object();
        if ($withProduct) {
            $product->setGiftWrappingPrice(10);
        } else {
            $product->setGiftWrappingPrice(0);
            $item->setWrapping($this->_wrappingMock);
        }
        $item->setProduct($product)->setQty(2)->setGwId(1);
        $this->_addressMock->expects(
            $this->any()
        )->method(
            'getAddressType'
        )->will(
            $this->returnValue(\Magento\Sales\Model\Quote\Address::TYPE_SHIPPING)
        );
        $this->_addressMock->expects($this->any())->method('getQuote')->will($this->returnValue($quote));
        $this->_addressMock->expects(
            $this->any()
        )->method(
            'getAllNonNominalItems'
        )->will(
            $this->returnValue(array($item))
        );

        if ($withProduct) {
            $this->_addressMock->expects($this->once())->method('setGwItemsBasePrice')->with(20);
        } else {
            $this->_addressMock->expects($this->once())->method('setGwItemsBasePrice')->with(12);
        }
        $this->_addressMock->expects($this->once())->method('setGwItemsPrice')->with(20);
        return $this->_addressMock;
    }

    /**
     * Data provider for testCollectQuote
     *
     * @return array
     */
    public function collectQuoteDataProvider()
    {
        return array('withProduct' => array(true), 'withoutProduct' => array(false));
    }
}
