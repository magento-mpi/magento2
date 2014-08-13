<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\PaymentMethod;

class WriteServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReadService
     */
    protected $service;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteLoaderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $paymentMethodBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $methodListMock;

    protected function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->quoteLoaderMock = $this->getMock('\Magento\Checkout\Service\V1\QuoteLoader', [], [], '', false);
        $this->storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManagerInterface');
        $this->paymentMethodBuilderMock = $this->getMock(
            '\Magento\Checkout\Service\V1\Data\Cart\PaymentMethod\Builder', [], [], '', false
        );
        $this->methodListMock = $this->getMock('\Magento\Payment\Model\MethodList', [], [], '', false);

        $this->service = $this->objectManager->getObject(
            '\Magento\Checkout\Service\V1\PaymentMethod\WriteService',
            [
                'quoteLoader' => $this->quoteLoaderMock,
                'storeManager' => $this->storeManagerMock,
                'paymentMethodBuilder' => $this->paymentMethodBuilderMock,
                'methodList' => $this->methodListMock,
            ]
        );
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Billing address is not set
     */
    public function testSetVirtualQuotePaymentThrowsExceptionIfBillingAdressNotSet()
    {
        $cartId = 11;
        $storeId = 12;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));

        $paymentsCollectionMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Collection\AbstractCollection', [], [], '', false
        );

        $quoteMock = $this->getMock('\Magento\Sales\Model\Quote', [], [], '', false);
        $quoteMock->expects($this->any())
            ->method('getPaymentsCollection')
            ->will($this->returnValue($paymentsCollectionMock));
        $quoteMock->expects($this->any())->method('isVirtual')->will($this->returnValue(true));

        $billingAddressMock = $this->getMock('\Magento\Sales\Model\Quote\Address', [], [], '', false);
        $quoteMock->expects($this->any())->method('getBillingAddress')->will($this->returnValue($billingAddressMock));

        $this->quoteLoaderMock->expects($this->once())
            ->method('load')
            ->with($cartId, $storeId)
            ->will($this->returnValue($quoteMock));

        $paymentMethodMock = $this->getMock('\Magento\Checkout\Service\V1\Data\Cart\PaymentMethod', [], [], '', false);

        $this->service->set($paymentMethodMock, $cartId);
    }

    public function testSetVirtualQuotePaymentSuccess()
    {
        $cartId = 11;
        $storeId = 12;
        $paymentId = 13;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));

        $paymentsCollectionMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Collection\AbstractCollection', [], [], '', false
        );

        $quoteMock = $this->getMock(
            '\Magento\Sales\Model\Quote',
            [
                'setTotalsCollectedFlag', '__wakeup', 'getPaymentsCollection', 'getPayment',
                'getItemsCollection', 'isVirtual', 'getBillingAddress', 'collectTotals'
            ], [], '', false
        );
        $quoteMock->expects($this->any())
            ->method('getPaymentsCollection')
            ->will($this->returnValue($paymentsCollectionMock));
        $quoteMock->expects($this->any())->method('isVirtual')->will($this->returnValue(true));

        $billingAddressMock =
            $this->getMock('\Magento\Sales\Model\Quote\Address', ['getCountryId', '__wakeup'], [], '', false);
        $billingAddressMock->expects($this->once())->method('getCountryId')->will($this->returnValue(1));
        $quoteMock->expects($this->any())->method('getBillingAddress')->will($this->returnValue($billingAddressMock));

        $quoteMock->expects($this->once())->method('setTotalsCollectedFlag')->will($this->returnSelf());
        $quoteMock->expects($this->once())->method('collectTotals')->will($this->returnSelf());

        $paymentMock = $this->getMock('Magento\Sales\Model\Quote\Payment', [], [], '', false);
        $paymentMock->expects($this->once())->method('getId')->will($this->returnValue($paymentId));

        $quoteMock->expects($this->once())->method('getPayment')->will($this->returnValue($paymentMock));

        $this->quoteLoaderMock->expects($this->once())
            ->method('load')
            ->with($cartId, $storeId)
            ->will($this->returnValue($quoteMock));

        $paymentMethodMock = $this->getMock('\Magento\Checkout\Service\V1\Data\Cart\PaymentMethod', [], [], '', false);

        $this->paymentMethodBuilderMock->expects($this->once())
            ->method('build')
            ->with($paymentMethodMock, $quoteMock)
            ->will($this->returnValue($paymentMock));

        $this->assertEquals($paymentId, $this->service->set($paymentMethodMock, $cartId));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Shipping address is not set
     */
    public function testSetNotVirtualQuotePaymentThrowsExceptionIfShippingAddressNotSet()
    {
        $cartId = 11;
        $storeId = 12;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));

        $quoteMock = $this->getMock(
            '\Magento\Sales\Model\Quote',
            ['__wakeup', 'getPaymentsCollection', 'isVirtual', 'getShippingAddress'], [], '', false
        );

        $paymentsCollectionMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Collection\AbstractCollection', [], [], '', false
        );

        $quoteMock->expects($this->any())
            ->method('getPaymentsCollection')
            ->will($this->returnValue($paymentsCollectionMock));
        $quoteMock->expects($this->any())->method('isVirtual')->will($this->returnValue(false));
        $quoteMock->expects($this->any())
            ->method('getShippingAddress')
            ->will($this->returnValue($this->getMock('\Magento\Sales\Model\Quote\Address', [], [], '', false)));

        $this->quoteLoaderMock->expects($this->once())
            ->method('load')
            ->with($cartId, $storeId)
            ->will($this->returnValue($quoteMock));

        $paymentMethodMock = $this->getMock('\Magento\Checkout\Service\V1\Data\Cart\PaymentMethod', [], [], '', false);
        $paymentMock = $this->getMock('Magento\Sales\Model\Quote\Payment', [], [], '', false);

        $this->paymentMethodBuilderMock->expects($this->once())
            ->method('build')
            ->with($paymentMethodMock, $quoteMock)
            ->will($this->returnValue($paymentMock));

        $this->service->set($paymentMethodMock, $cartId);
    }

    public function testSetNotVirtualQuotePaymentSuccess()
    {
        $cartId = 11;
        $storeId = 12;
        $paymentId = 13;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));

        $paymentsCollectionMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Collection\AbstractCollection', [], [], '', false
        );

        $quoteMock = $this->getMock(
            '\Magento\Sales\Model\Quote',
            [
                'setTotalsCollectedFlag', '__wakeup', 'getPaymentsCollection', 'getPayment',
                'getItemsCollection', 'isVirtual', 'getShippingAddress', 'collectTotals'
            ], [], '', false
        );
        $quoteMock->expects($this->any())
            ->method('getPaymentsCollection')
            ->will($this->returnValue($paymentsCollectionMock));
        $quoteMock->expects($this->any())->method('isVirtual')->will($this->returnValue(false));

        $shippingAddressMock =
            $this->getMock('\Magento\Sales\Model\Quote\Address', ['getCountryId', '__wakeup'], [], '', false);
        $shippingAddressMock->expects($this->once())->method('getCountryId')->will($this->returnValue(1));
        $quoteMock->expects($this->any())->method('getShippingAddress')->will($this->returnValue($shippingAddressMock));

        $quoteMock->expects($this->once())->method('setTotalsCollectedFlag')->will($this->returnSelf());
        $quoteMock->expects($this->once())->method('collectTotals')->will($this->returnSelf());

        $paymentMock = $this->getMock('Magento\Sales\Model\Quote\Payment', [], [], '', false);
        $paymentMock->expects($this->once())->method('getId')->will($this->returnValue($paymentId));

        $quoteMock->expects($this->once())->method('getPayment')->will($this->returnValue($paymentMock));

        $this->quoteLoaderMock->expects($this->once())
            ->method('load')
            ->with($cartId, $storeId)
            ->will($this->returnValue($quoteMock));

        $paymentMethodMock = $this->getMock('\Magento\Checkout\Service\V1\Data\Cart\PaymentMethod', [], [], '', false);

        $this->paymentMethodBuilderMock->expects($this->once())
            ->method('build')
            ->with($paymentMethodMock, $quoteMock)
            ->will($this->returnValue($paymentMock));

        $this->assertEquals($paymentId, $this->service->set($paymentMethodMock, $cartId));
    }
}
 