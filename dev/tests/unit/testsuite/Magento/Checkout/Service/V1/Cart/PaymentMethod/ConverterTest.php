<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Cart\PaymentMethod;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Converter
     */
    protected $converter;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $paymentMethodBuilderMock;

    protected function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->paymentMethodBuilderMock = $this->getMock(
            '\Magento\Checkout\Service\V1\Data\Cart\PaymentMethodBuilder', [], [], '', false
        );

        $this->converter = $this->objectManager->getObject(
            '\Magento\Checkout\Service\V1\Data\Cart\PaymentMethod\Converter',
            [
                'builder' => $this->paymentMethodBuilderMock,
            ]
        );
    }

    public function testConvertQuotePaymentObjectToPaymentDataObject()
    {
        $paymentMock = $this->getMock('\Magento\Sales\Model\Quote\Payment',
            [
                'getMethod', 'getPoNumber', 'getCcCid', 'getCcOwner', 'getCcNumber',
                'getCcType', 'getCcExpYear', 'getCcExpMonth', 'getAdditionalData', '__wakeup'
            ],
            [], '', false
        );
        $paymentMock->expects($this->once())->method('getMethod')->will($this->returnValue('checkmo'));
        $paymentMock->expects($this->once())->method('getPoNumber')->will($this->returnValue(100));
        $paymentMock->expects($this->once())->method('getCcCid')->will($this->returnValue(666));
        $paymentMock->expects($this->once())->method('getCcOwner')->will($this->returnValue('tester'));
        $paymentMock->expects($this->once())->method('getCcNumber')->will($this->returnValue(100200300));
        $paymentMock->expects($this->once())->method('getCcType')->will($this->returnValue('visa'));
        $paymentMock->expects($this->once())->method('getCcExpYear')->will($this->returnValue(2014));
        $paymentMock->expects($this->once())->method('getCcExpMonth')->will($this->returnValue(10));
        $paymentMock->expects($this->once())->method('getAdditionalData')->will($this->returnValue('test'));

        $data = [
            \Magento\Checkout\Service\V1\Data\Cart\PaymentMethod::METHOD => 'checkmo',
            \Magento\Checkout\Service\V1\Data\Cart\PaymentMethod::PO_NUMBER => 100,
            \Magento\Checkout\Service\V1\Data\Cart\PaymentMethod::CC_CID => 666,
            \Magento\Checkout\Service\V1\Data\Cart\PaymentMethod::CC_OWNER => 'tester',
            \Magento\Checkout\Service\V1\Data\Cart\PaymentMethod::CC_NUMBER => 100200300,
            \Magento\Checkout\Service\V1\Data\Cart\PaymentMethod::CC_TYPE => 'visa',
            \Magento\Checkout\Service\V1\Data\Cart\PaymentMethod::CC_EXP_YEAR => 2014,
            \Magento\Checkout\Service\V1\Data\Cart\PaymentMethod::CC_EXP_MONTH => 10,
            \Magento\Checkout\Service\V1\Data\Cart\PaymentMethod::PAYMENT_DETAILS => 'test',
        ];

        $this->paymentMethodBuilderMock->expects($this->once())
            ->method('populateWithArray')
            ->with($data)
            ->will($this->returnSelf());

        $paymentMethodMock = $this->getMock('\Magento\Checkout\Service\V1\Data\PaymentMethod', [], [], '', false);

        $this->paymentMethodBuilderMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($paymentMethodMock));

        $this->assertEquals($paymentMethodMock, $this->converter->toDataObject($paymentMock));
    }
}
 