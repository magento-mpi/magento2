<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Paypal\Model\Payflowpro
 */
namespace Magento\Paypal\Model;

class PayflowproTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Payflowpro
     */
    protected $_model;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_helper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configFactory;

    protected function setUp()
    {
        $this->_configFactory = $this->getMock(
            'Magento\Paypal\Model\ConfigFactory',
            ['create', 'getBuildNotationCode'],
            [],
            '',
            false
        );
        $client = $this->getMock(
            'Magento\Framework\HTTP\ZendClient',
            [
                'setUri',
                'setConfig',
                'setMethod',
                'setParameterPost',
                'setHeaders',
                'setUrlEncodeBody',
                'request',
                'getBody'
            ],
            [],
            '',
            false
        );
        $client->expects($this->any())->method('create')->will($this->returnSelf());
        $client->expects($this->any())->method('setUri')->will($this->returnSelf());
        $client->expects($this->any())->method('setConfig')->will($this->returnSelf());
        $client->expects($this->any())->method('setMethod')->will($this->returnSelf());
        $client->expects($this->any())->method('setParameterPost')->will($this->returnSelf());
        $client->expects($this->any())->method('setHeaders')->will($this->returnSelf());
        $client->expects($this->any())->method('setUrlEncodeBody')->will($this->returnSelf());
        $client->expects($this->any())->method('request')->will($this->returnSelf());
        $client->expects($this->any())->method('getBody')->will($this->returnValue('RESULT name=value&name2=value2'));
        $clientFactory = $this->getMock('Magento\Framework\HTTP\ZendClientFactory', ['create'], [], '', false);
        $clientFactory->expects($this->any())->method('create')->will($this->returnValue($client));

        $this->_helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $this->_helper->getObject(
            'Magento\Paypal\Model\Payflowpro',
            [
                'configFactory' => $this->_configFactory,
                'httpClientFactory' => $clientFactory
            ]
        );
    }

    /**
     * @param mixed $amountPaid
     * @param string $paymentType
     * @param bool $expected
     * @dataProvider canVoidDataProvider
     */
    public function testCanVoid($amountPaid, $paymentType, $expected)
    {
        $payment = $this->_helper->getObject($paymentType);
        $payment->setAmountPaid($amountPaid);
        $this->assertEquals($expected, $this->_model->canVoid($payment));
    }

    /**
     * @return array
     */
    public function canVoidDataProvider()
    {
        return [
            [0, 'Magento\Sales\Model\Order\Invoice', false],
            [0, 'Magento\Sales\Model\Order\Creditmemo', false],
            [12.1, 'Magento\Sales\Model\Order\Payment', false],
            [0, 'Magento\Sales\Model\Order\Payment', true],
            [null, 'Magento\Sales\Model\Order\Payment', true]
        ];
    }

    public function testCanCapturePartial()
    {
        $this->assertTrue($this->_model->canCapturePartial());
    }

    public function testCanRefundPartialPerInvoice()
    {
        $this->assertTrue($this->_model->canRefundPartialPerInvoice());
    }

    /**
     * test for _buildBasicRequest (BDCODE)
     */
    public function testFetchTransactionInfoForBN()
    {
        $this->_configFactory->expects($this->once())->method('create')->will($this->returnSelf());
        $this->_configFactory->expects($this->once())->method('getBuildNotationCode')
            ->will($this->returnValue('BNCODE'));
        $payment = $this->getMock('Magento\Payment\Model\Info', ['setTransactionId', '__wakeup'], [], '', false);
        $payment->expects($this->once())->method('setTransactionId')->will($this->returnSelf());
        $this->_model->fetchTransactionInfo($payment, 'AD49G8N825');
    }
}
