<?php
/** 
 * 
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Coupon;

class WriteServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var WriteService
     */
    protected $service;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteLoaderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $couponBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $couponCodeDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteAddressMock;

    protected function setUp()
    {
        $objectManager =new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->quoteLoaderMock = $this->getMock('\Magento\Checkout\Service\V1\QuoteLoader', [], [], '', false);
        $this->storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManagerInterface');
        $this->couponBuilderMock =
            $this->getMock('\Magento\Checkout\Service\V1\Data\Cart\CouponBuilder', [], [], '', false);
        $this->storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->quoteMock = $this->getMock(
            '\Magento\Sales\Model\Quote',
            [
                'getItemsCount',
                'setCouponCode',
                'collectTotals',
                'save',
                'getShippingAddress',
                'getCouponCode',
                '__wakeup'
            ],
            [],
            '',
            false
        );
        $this->couponCodeDataMock = $this->getMock('\Magento\Checkout\Service\V1\Data\Cart\Coupon', [], [], '', false);
        $this->quoteAddressMock = $this->getMock(
            '\Magento\Sales\Model\Quote\Address',
            [
                'setCollectShippingRates',
                '__wakeup'
            ],
            [],
            '',
            false)
        ;
        $this->service = $objectManager->getObject(
            'Magento\Checkout\Service\V1\Coupon\WriteService',
            [
                'quoteLoader' => $this->quoteLoaderMock,
                'couponBuilder' => $this->couponBuilderMock,
                'storeManager' => $this->storeManagerMock
            ]
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Cart 33 doesn't contain products
     */
    public function testSetWhenCartDoesNotContainsProducts()
    {
        $storeId = 10;
        $cartId = 33;

        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($this->storeMock));
        $this->storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->quoteLoaderMock->expects($this->once())
            ->method('load')->with($cartId, $storeId)->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('getItemsCount')->will($this->returnValue(0));

        $this->service->set($cartId, $this->couponCodeDataMock);
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Could not apply coupon code
     */
    public function testSetWhenCouldNotApplyCoupon()
    {
        $storeId = 10;
        $cartId = 33;
        $couponCode = '153a-ABC';

        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($this->storeMock));
        $this->storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->quoteLoaderMock->expects($this->once())
            ->method('load')->with($cartId, $storeId)->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('getItemsCount')->will($this->returnValue(12));
        $this->quoteMock->expects($this->once())
            ->method('getShippingAddress')->will($this->returnValue($this->quoteAddressMock));
        $this->quoteAddressMock->expects($this->once())->method('setCollectShippingRates')->with(true);
        $this->couponCodeDataMock->expects($this->once())
            ->method('getCouponCode')->will($this->returnValue($couponCode));
        $this->quoteMock->expects($this->once())->method('setCouponCode')->with($couponCode);
        $exceptionMessage = 'Could not apply coupon code';
        $exception = new \Magento\Framework\Exception\CouldNotDeleteException($exceptionMessage);
        $this->quoteMock->expects($this->once())->method('collectTotals')->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('save')->will($this->throwException($exception));

        $this->service->set($cartId, $this->couponCodeDataMock);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Coupon code is not valid
     */
    public function testSetWhenCouponCodeIsInvalid()
    {
        $storeId = 10;
        $cartId = 33;
        $couponCode = '153a-ABC';

        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($this->storeMock));
        $this->storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->quoteLoaderMock->expects($this->once())
            ->method('load')->with($cartId, $storeId)->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('getItemsCount')->will($this->returnValue(12));
        $this->quoteMock->expects($this->once())
            ->method('getShippingAddress')->will($this->returnValue($this->quoteAddressMock));
        $this->quoteAddressMock->expects($this->once())->method('setCollectShippingRates')->with(true);
        $this->couponCodeDataMock->expects($this->once())
            ->method('getCouponCode')->will($this->returnValue($couponCode));
        $this->quoteMock->expects($this->once())->method('setCouponCode')->with($couponCode);
        $this->quoteMock->expects($this->once())->method('collectTotals')->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('save');
        $this->quoteMock->expects($this->once())->method('getCouponCode')->will($this->returnValue('invalidCoupon'));

        $this->service->set($cartId, $this->couponCodeDataMock);
    }

    public function testSet()
    {
        $storeId = 10;
        $cartId = 33;
        $couponCode = '153a-ABC';

        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($this->storeMock));
        $this->storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->quoteLoaderMock->expects($this->once())
            ->method('load')->with($cartId, $storeId)->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('getItemsCount')->will($this->returnValue(12));
        $this->quoteMock->expects($this->once())
            ->method('getShippingAddress')->will($this->returnValue($this->quoteAddressMock));
        $this->quoteAddressMock->expects($this->once())->method('setCollectShippingRates')->with(true);
        $this->couponCodeDataMock->expects($this->once())
            ->method('getCouponCode')->will($this->returnValue($couponCode));
        $this->quoteMock->expects($this->once())->method('setCouponCode')->with($couponCode);
        $this->quoteMock->expects($this->once())->method('collectTotals')->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('save');
        $this->quoteMock->expects($this->once())->method('getCouponCode')->will($this->returnValue($couponCode));

        $this->assertTrue($this->service->set($cartId, $this->couponCodeDataMock));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Cart 65 doesn't contain products
     */
    public function testDeleteWhenCartDoesNotContainsProducts()
    {
        $storeId = 24;
        $cartId = 65;

        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($this->storeMock));
        $this->storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->quoteLoaderMock->expects($this->once())
            ->method('load')->with($cartId, $storeId)->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('getItemsCount')->will($this->returnValue(0));
        $this->quoteMock->expects($this->never())->method('getShippingAddress');

        $this->service->delete($cartId);
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     * @expectedExceptionMessage Could not delete coupon code
     */
    public function testDeleteWhenCouldNotDeleteCoupon()
    {
        $storeId = 24;
        $cartId = 65;

        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($this->storeMock));
        $this->storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->quoteLoaderMock->expects($this->once())
            ->method('load')->with($cartId, $storeId)->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('getItemsCount')->will($this->returnValue(12));
        $this->quoteMock->expects($this->once())
            ->method('getShippingAddress')->will($this->returnValue($this->quoteAddressMock));
        $this->quoteAddressMock->expects($this->once())->method('setCollectShippingRates')->with(true);
        $this->quoteMock->expects($this->once())->method('setCouponCode')->with('');
        $this->quoteMock->expects($this->once())->method('collectTotals')->will($this->returnValue($this->quoteMock));
        $exceptionMessage = 'Could not delete coupon code';
        $exception = new \Magento\Framework\Exception\CouldNotSaveException($exceptionMessage);
        $this->quoteMock->expects($this->once())->method('collectTotals')->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('save')->will($this->throwException($exception));

        $this->service->delete($cartId);
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     * @expectedExceptionMessage Could not delete coupon code
     */
    public function testDeleteWhenCouponIsNotEmpty()
    {
        $storeId = 24;
        $cartId = 65;

        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($this->storeMock));
        $this->storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->quoteLoaderMock->expects($this->once())
            ->method('load')->with($cartId, $storeId)->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('getItemsCount')->will($this->returnValue(12));
        $this->quoteMock->expects($this->once())
            ->method('getShippingAddress')->will($this->returnValue($this->quoteAddressMock));
        $this->quoteAddressMock->expects($this->once())->method('setCollectShippingRates')->with(true);
        $this->quoteMock->expects($this->once())->method('setCouponCode')->with('');
        $this->quoteMock->expects($this->once())->method('collectTotals')->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('collectTotals')->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('save');
        $this->quoteMock->expects($this->once())->method('getCouponCode')->will($this->returnValue('123_ABC'));

        $this->service->delete($cartId);
    }

    public function testDelete()
    {
        $storeId = 24;
        $cartId = 65;

        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($this->storeMock));
        $this->storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->quoteLoaderMock->expects($this->once())
            ->method('load')->with($cartId, $storeId)->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('getItemsCount')->will($this->returnValue(12));
        $this->quoteMock->expects($this->once())
            ->method('getShippingAddress')->will($this->returnValue($this->quoteAddressMock));
        $this->quoteAddressMock->expects($this->once())->method('setCollectShippingRates')->with(true);
        $this->quoteMock->expects($this->once())->method('setCouponCode')->with('');
        $this->quoteMock->expects($this->once())->method('collectTotals')->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('collectTotals')->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('save');
        $this->quoteMock->expects($this->once())->method('getCouponCode')->will($this->returnValue(''));

        $this->assertTrue($this->service->delete($cartId));
    }
}
 