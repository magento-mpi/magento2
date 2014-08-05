<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Coupon;

use Magento\Checkout\Service\V1\Data\Cart\Coupon;

class ReadServiceTest extends \PHPUnit_Framework_TestCase
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
    protected $couponBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    protected function setUp()
    {
        $this->quoteLoaderMock = $this->getMock('\Magento\Checkout\Service\V1\QuoteLoader', [], [], '', false);
        $this->storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManagerInterface');
        $this->couponBuilderMock = $this->getMock('\Magento\Checkout\Service\V1\Data\Cart\CouponBuilder',
            [], [], '', false);
        $this->service = new ReadService($this->quoteLoaderMock, $this->couponBuilderMock, $this->storeManagerMock);
    }

    public function testGetCoupon()
    {
        $cartId = 11;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->once())->method('getId')->will($this->returnValue(12));
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));

        $quoteMock = $this->getMock('\Magento\Sales\Model\Quote', ['getCouponCode'], [], '', false);
        $quoteMock->expects($this->any())->method('getCouponCode')->will($this->returnValue(100500));

        $this->quoteLoaderMock->expects($this->once())->method('load')
            ->with($cartId, 12)->will($this->returnValue($quoteMock));

        $data = [Coupon::COUPON_CODE => $quoteMock->getCouponCode()];

        $this->couponBuilderMock->expects($this->once())->method('populateWithArray')
            ->with($data)->will($this->returnSelf());
        $this->couponBuilderMock->expects($this->once())->method('create')->will($this->returnValue('couponCode'));

        $this->assertEquals('couponCode', $this->service->get($cartId));
    }
}
 