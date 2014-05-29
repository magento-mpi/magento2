<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PbridgePaypal\Model\Payment\Method;

class PayflowproTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Payflowpro
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_pbridgeMethod;

    protected function setUp()
    {
        $this->_pbridgeMethod = $this->getMock(
            'Magento\Pbridge\Model\Payment\Method\Pbridge',
            array('capture', 'authorize', 'refund'),
            array(),
            '',
            false
        );
        $paypal = $this->getMock(
            'Magento\PbridgePaypal\Model\Payment\Method\Paypal',
            array('getPbridgeMethodInstance'),
            array(),
            '',
            false
        );
        $paypal->expects($this->any())->method('getPbridgeMethodInstance')->will(
            $this->returnValue($this->_pbridgeMethod)
        );
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $helper->getObject(
            'Magento\PbridgePaypal\Model\Payment\Method\Payflowpro',
            array('paypal' => $paypal)
        );
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
        $payment = new \Magento\Framework\Object();
        $this->_model->capture($payment, $amount);
        // check fix for partial refunds in Payflow Pro
        $this->assertEquals(!isset($amountPaid), $payment->getFirstCaptureFlag());
        $this->assertEquals($amount == $amountAuthorized, $payment->getShouldCloseParentTransaction());
    }

    public function captureDataProvider()
    {
        return array(array(3.2, 5, 1), array(3.2, 5, null), array(3, 3, null), array(2.23, 2.23, 1));
    }

    /**
     * @param bool $canRefund
     * @dataProvider refundDataProvider
     */
    public function testRefund($canRefund)
    {
        $invoice = $this->getMock(
            'Magento\Sales\Model\Order\Invoice',
            array('canRefund', '__wakeup'),
            array(),
            '',
            false
        );
        $invoice->expects($this->once())->method('canRefund')->will($this->returnValue($canRefund));
        $payment = new \Magento\Framework\Object(
            array('creditmemo' => new \Magento\Framework\Object(array('invoice' => $invoice)))
        );
        $this->_model->refund($payment, 'any');
        // check fix for partial refunds in Payflow Pro
        $this->assertEquals(!$canRefund, $payment->getShouldCloseParentTransaction());
    }

    public function refundDataProvider()
    {
        return array(array(true), array(false));
    }

    public function testAuthorizeRespMsg()
    {
        $payment = new \Magento\Framework\Object();
        $payment->setPreparedMessage('first');
        $amount = 12.5;
        $this->_pbridgeMethod->expects($this->once())->method('authorize')->with($payment, $amount)->will(
            $this->returnValue(['respmsg' => 'response message', 'postfpsmsg' => 'something went wrong'])
        );
        $this->_model->authorize($payment, $amount);
        $this->assertEquals('first response message: something went wrong.', $payment->getPreparedMessage());
    }
}
