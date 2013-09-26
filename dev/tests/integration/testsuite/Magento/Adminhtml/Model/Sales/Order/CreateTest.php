<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Model_Sales_Order_CreateTest extends PHPUnit_Framework_TestCase
{
    /**
     * Model instance
     *
     * @var Magento_Adminhtml_Model_Sales_Order_Create
     */
    protected $_model;

    protected function setUp()
    {
        parent::setUp();

        $this->_model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Adminhtml_Model_Sales_Order_Create');
    }

    /**
     * @magentoDataFixture Magento/Downloadable/_files/product.php
     * @magentoDataFixture Magento/Downloadable/_files/order_with_downloadable_product.php
     */
    public function testInitFromOrderShippingAddressSameAsBillingWhenEmpty()
    {
        /** @var $order Magento_Sales_Model_Order */
        $order = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Order');
        $order->loadByIncrementId('100000001');
        $this->assertFalse($order->getShippingAddress());

        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Registry')->unregister('rule_data');
        $this->_model->initFromOrder($order);

        $this->assertFalse($order->getShippingAddress());
    }

    /**
     * @magentoDataFixture Magento/Downloadable/_files/product.php
     * @magentoDataFixture Magento/Downloadable/_files/order_with_downloadable_product.php
     * @magentoDataFixture Magento/Adminhtml/_files/order_shipping_address_same_as_billing.php
     */
    public function testInitFromOrderShippingAddressSameAsBillingWhenSame()
    {
        /** @var $order Magento_Sales_Model_Order */
        $order = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Order');
        $order->loadByIncrementId('100000001');

        $this->assertNull($order->getShippingAddress()->getSameAsBilling());

        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Registry')->unregister('rule_data');
        $this->_model->initFromOrder($order);

        $this->assertTrue($order->getShippingAddress()->getSameAsBilling());
    }

    /**
     * @magentoDataFixture Magento/Downloadable/_files/product.php
     * @magentoDataFixture Magento/Downloadable/_files/order_with_downloadable_product.php
     * @magentoDataFixture Magento/Adminhtml/_files/order_shipping_address_different_to_billing.php
     */
    public function testInitFromOrderShippingAddressSameAsBillingWhenDifferent()
    {
        /** @var $order Magento_Sales_Model_Order */
        $order = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Order');
        $order->loadByIncrementId('100000001');

        $this->assertNull($order->getShippingAddress()->getSameAsBilling());

        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Registry')->unregister('rule_data');
        $this->_model->initFromOrder($order);

        $this->assertFalse($order->getShippingAddress()->getSameAsBilling());
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order_paid_with_verisign.php
     */
    public function testInitFromOrderCcInformationDeleted()
    {
        /** @var $order Magento_Sales_Model_Order */
        $order = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Order');
        $order->loadByIncrementId('100000001');

        $payment = $order->getPayment();
        $this->assertEquals('5', $payment->getCcExpMonth());
        $this->assertEquals('2016', $payment->getCcExpYear());
        $this->assertEquals('AE', $payment->getCcType());
        $this->assertEquals('0005', $payment->getCcLast4());

        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Registry')->unregister('rule_data');
        $payment = $this->_model->initFromOrder($order)->getQuote()->getPayment();

        $this->assertNull($payment->getCcExpMonth());
        $this->assertNull($payment->getCcExpYear());
        $this->assertNull($payment->getCcType());
        $this->assertNull($payment->getCcLast4());
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order_paid_with_saved_cc.php
     */
    public function testInitFromOrderSavedCcInformationNotDeleted()
    {
        /** @var $order Magento_Sales_Model_Order */
        $order = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Order');
        $order->loadByIncrementId('100000001');

        $payment = $order->getPayment();
        $this->assertEquals('5', $payment->getCcExpMonth());
        $this->assertEquals('2016', $payment->getCcExpYear());
        $this->assertEquals('AE', $payment->getCcType());
        $this->assertEquals('0005', $payment->getCcLast4());

        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Registry')->unregister('rule_data');
        $payment = $this->_model->initFromOrder($order)->getQuote()->getPayment();

        $this->assertEquals('5', $payment->getCcExpMonth());
        $this->assertEquals('2016', $payment->getCcExpYear());
        $this->assertEquals('AE', $payment->getCcType());
        $this->assertEquals('0005', $payment->getCcLast4());
    }
}
