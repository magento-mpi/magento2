<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model\Order;

class PaymentTest extends \PHPUnit_Framework_TestCase
{
    public function testCancel()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var Payment $model */
        $payment = $this->getMock('Magento\Payment\Model\Method\AbstractMethod', array('canVoid'), array(), '', false);
        $paymentData = $this->getMock('Magento\Payment\Helper\Data', array('getMethodInstance'), array(), '', false);
        $paymentData->expects($this->once())->method('getMethodInstance')->will($this->returnValue($payment));
        $model = $helper->getObject('Magento\Sales\Model\Order\Payment', array('paymentData' => $paymentData));
        $model->setMethod('any');
        // check fix for partial refunds in Payflow Pro
        $payment->expects($this->once())
            ->method('canVoid')
            ->with(new \PHPUnit_Framework_Constraint_IsIdentical($model))
            ->will($this->returnValue(false));

        $model->cancel();
    }
}
