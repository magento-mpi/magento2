<?php
/**
 * Created by JetBrains PhpStorm.
 * User: User
 * Date: 16.11.12
 * Time: 13:42
 * To change this template use File | Settings | File Templates.
 */


class Mage_Paypal_Model_VoidTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Mage/Paypal/_files/order_payflowpro.php
     * @magentoConfigFixture current_store payment/verisign/active 1
     */
    public function testPayflowProVoid()
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = Mage::getModel('Mage_Sales_Model_Order');
        $order->loadByIncrementId('100000001');
        $payment = $order->getPayment();
        $instance = $this->getMock('Mage_Paypal_Model_Payflowpro', array('_postRequest'));
        $response = array(
                'result' => '0',
                'pnref' => 'V19A3D27B61E',
                'respmsg' => 'Approved',
                'authcode' => '510PNI',
                'hostcode' => 'A',
                'request_id' => 'f930d3dc6824c1f7230c5529dc37ae5e',
                'result_code' => '0'
            );

        $instance->expects($this->any())
            ->method('_postRequest')
            ->will($this->returnValue($response));

        $payment->setMethodInstance($instance);
        $payment->void(new Varien_Object);
        $order->save();
        $order = Mage::getModel('Mage_Sales_Model_Order');
        $order->loadByIncrementId('100000001');
        $this->assertFalse($order->canVoidPayment());
    }
}

