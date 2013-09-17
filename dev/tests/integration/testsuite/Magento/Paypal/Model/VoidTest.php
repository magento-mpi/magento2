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
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $eventManager = $objectManager->get('Magento_Core_Model_Event_Manager');
        $coreData = $objectManager->get('Magento_Core_Helper_Data');
        $moduleList = $objectManager->get('Magento_Core_Model_ModuleListInterface');
        $paymentData = $objectManager->get('Magento_Payment_Helper_Data');

        /** @var $order Magento_Sales_Model_Order */
        $order = $objectManager->create('Magento_Sales_Model_Order');
        $order->loadByIncrementId('100000001');
        $payment = $order->getPayment();

        $instance = $this->getMock('Magento_Paypal_Model_Payflowpro', array('_postRequest'),
            array($eventManager, $coreData, $moduleList, $paymentData));

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
