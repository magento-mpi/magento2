<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftWrapping\Model\Quote\Tax;

/**
 * Test class for \Magento\GiftWrapping\Model\Quote\Tax\Giftwrapping
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
     * Test for collect method
     */
    public function testCollectQuote()
    {
        $helperMock = $this->getMock('Magento\GiftWrapping\Helper\Data', [], [], '', false);
        $helperMock->expects($this->any())->method('getWrappingTaxClass')->will($this->returnValue(2));
        $addressMock = $this->_prepareData();

        $model = new \Magento\GiftWrapping\Model\Total\Quote\Tax\Giftwrapping($helperMock);
        $model->collect($addressMock);
    }

    /**
     * Prepare mocks for test
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Sales\Model\Quote\Address
     */
    protected function _prepareData()
    {
        $product = $this->getMockBuilder(
            'Magento\Catalog\Model\Product'
        )->disableOriginalConstructor()->setMethods(
            ['isVirtual', '__wakeup']
        )->getMock();
        $storeMock = $this->getMockBuilder(
            'Magento\Store\Model\Store'
        )->disableOriginalConstructor()->setMethods(
            ['convertPrice', 'getId', '__wakeup']
        )->getMock();
        $this->_wrappingMock = $this->getMock(
            'Magento\GiftWrapping\Model\Wrapping',
            ['load', 'setStoreId', 'getBasePrice', '__wakeup'],
            [],
            '',
            false
        );
        $this->_addressMock = $this->getMock(
            'Magento\Sales\Model\Quote\Address',
            [
                'getAddressType',
                'getQuote',
                '__wakeup',
                'getAllItems',
                'setGwItemsBaseTaxAmount',
                'setGwItemsTaxAmount',
                'getExtraTaxableDetails',
            ],
            [],
            '',
            false
        );

        $storeMock->expects($this->any())->method('convertPrice')->will($this->returnValue(10));
        $product->expects($this->any())->method('isVirtual')->will($this->returnValue(false));
        $quote = new \Magento\Framework\Object(
            [
                'isMultishipping' => false,
                'store' => $storeMock,
                'billingAddress' => null,
                'customerTaxClassId' => null,
                'tax_class_id' => 2,
            ]
        );

        $this->_wrappingMock->expects($this->any())->method('load')->will($this->returnSelf());
        $this->_wrappingMock->expects($this->any())->method('getBasePrice')->will($this->returnValue(6));

        $item = $this->getMock(
            '\Magento\Sales\Model\Quote\Item',
            [
                'setAssociatedTaxables',
                '__wakeup',
                'setProduct',
                'getProduct',
                'getQty',
            ],
            [],
            '',
            false
        );
        $product->setGiftWrappingPrice(10);
        $item->setGwId(1)->setGwPrice(5)->setGwBasePrice(10);
        $item->expects($this->any())->method('getProduct')->will($this->returnValue($product));
        $item->expects($this->any())->method('getQty')->will($this->returnValue(2));
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
            'getAllItems'
        )->will(
            $this->returnValue([$item])
        );

        $expected = [
            [
                'type' => 'item_gw',
                'code' => 'item_gw1',
                'unit_price' => 5,
                'base_unit_price' => 10,
                'quantity' => 2,
                'tax_class_id' => 2,
                'price_includes_tax' => false,
            ],
        ];
        $item->expects($this->once())->method('setAssociatedTaxables')->with($expected);
        return $this->_addressMock;
    }
}
