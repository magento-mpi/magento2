<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Test API getting orders list method
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @magentoApiDataFixture Mage/SalesOrder/_fixture/order.php
 */
class SalesOrder_StatusTest extends Magento_Test_TestCase_ApiAbstract
{
    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public static function tearDownAfterClass()
    {
        Magento_Test_TestCase_ApiAbstract::deleteFixture('customer', true);
        Magento_Test_TestCase_ApiAbstract::deleteFixture('product_virtual', true);
        Magento_Test_TestCase_ApiAbstract::deleteFixture('quote', true);
        Magento_Test_TestCase_ApiAbstract::deleteFixture('order', true);

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
        $order = Magento_Test_TestCase_ApiAbstract::getFixture('order');

        $order->setStatus('pending')
            ->save();

        $soapResult = $this->getWebService()->call(
            'sales_order.cancel',
            array(
                'orderIncrementId' => $order->getIncrementId()
            )
        );

        $this->assertTrue((bool)$soapResult, 'API call result in not TRUE');

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
        $order = Magento_Test_TestCase_ApiAbstract::getFixture('order');

        $order->setState(Mage_Sales_Model_Order::STATE_NEW, 'pending')
            ->save();

        $soapResult = $this->getWebService()->call(
            'sales_order.hold',
            array(
                'orderIncrementId' => $order->getIncrementId()
            )
        );

        $this->assertTrue((bool)$soapResult, 'API call result in not TRUE');

        // reload order to obtain new status
        $order->load($order->getId());

        $this->assertEquals(Mage_Sales_Model_Order::STATE_HOLDED, $order->getStatus(), 'Status is not HOLDED');
    }
}
