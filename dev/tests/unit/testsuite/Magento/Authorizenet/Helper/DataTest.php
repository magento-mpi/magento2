<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Authorizenet\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Last 4 digit of cc
     */
    const LAST4 = 1111;

    /**
     * Transaction ID
     */
    const TRID = '2217041665';

    /**
     * @var Data
     */
    protected $_model;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $helper->getObject('Magento\Authorizenet\Helper\Data');
    }

    /**
     * @param $type
     * @param $amount
     * @param $exception
     * @param $additionalMessage
     * @param $expected
     * @dataProvider getMessagesParamDataProvider
     */
    public function testGetTransactionMessage($type, $amount, $exception, $additionalMessage, $expected)
    {
        $currency = $this->getMock('Magento\Directory\Model\Currency', ['formatTxt', '__wakeup'], [], '', false);
        $currency->expects($this->any())
            ->method('formatTxt')
            ->will($this->returnValue($amount));
        $order = $this->getMock('Magento\Sales\Model\Order', ['getBaseCurrency', '__wakeup'], [], '', false);
        $order->expects($this->any())
            ->method('getBaseCurrency')
            ->will($this->returnValue($currency));
        $payment = $this->getMock('Magento\Payment\Model\Info', ['getOrder', '__wakeup'], [], '', false);
        $payment->expects($this->any())
            ->method('getOrder')
            ->will($this->returnValue($order));
        $card = new \Magento\Framework\Object(['cc_last_4' => self::LAST4]);
        $message = $this->_model->getTransactionMessage(
            $payment,
            $type,
            self::TRID,
            $card,
            $amount,
            $exception,
            $additionalMessage
        );

        $this->assertEquals($expected, $message);
    }

    /**
     * @return array
     */
    public function getMessagesParamDataProvider()
    {
        $amount = 12.30;
        $additionalMessage = 'Addition message.';
        return [
            [
                'AUTH_ONLY',
                $amount,
                false,
                $additionalMessage,
                'Credit Card: xxxx-' . self::LAST4 . ' amount 12.3 authorize - successful. '
                . 'Authorize.Net Transaction ID ' . self::TRID . '. Addition message.',
            ],
            [
                'AUTH_CAPTURE',
                $amount,
                'some exception',
                false,
                'Credit Card: xxxx-' . self::LAST4 . ' amount 12.3 authorize and capture - failed. '
                . 'Authorize.Net Transaction ID ' . self::TRID . '. some exception'
            ],
            [
                'CREDIT',
                false,
                false,
                $additionalMessage,
                'Credit Card: xxxx-' . self::LAST4 . ' refund - successful. '
                . 'Authorize.Net Transaction ID ' . self::TRID . '. Addition message.'
            ],
        ];
    }
}
