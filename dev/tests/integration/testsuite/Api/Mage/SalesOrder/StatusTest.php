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
 * @magentoDataFixture Api/Mage/SalesOrder/_fixture/order.php
 */
class SalesOrder_StatusTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public static function tearDownAfterClass()
    {
        PHPUnit_Framework_TestCase::deleteFixture('customer', true);
        PHPUnit_Framework_TestCase::deleteFixture('product_virtual', true);
        PHPUnit_Framework_TestCase::deleteFixture('quote', true);
        PHPUnit_Framework_TestCase::deleteFixture('order', true);

        parent::tearDownAfterClass();
    }

    /**
     * Test for salesOrderCancel when order is in 'pending' status
     */
    public function testCancelPendingOrder()
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = Mage::registry('order');

        $order->setStatus('pending')
            ->save();

        $soapResult = Magento_Test_Helper_Api::call(
            $this,
            'salesOrderCancel',
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
     * Test for salesOrderHold when order is in 'processing' status
     */
    public function testHoldProcessingOrder()
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = Mage::registry('order');

        $order->setState(Mage_Sales_Model_Order::STATE_NEW, 'pending')
            ->save();

        $soapResult = Magento_Test_Helper_Api::call(
            $this,
            'salesOrderHold',
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
