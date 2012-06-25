<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Model_Sales_Order_CreateTest extends PHPUnit_Framework_TestCase
{
    /**
     * Model instance
     *
     * @var Mage_Adminhtml_Model_Sales_Order_Create
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Mage_Adminhtml_Model_Sales_Order_Create();
    }

    /**
     * @magentoDataFixture Mage/Downloadable/_files/product.php
     * @magentoDataFixture Mage/Downloadable/_files/order_with_downloadable_product.php
     */
    public function testInitFromOrderShippingAddressSameAsBillingWhenEmpty()
    {
        $order = new Mage_Sales_Model_Order();
        $order->loadByIncrementId('100000001');
        $this->assertFalse($order->getShippingAddress());

        Mage::unregister('rule_data');
        $this->_model->initFromOrder($order);

        $this->assertFalse($order->getShippingAddress());
    }

    /**
     * @magentoDataFixture Mage/Downloadable/_files/product.php
     * @magentoDataFixture Mage/Downloadable/_files/order_with_downloadable_product.php
     * @magentoDataFixture Mage/Adminhtml/_files/order_shipping_address_same_as_billing.php
     */
    public function testInitFromOrderShippingAddressSameAsBillingWhenSame()
    {
        $order = new Mage_Sales_Model_Order();
        $order->loadByIncrementId('100000001');

        $this->assertNull($order->getShippingAddress()->getSameAsBilling());

        Mage::unregister('rule_data');
        $this->_model->initFromOrder($order);

        $this->assertTrue($order->getShippingAddress()->getSameAsBilling());
    }

    /**
     * @magentoDataFixture Mage/Downloadable/_files/product.php
     * @magentoDataFixture Mage/Downloadable/_files/order_with_downloadable_product.php
     * @magentoDataFixture Mage/Adminhtml/_files/order_shipping_address_different_to_billing.php
     */
    public function testInitFromOrderShippingAddressSameAsBillingWhenDifferent()
    {
        $order = new Mage_Sales_Model_Order();
        $order->loadByIncrementId('100000001');

        $this->assertNull($order->getShippingAddress()->getSameAsBilling());

        Mage::unregister('rule_data');
        $this->_model->initFromOrder($order);

        $this->assertFalse($order->getShippingAddress()->getSameAsBilling());
    }
}
