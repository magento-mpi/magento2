<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Paypal_Model_VoidTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Paypal/_files/order_payflowpro.php
     * @magentoConfigFixture current_store payment/verisign/active 1
     */
    public function testPayflowProVoid()
    {
        /** @var $order Magento_Sales_Model_Order */
        $order = Mage::getModel('Magento_Sales_Model_Order');
        $order->loadByIncrementId('100000001');
        $payment = $order->getPayment();
        $storeConfig = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_Store_Config');
        $instance = $this->getMock('Magento_Paypal_Model_Payflowpro',
            array('_postRequest'),
            array($storeConfig, $this->getMock('Magento_Core_Model_ModuleListInterface'))
        );
        $response = new Magento_Object(array(
            'result' => '0',
            'pnref' => 'V19A3D27B61E',
            'respmsg' => 'Approved',
            'authcode' => '510PNI',
            'hostcode' => 'A',
            'request_id' => 'f930d3dc6824c1f7230c5529dc37ae5e',
            'result_code' => '0'
        ));

        $instance->expects($this->any())
            ->method('_postRequest')
            ->will($this->returnValue($response));

        $payment->setMethodInstance($instance);
        $payment->void(new Magento_Object);
        $order->save();
        $order = Mage::getModel('Magento_Sales_Model_Order');
        $order->loadByIncrementId('100000001');
        $this->assertFalse($order->canVoidPayment());
    }
}

