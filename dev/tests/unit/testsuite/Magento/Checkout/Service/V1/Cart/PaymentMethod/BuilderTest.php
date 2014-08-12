<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Cart\PaymentMethod;

class BuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->builder = $this->objectManager->getObject(
            '\Magento\Checkout\Service\V1\Data\Cart\PaymentMethod\Builder'
        );
    }

    public function testBuildPaymentObject()
    {
        $paymentData = [
            'method' => 'checkmo',
            'payment_details' => 'paymentDetailsTest'
        ];

        $paymentMethodMock = $this->getMock('\Magento\Checkout\Service\V1\Data\Cart\PaymentMethod', [], [], '', false);
        $paymentMethodMock->expects($this->once())->method('__toArray')->will($this->returnValue($paymentData));
        $paymentMethodMock->expects($this->once())
            ->method('getPaymentDetails')
            ->will($this->returnValue(['paymentDetailsTest']));

        $paymentMock = $this->getMock('\Magento\Sales\Model\Quote\Payment', [], [], '', false);
        $paymentMock->expects($this->once())
            ->method('importData')
            ->with(['method' => 'checkmo'])
            ->will($this->returnSelf());

        $quoteMock = $this->getMock('\Magento\Sales\Model\Quote', [], [], '', false);
        $quoteMock->expects($this->once())->method('getPayment')->will($this->returnValue($paymentMock));

        $this->assertEquals($paymentMock, $this->builder->build($paymentMethodMock, $quoteMock));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage The requested Payment Method is not available.
     */
    public function testBuildPaymentObjectThrowsExceptionIfPaymentMethodNotAvailable()
    {
        $paymentData = [
            'method' => 'notAvailableMethod',
            'payment_details' => 'paymentDetailsTest'
        ];

        $paymentMethodMock = $this->getMock('\Magento\Checkout\Service\V1\Data\Cart\PaymentMethod', [], [], '', false);
        $paymentMethodMock->expects($this->once())->method('__toArray')->will($this->returnValue($paymentData));
        $paymentMethodMock->expects($this->once())
            ->method('getPaymentDetails')
            ->will($this->returnValue(['paymentDetailsTest']));

        $paymentMock = $this->getMock('\Magento\Sales\Model\Quote\Payment', [], [], '', false);
        $paymentMock->expects($this->once())
            ->method('importData')
            ->with(['method' => 'notAvailableMethod'])
            ->will($this->throwException(
                new \Magento\Framework\Exception\LocalizedException('The requested Payment Method is not available.'))
            );

        $quoteMock = $this->getMock('\Magento\Sales\Model\Quote', [], [], '', false);
        $quoteMock->expects($this->once())->method('getPayment')->will($this->returnValue($paymentMock));

        $this->assertEquals($paymentMock, $this->builder->build($paymentMethodMock, $quoteMock));
    }
}
 