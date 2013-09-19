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
        $eventManager = $objectManager->get('Magento\Core\Model\Event\Manager');
        $coreData = $objectManager->get('Magento\Core\Helper\Data');
        $moduleList = $objectManager->get('Magento\Core\Model\ModuleListInterface');
        $paymentData = $objectManager->get('Magento\Payment\Helper\Data');
        $coreStoreConfig = $objectManager->get('Magento\Core\Model\Store\Config');

        /** @var $order \Magento\Sales\Model\Order */
        $order = $objectManager->create('Magento\Sales\Model\Order');
        $order->loadByIncrementId('100000001');
        $payment = $order->getPayment();

        /** @var \Magento\Paypal\Model\Payflowpro $instance */
        $instance = $this->getMock('Magento\Paypal\Model\Payflowpro', array('_postRequest'),
            array($eventManager, $coreData, $moduleList, $coreStoreConfig, $paymentData));

        $response = new \Magento\Object(array(
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
        $payment->void(new \Magento\Object);
        $order->save();

        $order = Mage::getModel('Magento\Sales\Model\Order');
        $order->loadByIncrementId('100000001');
        $this->assertFalse($order->canVoidPayment());
    }
}
