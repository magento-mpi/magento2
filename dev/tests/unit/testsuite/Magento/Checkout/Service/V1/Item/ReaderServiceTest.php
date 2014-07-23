<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Item;

use \Magento\Checkout\Service\V1\Data\Cart\Item as Item;

class ReaderServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReadService
     */
    protected $service;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteLoaderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    protected function setUp()
    {
        $this->quoteLoaderMock = $this->getMock('\Magento\Checkout\Service\V1\QuoteLoader', [], [], '', false);
        $this->itemBuilderMock = $this->getMock('\Magento\Checkout\Service\V1\Data\Cart\ItemBuilder', [], [], '', false);
        $this->storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManagerInterface');
        $this->service = new ReadService($this->quoteLoaderMock, $this->itemBuilderMock, $this->storeManagerMock);
    }

    public  function testGetList()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));
        $storeMock->expects($this->once())->method('getId')->will($this->returnValue(11));
        $quoteMock = $this->getMock('\Magento\Sales\Model\Quote', [], [], '', false);
        $this->quoteLoaderMock->expects($this->once())->method('load')
            ->with(array('cartId'), 11)
            ->will($this->returnValue($quoteMock));
        $itemMock = $this->getMock('\Magento\Sales\Model\Quote\Item',
            ['getSku', 'getName', 'getPrice', 'getQty', 'getProductType', '__wakeup'], [], '', false);
        $quoteMock->expects($this->any())->method('getAllItems')->will($this->returnValue(array($itemMock)));
        $itemMock->expects($this->any())->method('getSku')->will($this->returnValue('prd_SKU'));
        $itemMock->expects($this->any())->method('getName')->will($this->returnValue('prd_NAME'));
        $itemMock->expects($this->any())->method('getPrice')->will($this->returnValue(100.15));
        $itemMock->expects($this->any())->method('getQty')->will($this->returnValue(16));
        $itemMock->expects($this->any())->method('getProductType')->will($this->returnValue('simple'));
        $testData = [
            Item::SKU => 'prd_SKU',
            Item::NAME => 'prd_NAME',
            Item::PRICE => '100.15',
            Item::QTY => '16',
            Item::TYPE => 'simple',
        ];
        $this->itemBuilderMock->expects($this->once())
            ->method('populateWithArray')
            ->with($testData)
            ->will($this->returnValue($this->itemBuilderMock));
        $this->itemBuilderMock->expects($this->once())->method('create')->will($this->returnValue('Expected value'));

        $this->assertEquals(array('Expected value'), $this->service->getList(array('cartId')));
    }
}
