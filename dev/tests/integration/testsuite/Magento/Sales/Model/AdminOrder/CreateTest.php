<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model\AdminOrder;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * @magentoAppArea adminhtml
 */
class CreateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\AdminOrder\Create
     */
    protected $_model;

    /** @var \Magento\Message\ManagerInterface */
    protected $_messageManager;

    protected function setUp()
    {
        parent::setUp();
        $this->_messageManager = Bootstrap::getObjectManager()->get('Magento\Message\ManagerInterface');
        $this->_model = Bootstrap::getObjectManager()->create(
            'Magento\Sales\Model\AdminOrder\Create',
            ['messageManager' => $this->_messageManager]
        );
    }

    /**
     * @magentoDataFixture Magento/Downloadable/_files/product.php
     * @magentoDataFixture Magento/Downloadable/_files/order_with_downloadable_product.php
     */
    public function testInitFromOrderShippingAddressSameAsBillingWhenEmpty()
    {
        /** @var $order \Magento\Sales\Model\Order */
        $order = Bootstrap::getObjectManager()->create('Magento\Sales\Model\Order');
        $order->loadByIncrementId('100000001');
        $this->assertFalse($order->getShippingAddress());

        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->unregister('rule_data');
        $this->_model->initFromOrder($order);

        $this->assertFalse($order->getShippingAddress());
    }

    /**
     * @magentoDataFixture Magento/Downloadable/_files/product.php
     * @magentoDataFixture Magento/Downloadable/_files/order_with_downloadable_product.php
     * @magentoDataFixture Magento/Sales/_files/order_shipping_address_same_as_billing.php
     */
    public function testInitFromOrderShippingAddressSameAsBillingWhenSame()
    {
        /** @var $order \Magento\Sales\Model\Order */
        $order = Bootstrap::getObjectManager()->create('Magento\Sales\Model\Order');
        $order->loadByIncrementId('100000001');

        $this->assertNull($order->getShippingAddress()->getSameAsBilling());

        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->unregister('rule_data');
        $this->_model->initFromOrder($order);

        $this->assertTrue($order->getShippingAddress()->getSameAsBilling());
    }

    /**
     * @magentoDataFixture Magento/Downloadable/_files/product.php
     * @magentoDataFixture Magento/Downloadable/_files/order_with_downloadable_product.php
     * @magentoDataFixture Magento/Sales/_files/order_shipping_address_different_to_billing.php
     */
    public function testInitFromOrderShippingAddressSameAsBillingWhenDifferent()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = Bootstrap::getObjectManager();

        /** @var $order \Magento\Sales\Model\Order */
        $order = $objectManager->create('Magento\Sales\Model\Order');
        $order->loadByIncrementId('100000001');

        $this->assertNull($order->getShippingAddress()->getSameAsBilling());

        $objectManager->get('Magento\Core\Model\Registry')->unregister('rule_data');
        $this->_model->initFromOrder($order);

        $this->assertFalse($order->getShippingAddress()->getSameAsBilling());
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order_paid_with_verisign.php
     */
    public function testInitFromOrderCcInformationDeleted()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = Bootstrap::getObjectManager();

        /** @var $order \Magento\Sales\Model\Order */
        $order = $objectManager->create('Magento\Sales\Model\Order');
        $order->loadByIncrementId('100000001');

        $payment = $order->getPayment();
        $this->assertEquals('5', $payment->getCcExpMonth());
        $this->assertEquals('2016', $payment->getCcExpYear());
        $this->assertEquals('AE', $payment->getCcType());
        $this->assertEquals('0005', $payment->getCcLast4());

        $objectManager->get('Magento\Core\Model\Registry')->unregister('rule_data');
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
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = Bootstrap::getObjectManager();

        /** @var $order \Magento\Sales\Model\Order */
        $order = $objectManager->create('Magento\Sales\Model\Order');
        $order->loadByIncrementId('100000001');

        $payment = $order->getPayment();
        $this->assertEquals('5', $payment->getCcExpMonth());
        $this->assertEquals('2016', $payment->getCcExpYear());
        $this->assertEquals('AE', $payment->getCcType());
        $this->assertEquals('0005', $payment->getCcLast4());

        $objectManager->get('Magento\Core\Model\Registry')->unregister('rule_data');
        $payment = $this->_model->initFromOrder($order)->getQuote()->getPayment();

        $this->assertEquals('5', $payment->getCcExpMonth());
        $this->assertEquals('2016', $payment->getCcExpYear());
        $this->assertEquals('AE', $payment->getCcType());
        $this->assertEquals('0005', $payment->getCcLast4());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetCustomerWishlistNoCustomerId()
    {
        /** @var \Magento\Backend\Model\Session\Quote $session */
        $session = Bootstrap::getObjectManager()->create('Magento\Backend\Model\Session\Quote');
        $session->setCustomerId(null);
        $this->assertFalse(
            $this->_model->getCustomerWishlist(true),
            'If customer ID is not set to session, false is expected to be returned.'
        );
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     */
    public function testGetCustomerWishlist()
    {
        $customerIdFromFixture = 1;
        $productIdFromFixture = 1;
        /** @var \Magento\Backend\Model\Session\Quote $session */
        $session = Bootstrap::getObjectManager()->create('Magento\Backend\Model\Session\Quote');
        $session->setCustomerId($customerIdFromFixture);

        /** Test new wishlist creation for the customer specified above */
        /** @var \Magento\Wishlist\Model\Wishlist $wishlist */
        $wishlist = $this->_model->getCustomerWishlist(true);
        $this->assertInstanceOf(
            'Magento\Wishlist\Model\Wishlist',
            $wishlist,
            'New wishlist is expected to be created if existing customer does not have one yet.'
        );
        $this->assertEquals(0, $wishlist->getItemsCount(), 'New wishlist must be empty just after creation.');

        /** Add new item to wishlist and try to get it using getCustomerWishlist once again */
        $wishlist->addNewItem($productIdFromFixture)->save();
        $updatedWishlist = $this->_model->getCustomerWishlist(true);
        $this->assertEquals(
            1,
            $updatedWishlist->getItemsCount(),
            'Wishlist must contain a product which was added to it earlier.'
        );

        /** Try to load wishlist from cache in the class after it is deleted from DB */
        $wishlist->delete();
        $this->assertSame(
            $updatedWishlist,
            $this->_model->getCustomerWishlist(false),
            'Wishlist cached in class variable is expected to be returned.'
        );
        $this->assertNotSame(
            $updatedWishlist,
            $this->_model->getCustomerWishlist(true),
            'New wishlist is expected to be created when cache is forced to be refreshed.'
        );
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSetBillingAddress()
    {
        $addressData = $this->_getValidAddressData();
        /** Validate data before creating address object */
        $this->_model->setIsValidate(true)->setBillingAddress($addressData);
        $this->assertInstanceOf(
            'Magento\Sales\Model\Quote\Address',
            $this->_model->getBillingAddress(),
            'Billing address object was not created.'
        );

        $expectedAddressData = array_merge(
            $addressData,
            ['address_type' => 'billing', 'quote_id' => null, 'street' => "Line1\nLine2", 'save_in_address_book' => 0]
        );
        $this->assertEquals(
            $expectedAddressData,
            $this->_model->getBillingAddress()->getData(),
            'Created billing address is invalid.'
        );
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     */
    public function testSetBillingAddressValidationErrors()
    {
        $customerIdFromFixture = 1;
        /** @var \Magento\Backend\Model\Session\Quote $session */
        $session = Bootstrap::getObjectManager()->create('Magento\Backend\Model\Session\Quote');
        $session->setCustomerId($customerIdFromFixture);
        $invalidAddressData = array_merge($this->_getValidAddressData(), ['firstname' => '', 'lastname' => '']);
        /**
         * Note that validation errors are collected during setBillingAddress() call in the internal class variable,
         * but they are not set to message manager at this step.
         * They are set to message manager only during createOrder() call.
         */
        $this->_model->setIsValidate(true)->setBillingAddress($invalidAddressData);
        try{
            $this->_model->createOrder();
            $this->fail('Validation errors are expected to lead to exception during createOrder() call.');
        } catch (\Magento\Core\Exception $e) {
            /** createOrder is expected to throw exception with empty message when validation error occurs */
        }
        $errorMessages = [];
        /** @var $validationError \Magento\Message\Error */
        foreach ($this->_messageManager->getMessages()->getItems() as $validationError) {
            $errorMessages[] = $validationError->getText();
        }
        $this->assertTrue(
            in_array('Billing Address: "First Name" is a required value.', $errorMessages),
            'Expected validation message is absent.'
        );
        $this->assertTrue(
            in_array('Billing Address: "Last Name" is a required value.', $errorMessages),
            'Expected validation message is absent.'
        );
    }

    /**
     * Get valid address data for address creation.
     *
     * @return array
     */
    protected function _getValidAddressData()
    {
        return [
            'customer_address_id' => '',
            'prefix' => 'prefix',
            'firstname' => 'FirstName',
            'middlename' => 'MiddleName',
            'lastname' => 'LastName',
            'suffix' => 'suffix',
            'company' => 'Company Name',
            'street' => [
                0 => 'Line1',
                1 => 'Line2',
            ],
            'city' => 'City',
            'country_id' => 'US',
            'region' => '',
            'region_id' => '1',
            'postcode' => '76868',
            'telephone' => '+8709273498729384',
            'fax' => '',
            'vat_id' => '',
        ];
    }
}
