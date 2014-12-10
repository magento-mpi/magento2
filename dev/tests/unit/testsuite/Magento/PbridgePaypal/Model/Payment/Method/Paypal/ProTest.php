<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\PbridgePaypal\Model\Payment\Method\Paypal;

class ProTest extends \PHPUnit_Framework_TestCase
{
    /**#@+
     * Payment review actions
     */
    const PAYMENT_REVIEW_ACCEPT = 'accept';
    const PAYMENT_REVIEW_DENY = 'deny';
    /**#@-*/

    /**
     * Test transaction id
     */
    const TRANSACTION_ID = 'AD3903FR972F';

    /**
     * @var Pro
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_pbInstance;

    protected function setUp()
    {
        $info = $this->getMock(
            'Magento\Paypal\Model\InfoFactory',
            ['create', 'importToPayment', 'isPaymentReviewRequired', 'isPaymentSuccessful', 'isPaymentFailed'],
            [],
            '',
            false
        );
        $info->expects($this->any())->method('create')->will($this->returnSelf());
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $helper->getObject(
            'Magento\PbridgePaypal\Model\Payment\Method\Paypal\Pro',
            ['infoFactory' => $info]
        );
        $this->_pbInstance = $this->getMock(
            'Magento\Paypal\Model\Pro',
            ['fetchTransactionInfo', 'acceptPayment', 'denyPayment'],
            [],
            '',
            false
        );
        $this->_pbInstance->expects($this->any())->method('acceptPayment')->will($this->returnValue(['key' => 'val']));
        $this->_pbInstance->expects($this->any())->method('denyPayment')->will($this->returnValue(['key1' => 'val1']));
        $ppdirect = $this->getMock(
            'Magento\PbridgePaypal\Model\Payment\Method\PaypalDirect',
            ['getPbridgeMethodInstance'],
            [],
            '',
            false
        );
        $ppdirect->expects($this->once())->method('getPbridgeMethodInstance')->will(
            $this->returnValue($this->_pbInstance)
        );
        $this->_model->setPaymentMethod($ppdirect);
    }

    /**
     * @dataProvider reviewPaymentActionsDataProvider
     */
    public function testReviewPayment($action, $expected)
    {
        $infoInstance = $this->getMock('Magento\Payment\Model\Info', ['__wakeup'], [], '', false);
        $actual = $this->_model->reviewPayment($infoInstance, $action);
        $this->assertEquals($expected, $actual);
    }

    public function reviewPaymentActionsDataProvider()
    {
        return [
            [self::PAYMENT_REVIEW_ACCEPT, true],
            [self::PAYMENT_REVIEW_DENY, true],
            ['something', false]
        ];
    }

    /**
     * @dataProvider fetchTransactionInfoResponseDataProvider
     */
    public function testFetchTransactionInfo($response, $expected)
    {
        $this->_pbInstance->expects($this->once())->method('fetchTransactionInfo')->will(
            $this->returnValue($response)
        );
        $infoInstance = $this->getMock('Magento\Payment\Model\Info', ['__wakeup'], [], '', false);
        $actual = $this->_model->fetchTransactionInfo($infoInstance, self::TRANSACTION_ID);
        $this->assertEquals($expected, $actual);
    }

    public function fetchTransactionInfoResponseDataProvider()
    {
        return [
            [['raw_success_response_data' => ['response' => 'something']], ['response' => 'something']],
            [['name' => 'something'], []]
        ];
    }
}
