<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\RecurringProfile\Model\Method;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class RecurringPaymentSpecificationTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\RecurringProfile\Model\Method\RecurringPaymentSpecification */
    protected $recurringPaymentSpecification;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Payment\Model\Config|\PHPUnit_Framework_MockObject_MockObject */
    protected $configMock;

    protected function setUp()
    {
        $this->configMock = $this->getMock('Magento\Payment\Model\Config', [], [], '', false);

        $this->objectManagerHelper = new ObjectManagerHelper($this);
    }

    public function testIsSatisfiedBy()
    {
        $paymentMethodCode = 'test';
        $this->configMock->expects($this->once())
            ->method('getMethodsInfo')
            ->will($this->returnValue([
                $paymentMethodCode => [
                    \Magento\RecurringProfile\Model\Method\RecurringPaymentSpecification::CONFIG_KEY => 1
                ]
            ]));

        $this->recurringPaymentSpecification = $this->objectManagerHelper->getObject(
            'Magento\RecurringProfile\Model\Method\RecurringPaymentSpecification',
            [
                'paymentConfig' => $this->configMock
            ]
        );

        $this->assertTrue($this->recurringPaymentSpecification->isSatisfiedBy($paymentMethodCode));
    }
}
