<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Paypal\Model\Ipn
 */
namespace Magento\Paypal\Model;

use Magento\Sales\Model\Order;

class IpnTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Paypal\Model\Ipn
     */
    protected $_ipn;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_orderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_paypalInfo;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $curlFactory;

    protected function setUp()
    {
        $methods = [
            'create',
            'loadByIncrementId',
            'canFetchPaymentReviewUpdate',
            'getId',
            'getPayment',
            'getMethod',
            'getStoreId',
            'registerPaymentReviewAction',
            'getAdditionalInformation',
            'getEmailSent',
            'save',
            'getState',
        ];
        $this->_orderMock = $this->getMock('Magento\Sales\Model\OrderFactory', $methods, [], '', false);
        $this->_orderMock->expects($this->any())->method('create')->will($this->returnSelf());
        $this->_orderMock->expects($this->any())->method('loadByIncrementId')->will($this->returnSelf());
        $this->_orderMock->expects($this->any())->method('getId')->will($this->returnSelf());
        $this->_orderMock->expects($this->any())->method('getMethod')->will($this->returnSelf());
        $this->_orderMock->expects($this->any())->method('getStoreId')->will($this->returnSelf());
        $this->_orderMock->expects($this->any())->method('getEmailSent')->will($this->returnValue(true));

        $this->configFactory = $this->getMock(
            'Magento\Paypal\Model\ConfigFactory',
            ['create', 'isMethodActive', 'isMethodAvailable', 'getConfigValue', 'getPaypalUrl'],
            [],
            '',
            false
        );
        $this->configFactory->expects($this->any())->method('create')->will($this->returnSelf());
        $this->configFactory->expects($this->any())->method('isMethodActive')->will($this->returnValue(true));
        $this->configFactory->expects($this->any())->method('isMethodAvailable')->will($this->returnValue(true));
        $this->configFactory->expects($this->any())->method('getConfigValue')->will($this->returnValue(null));
        $this->configFactory->expects($this->any())->method('getPaypalUrl')
            ->will($this->returnValue('http://paypal_url'));

        $this->curlFactory = $this->getMock(
            'Magento\Framework\HTTP\Adapter\CurlFactory',
            ['create', 'setConfig', 'write', 'read'],
            [],
            '',
            false
        );
        $this->curlFactory->expects($this->any())->method('create')->will($this->returnSelf());
        $this->curlFactory->expects($this->any())->method('setConfig')->will($this->returnSelf());
        $this->curlFactory->expects($this->any())->method('write')->will($this->returnSelf());
        $this->curlFactory->expects($this->any())->method('read')->will($this->returnValue(
            '
                VERIFIED'
        ));
        $this->_paypalInfo = $this->getMock(
            'Magento\Paypal\Model\Info',
            ['importToPayment', 'getMethod', 'getAdditionalInformation'],
            [],
            '',
            false
        );
        $this->_paypalInfo->expects($this->any())->method('getMethod')->will($this->returnValue('some_method'));
        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_ipn = $objectHelper->getObject('Magento\Paypal\Model\Ipn',
            [
                'configFactory' => $this->configFactory,
                'logAdapterFactory' => $this->getMock('Magento\Framework\Logger\AdapterFactory', [], [], '', false),
                'curlFactory' => $this->curlFactory,
                'orderFactory' => $this->_orderMock,
                'paypalInfo' => $this->_paypalInfo,
                'data' => ['payment_status' => 'Pending', 'pending_reason' => 'authorization']
            ]
        );
    }

    public function testLegacyRegisterPaymentAuthorization()
    {
        $this->_orderMock->expects($this->any())->method('canFetchPaymentReviewUpdate')->will(
            $this->returnValue(false)
        );
        $methods = [
            'setPreparedMessage',
            '__wakeup',
            'setTransactionId',
            'setParentTransactionId',
            'setIsTransactionClosed',
            'registerAuthorizationNotification',
        ];
        $payment = $this->getMock('Magento\Sales\Model\Order\Payment', $methods, [], '', false);
        $payment->expects($this->any())->method('setPreparedMessage')->will($this->returnSelf());
        $payment->expects($this->any())->method('setTransactionId')->will($this->returnSelf());
        $payment->expects($this->any())->method('setParentTransactionId')->will($this->returnSelf());
        $payment->expects($this->any())->method('setIsTransactionClosed')->will($this->returnSelf());
        $this->_orderMock->expects($this->any())->method('getPayment')->will($this->returnValue($payment));
        $this->_orderMock->expects($this->any())->method('getAdditionalInformation')->will($this->returnValue([]));

        $this->_paypalInfo->expects($this->once())->method('importToPayment');
        $this->_ipn->processIpnRequest();
    }

    public function testPaymentReviewRegisterPaymentAuthorization()
    {
        $this->_orderMock->expects($this->any())->method('getPayment')->will($this->returnSelf());
        $this->_orderMock->expects($this->any())->method('canFetchPaymentReviewUpdate')->will($this->returnValue(true));
        $this->_orderMock->expects($this->once())->method('registerPaymentReviewAction')->with(
            'update',
            true
        )->will($this->returnSelf());
        $this->_ipn->processIpnRequest();
    }

    public function testPaymentReviewRegisterPaymentFraud()
    {
        $paymentMock = $this->getMock(
            '\Magento\Sales\Model\Order\Payment',
            ['getAdditionalInformation', '__wakeup', 'registerCaptureNotification'],
            [],
            '',
            false
        );
        $paymentMock->expects($this->any())
            ->method('getAdditionalInformation')
            ->will($this->returnValue([]));
        $paymentMock->expects($this->any())
            ->method('registerCaptureNotification')
            ->will($this->returnValue(true));
        $this->_orderMock->expects($this->any())->method('getPayment')->will($this->returnValue($paymentMock));
        $this->_orderMock->expects($this->any())->method('canFetchPaymentReviewUpdate')->will($this->returnValue(true));
        $this->_orderMock->expects($this->once())->method('getState')->will(
            $this->returnValue(Order::STATE_PENDING_PAYMENT)
        );
        $this->_paypalInfo->expects($this->once())
            ->method('importToPayment')
            ->with(
                [
                    'payment_status' => 'pending',
                    'pending_reason' => 'fraud',
                    'collected_fraud_filters' => ['Maximum Transaction Amount'],
                ],
                $paymentMock
            );
        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_ipn = $objectHelper->getObject('Magento\Paypal\Model\Ipn',
            [
                'configFactory' => $this->configFactory,
                'logAdapterFactory' => $this->getMock('Magento\Framework\Logger\AdapterFactory', [], [], '', false),
                'curlFactory' => $this->curlFactory,
                'orderFactory' => $this->_orderMock,
                'paypalInfo' => $this->_paypalInfo,
                'data' => [
                    'payment_status' => 'Pending',
                    'pending_reason' => 'fraud',
                    'fraud_management_pending_filters_1' => 'Maximum Transaction Amount',
                ]
            ]
        );
        $this->_ipn->processIpnRequest();
        $this->assertEquals('IPN "Pending"', $paymentMock->getPreparedMessage());
    }
}
