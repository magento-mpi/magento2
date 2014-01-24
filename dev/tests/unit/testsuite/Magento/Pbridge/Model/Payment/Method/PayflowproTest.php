<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pbridge\Model\Payment\Method;

class PayflowproTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Payflowpro
     */
    protected $_model;

    protected function setUp()
    {
        $pbridgeMethod = $this->getMock('Magento\Pbridge\Model\Payment\Method\Pbridge', array(
            'capture',
            'authorize',
            'refund'
        ), array(), '', false);
        $paymentData = $this->getMock('Magento\Payment\Helper\Data', array('getMethodInstance'), array(), '', false);
        $paymentData->expects($this->once())
            ->method('getMethodInstance')
            ->will($this->returnValue($pbridgeMethod));
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $helper->getObject('Magento\Pbridge\Model\Payment\Method\Payflowpro', array(
            'paymentData' => $paymentData
        ));
    }

    /**
     * @param float $amount
     * @param float $amountAuthorized
     * @param float|null $amountPaid
     * @dataProvider captureDataProvider
     */
    public function testCapture($amount, $amountAuthorized, $amountPaid)
    {
        $infoInstance = $this->getMock('Magento\Payment\Model\Info', array('__wakeup'), array(), '', false);
        $infoInstance->setAmountPaid($amountPaid);
        $infoInstance->setAmountAuthorized($amountAuthorized);
        $this->_model->setData('info_instance', $infoInstance);
        $payment = new \Magento\Object();
        $this->_model->capture($payment, $amount);
        // check fix for partial refunds in Payflow Pro
        $this->assertEquals(!isset($amountPaid), $payment->getFirstCaptureFlag());
        $this->assertEquals($amount == $amountAuthorized, $payment->getShouldCloseParentTransaction());
    }

    public function captureDataProvider()
    {
        return array(
            array(3.2, 5, 1),
            array(3.2, 5, null),
            array(3, 3, null),
            array(2.23, 2.23, 1),
        );
    }

    /**
     * @param bool $canRefund
     * @dataProvider refundDataProvider
     */
    public function testRefund($canRefund)
    {
        $invoice = $this->getMock('Magento\Sales\Model\Order\Invoice', array(
            'canRefund',
            '__wakeup'
        ), array(), '', false);
        $invoice->expects($this->once())->method('canRefund')->will($this->returnValue($canRefund));
        $payment = new \Magento\Object(array(
            'creditmemo' => new \Magento\Object(array('invoice' => $invoice))
        ));
        $this->_model->refund($payment, 'any');
        // check fix for partial refunds in Payflow Pro
        $this->assertEquals(!$canRefund, $payment->getShouldCloseParentTransaction());
    }

    public function refundDataProvider()
    {
        return array(
            array(true),
            array(false),
        );
    }
}
