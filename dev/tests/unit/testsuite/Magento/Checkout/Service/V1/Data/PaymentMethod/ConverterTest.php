<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Data\PaymentMethod;

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
            '\Magento\Checkout\Service\V1\Data\PaymentMethodBuilder', [], [], '', false
        );

        $this->converter = $this->objectManager->getObject(
            '\Magento\Checkout\Service\V1\Data\PaymentMethod\Converter',
            [
                'builder' => $this->paymentMethodBuilderMock,
            ]
        );
    }

    public function testConvertQuotePaymentObjectToPaymentDataObject()
    {
        $paypalMethodMock = $this->getMock('\Magento\PbridgePaypal\Model\Payment\Method\Paypal', [], [], '', false);
        $paypalMethodMock->expects($this->once())->method('getCode')->will($this->returnValue('paymentCode'));
        $paypalMethodMock->expects($this->once())->method('getTitle')->will($this->returnValue('paymentTitle'));

        $data = [
            \Magento\Checkout\Service\V1\Data\PaymentMethod::TITLE => 'paymentTitle',
            \Magento\Checkout\Service\V1\Data\PaymentMethod::CODE => 'paymentCode'
        ];

        $this->paymentMethodBuilderMock->expects($this->once())
            ->method('populateWithArray')
            ->with($data)
            ->will($this->returnSelf());

        $paymentMethodMock = $this->getMock('\Magento\Checkout\Service\V1\Data\PaymentMethod', [], [], '', false);

        $this->paymentMethodBuilderMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($paymentMethodMock));

        $this->assertEquals($paymentMethodMock, $this->converter->toDataObject($paypalMethodMock));
    }
}
 