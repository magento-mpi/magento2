<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Model\Quote\Tax;

/**
 * Test class for \Magento\GiftWrapping\Model\Quote\Tax\GiftwrappingAfterTax
 */
class GiftWrappingAfterTaxTest extends \PHPUnit_Framework_TestCase
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
        $addressMock = $this->_prepareData();

        $model = new \Magento\GiftWrapping\Model\Total\Quote\Tax\GiftwrappingAfterTax($helperMock);
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
                'getAllNonNominalItems',
                'setGwItemsBaseTaxAmount',
                'setGwItemsTaxAmount',
                'getExtraTaxableDetails',
                'getGWItemCodeToItemMapping',
            ],
            [],
            '',
            false
        );

        $input = [
            'item_gw' => [
                'item_gw1' => [
                    [
                        'code' => 'item_gw1',
                        'base_row_tax' => 20,
                        'row_tax' => 10,
                    ],
                ],
            ],
        ];
        $this->_addressMock->expects($this->once())->method('getExtraTaxableDetails')->will($this->returnValue($input));
        $item = new \Magento\Framework\Object();
        $this->_addressMock
            ->expects($this->once())
            ->method('getGWItemCodeToItemMapping')
            ->will($this->returnValue(['item_gw1' => $item]));
        $storeMock->expects($this->any())->method('convertPrice')->will($this->returnValue(10));
        $product->expects($this->any())->method('isVirtual')->will($this->returnValue(false));
        $quote = new \Magento\Framework\Object(
            [
                'isMultishipping' => false,
                'store' => $storeMock,
                'billingAddress' => null,
                'customerTaxClassId' => null,
            ]
        );

        $this->_wrappingMock->expects($this->any())->method('load')->will($this->returnSelf());
        $this->_wrappingMock->expects($this->any())->method('getBasePrice')->will($this->returnValue(6));

        $product->setGiftWrappingPrice(10);
        $item->setProduct($product)->setQty(2)->setGwId(1)->setGwPrice(5)->setGwBasePrice(10);
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
            $this->returnValue([$item])
        );

        $this->_addressMock->expects($this->once())->method('setGwItemsBaseTaxAmount')->with(20);
        $this->_addressMock->expects($this->once())->method('setGwItemsTaxAmount')->with(10);
        return $this->_addressMock;
    }
}
