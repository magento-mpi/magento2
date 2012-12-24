<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test API getting orders list method
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 * @magentoApiDataFixture Api/SalesOrder/_fixture/order.php
 */
class Api_SalesOrder_StatusTest extends Magento_Test_Webservice
{
    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public static function tearDownAfterClass()
    {
        Magento_Test_Webservice::deleteFixture('customer', true);
        Magento_Test_Webservice::deleteFixture('product_virtual', true);
        Magento_Test_Webservice::deleteFixture('quote', true);
        Magento_Test_Webservice::deleteFixture('order', true);

        parent::tearDownAfterClass();
    }

    /**
     * Test for sales_order.cancel when order is in 'pending' status
     *
     * @return void
     */
    public function testCancelPendingOrder()
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = Magento_Test_Webservice::getFixture('order');

        $order->setStatus('pending')
           ->save();

        $soapResult = $this->getWebService()->call('sales_order.cancel', array(
            'orderIncrementId' => $order->getIncrementId()
        ));

        $this->assertTrue((bool) $soapResult, 'API call result in not TRUE');

        // reload order to obtain new status
        $order->load($order->getId());

        $this->assertEquals(Mage_Sales_Model_Order::STATE_CANCELED, $order->getStatus(), 'Status is not CANCELED');
    }

    /**
     * Test for sales_order.hold when order is in 'processing' status
     *
     * @return void
     */
    public function testHoldProcessingOrder()
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = Magento_Test_Webservice::getFixture('order');

        $order->setState(Mage_Sales_Model_Order::STATE_NEW, 'pending')
           ->save();

        $soapResult = $this->getWebService()->call('sales_order.hold', array(
            'orderIncrementId' => $order->getIncrementId()
        ));

        $this->assertTrue((bool) $soapResult, 'API call result in not TRUE');

        // reload order to obtain new status
        $order->load($order->getId());

        $this->assertEquals(Mage_Sales_Model_Order::STATE_HOLDED, $order->getStatus(), 'Status is not HOLDED');
    }
}
