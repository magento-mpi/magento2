<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model\Convert;

use Magento\TestFramework\Helper\ObjectManager;
use Magento\Sales\Model\Quote\Address;

/**
 * Test class for \Magento\Sales\Model\Order
 */
class QuoteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Convert\Quote
     */
    protected $quote;

    protected function setUp()
    {
        $orderPaymentMock = $this->getMock(
            'Magento\Sales\Model\Order\Payment',
            array('setStoreId', 'setCustomerPaymentId', '__wakeup'),
            array(),
            '',
            false
        );
        $orderPaymentMock->expects(
            $this->any()
        )->method(
            'setStoreId'
        )->will(
            $this->returnValue(
                $orderPaymentMock
            )
        );
        $orderPaymentMock->expects(
            $this->any()
        )->method(
            'setCustomerPaymentId'
        )->will(
            $this->returnValue(
                $orderPaymentMock
            )
        );
        $orderPaymentFactoryMock = $this->getMock(
            'Magento\Sales\Model\Order\PaymentFactory',
            array('create'),
            array(),
            '',
            false
        );
        $orderPaymentFactoryMock->expects($this->any())->method('create')->will($this->returnValue($orderPaymentMock));

        $objectCopyServiceMock = $this->getMock('Magento\Framework\Object\Copy', array(), array(), '', false);
        $objectManager = new ObjectManager($this);
        $this->quote = $objectManager->getObject(
            'Magento\Sales\Model\Convert\Quote',
            array(
                'orderPaymentFactory' => $orderPaymentFactoryMock,
                'objectCopyService' => $objectCopyServiceMock
            )
        );
    }

    public function testPaymentToOrderPayment()
    {
        $payment = $this->getMock('Magento\Sales\Model\Quote\Payment', array(), array(), '', false);
        $title = new \Magento\Framework\Object(['title' => 'some title']);
        $payment->expects($this->any())->method('getMethodInstance')->will($this->returnValue($title));
        $this->assertEquals(
            ['method_title' => 'some title'],
            $this->quote->paymentToOrderPayment($payment)->getAdditionalInformation()
        );
    }
}
