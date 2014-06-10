<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paypal\Model;

use Magento\Sales\Model\Order\Payment\Transaction;

class PayflowExpressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Paypal\Model\PayflowExpress
     */
    protected $_model;

    /**
     * Payflow pro transaction key
     */
    const TRANSPORT_PAYFLOW_TXN_ID = 'Payflow pro transaction key';

    public function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $proFactory = $this->getMockBuilder(
            'Magento\Paypal\Model\ProFactory'
        )->disableOriginalConstructor()->setMethods(['create'])->getMock();
        $paypalPro = $this->getMockBuilder(
            'Magento\Paypal\Model\Pro'
        )->disableOriginalConstructor()->setMethods([])->getMock();

        $proFactory->expects($this->once())->method('create')->will($this->returnValue($paypalPro));

        $this->_model = $objectManager->getObject('Magento\Paypal\Model\PayflowExpress', ['proFactory' => $proFactory]);
    }

    public function testCanRefundCaptureNotExist()
    {
        $paymentInfo = $this->_getPreparedPaymentInfo();

        $paymentInfo->expects($this->once())->method('lookupTransaction')->with('', Transaction::TYPE_CAPTURE)->will(
            $this->returnValue(false)
        );
        $this->assertFalse($this->_model->canRefund());
    }

    public function testCanRefundCaptureExistNoAdditionalInfo()
    {
        $paymentInfo = $this->_getPreparedPaymentInfo();
        $captureTransaction = $this->_getCaptureTransaction();
        $captureTransaction->expects($this->once())->method('getAdditionalInformation')->with(
            Payflow\Pro::TRANSPORT_PAYFLOW_TXN_ID
        )->will($this->returnValue(null));
        $paymentInfo->expects($this->once())->method('lookupTransaction')->with('', Transaction::TYPE_CAPTURE)->will(
            $this->returnValue($captureTransaction)
        );
        $this->assertFalse($this->_model->canRefund());
    }

    public function testCanRefundCaptureExistValid()
    {
        $paymentInfo = $this->_getPreparedPaymentInfo();
        $captureTransaction = $this->_getCaptureTransaction();
        $captureTransaction->expects($this->once())->method('getAdditionalInformation')->with(
            Payflow\Pro::TRANSPORT_PAYFLOW_TXN_ID
        )->will($this->returnValue(self::TRANSPORT_PAYFLOW_TXN_ID));
        $paymentInfo->expects($this->once())->method('lookupTransaction')->with('', Transaction::TYPE_CAPTURE)->will(
            $this->returnValue($captureTransaction)
        );
        $this->assertTrue($this->_model->canRefund());
    }

    /**
     * Prepares payment info mock and adds it to the model
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getPreparedPaymentInfo()
    {
        $paymentInfo = $this->getMockBuilder(
            'Magento\Sales\Model\Order\Payment'
        )->disableOriginalConstructor()->setMethods([])->getMock();
        $this->_model->setData('info_instance', $paymentInfo);
        return $paymentInfo;
    }

    /**
     * Prepares capture transaction
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getCaptureTransaction()
    {
        return $this->getMockBuilder(
            'Magento\Sales\Model\Order\Payment\Transaction'
        )->disableOriginalConstructor()->setMethods([])->getMock();
    }
}
