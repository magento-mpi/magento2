<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 */

namespace Magento\Checkout\Model\Type;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * @magentoDataFixture Magento/Checkout/_files/quote_with_product_and_payment.php
 * @magentoAppArea frontend
 */
class OnepageTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Checkout\Model\Type\Onepage */
    protected $_model;

    /** @var \Magento\Sales\Model\Quote */
    protected $_currentQuote;

    protected function setUp()
    {
        parent::setUp();
        $this->_model = Bootstrap::getObjectManager()->create('Magento\Checkout\Model\Type\Onepage');
        /** @var \Magento\Sales\Model\Resource\Quote\Collection $quoteCollection */
        $quoteCollection = Bootstrap::getObjectManager()->create('Magento\Sales\Model\Resource\Quote\Collection');
        /** @var \Magento\Sales\Model\Quote $quote */
        $this->_currentQuote = $quoteCollection->getLastItem();
        $this->_model->setQuote($this->_currentQuote);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     */
    public function testSaveShipping()
    {
        $data = [
            'address_id' => '',
            'firstname' => 'Joe',
            'lastname' => 'Black',
            'company' => 'Lunatis',
            'street' => ['1100 Parmer', 'ln.'],
            'city' => 'Austin',
            'region_id' => '57',
            'region' => '',
            'postcode' => '78757',
            'country_id' => 'US',
            'telephone' => '(512) 999-9999',
            'fax' => '',
            'save_in_address_book' => 1
        ];
        $this->_model->saveShipping($data, 1);
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testSaveOrder()
    {
        $this->_model->saveBilling($this->_getCustomerData(), null);
        $this->_prepareQuote($this->_getQuote());
        $this->_model->saveOrder();

        /** @var $order \Magento\Sales\Model\Order */
        $order = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Order');
        $order->loadByIncrementId($this->_model->getLastOrderId());

        $this->assertNotEmpty($this->_model->getQuote()->getShippingAddress()->getCustomerAddressId(),
            'Quote shipping CustomerAddressId should not be empty');
        $this->assertNotEmpty($this->_model->getQuote()->getBillingAddress()->getCustomerAddressId(),
            'Quote billing CustomerAddressId should not be empty');

        $this->assertNotEmpty($order->getShippingAddress()->getCustomerAddressId(),
            'Order shipping CustomerAddressId should not be empty');
        $this->assertNotEmpty($order->getBillingAddress()->getCustomerAddressId(),
            'Order billing CustomerAddressId should not be empty');
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testInitCheckoutNotLoggedIn()
    {
        $this->_model->saveBilling($this->_getCustomerData(), null);
        $this->_prepareQuote($this->_getQuote());
        $this->assertTrue($this->_model->getCheckout()->getSteps()['shipping']['allow']);
        $this->assertTrue($this->_model->getCheckout()->getSteps()['billing']['allow']);
        $this->_model->initCheckout();
        $this->assertFalse($this->_model->getCheckout()->getSteps()['shipping']['allow']);
        $this->assertFalse($this->_model->getCheckout()->getSteps()['billing']['allow']);
        $this->assertNull($this->_model->getQuote()->getCustomerData()->getEmail());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testInitCheckoutLoggedIn()
    {
        $this->_model->saveBilling($this->_getCustomerData(), null);
        $this->_prepareQuote($this->_getQuote());
        $customerIdFromFixture = 1;
        $emailFromFixture = 'customer@example.com';
        /** @var $customerSession \Magento\Customer\Model\Session*/
        $customerSession = Bootstrap::getObjectManager()->create(
            '\Magento\Customer\Model\Session');
        /** @var $customerService \Magento\Customer\Service\V1\CustomerService*/
        $customerService = Bootstrap::getObjectManager()->create('\Magento\Customer\Service\V1\CustomerService');
        $customerDto = $customerService->getCustomer($customerIdFromFixture);
        $customerSession->setCustomerData($customerDto);
        $this->_model = Bootstrap::getObjectManager()->create(
            'Magento\Checkout\Model\Type\Onepage',
            ['customerSession' => $customerSession]
        );
        $this->assertTrue($this->_model->getCheckout()->getSteps()['shipping']['allow']);
        $this->assertTrue($this->_model->getCheckout()->getSteps()['billing']['allow']);
        $this->_model->initCheckout();
        $this->assertFalse($this->_model->getCheckout()->getSteps()['shipping']['allow']);
        //When the user is logged in and for Step billing - allow is not reset to true
        $this->assertTrue($this->_model->getCheckout()->getSteps()['billing']['allow']);
        $this->assertEquals($emailFromFixture, $this->_model->getQuote()->getCustomerData()->getEmail());
    }

    /**
     * New customer, the same address should be used for shipping and billing, it should be persisted to DB.
     *
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     */
    public function testSaveBilling()
    {
        $quote = $this->_model->getQuote();

        /** Preconditions */
        $customerData = $this->_getCustomerData();
        $customerAddressId = false;
        $this->assertEquals(1, $customerData['use_for_shipping'], "Precondition failed: use_for_shipping is invalid");
        $this->assertEquals(
            1,
            $customerData['save_in_address_book'],
            "Precondition failed: save_in_address_book is invalid"
        );
        $this->assertEmpty(
            $quote->getBillingAddress()->getId(),
            "Precondition failed: billing address must not be initialized."
        );
        $this->assertEmpty(
            $quote->getShippingAddress()->getId(),
            "Precondition failed: billing address must not be initialized."
        );

        /** Execute SUT */
        $result = $this->_model->saveBilling($customerData, $customerAddressId);
        $this->assertEquals([], $result, 'Return value is invalid');

        /** Ensure that quote addresses were persisted correctly */
        $billingAddress = $quote->getBillingAddress();
        $shippingAddress = $quote->getShippingAddress();

        $quoteAddressFieldsToCheck = [
            'quote_id' => $quote->getId(),
            'firstname' => 'John',
            'lastname' => 'Smith',
            'email' => 'John.Smith@example.com',
            'street' => '6131 Monterey Rd, Apt 1',
            'city' => 'Los Angeles',
            'postcode' => '90042',
            'country_id' => 'US',
            'region_id' => '1',
            'region' => 'Alabama',
            'telephone' => '(323) 255-5861',
            'customer_id' => null,
            'customer_address_id' => null
        ];

        foreach ($quoteAddressFieldsToCheck as $field => $value) {
            $this->assertEquals($value, $billingAddress->getData($field), "{$field} value is invalid");
            $this->assertEquals($value, $shippingAddress->getData($field), "{$field} value is invalid");
        }
        $this->assertEquals('1', $shippingAddress->getData('same_as_billing'), "same_as_billing value is invalid");
        $this->assertGreaterThan(0, $shippingAddress->getData('address_id'), "address_id value is invalid");
        $this->assertGreaterThan(0, $billingAddress->getData('address_id'), "address_id value is invalid");
        $this->assertEquals(
            1,
            $billingAddress->getData('save_in_address_book'),
            "save_in_address_book value is invalid"
        );
        $this->assertEquals(
            0,
            $shippingAddress->getData('save_in_address_book'),
            "As soon as 'same_as_billing' is set to 1, 'save_in_address_book' of shipping should be 0"
        );

        /** Ensure that customer-related data was ported to quote correcty */
        $quoteFieldsToCheck = [
            'customer_firstname' => 'John',
            'customer_lastname' => 'Smith',
            'customer_email' => 'John.Smith@example.com'
        ];
        foreach ($quoteFieldsToCheck as $field => $value) {
            $this->assertEquals($value, $quote->getData($field), "{$field} value is set to quote incorrectly.");
        }

        /** Perform if checkout steps status was correctly updated in session */
        /** @var \Magento\Checkout\Model\Session $checkoutSession */
        $checkoutSession = Bootstrap::getObjectManager()->get('Magento\Checkout\Model\Session');
        $this->assertTrue($checkoutSession->getStepData('billing', 'allow'), 'Billing step should be allowed.');
        $this->assertTrue($checkoutSession->getStepData('billing', 'complete'), 'Billing step should be completed.');
        $this->assertTrue($checkoutSession->getStepData('shipping', 'allow'), 'Shipping step should be allowed.');
    }

    /**
     * @return \Magento\Sales\Model\Quote
     */
    protected function _getQuote()
    {
        return $this->_currentQuote;
    }

    /**
     * Prepare Quote
     *
     * @param \Magento\Sales\Model\Quote $quote
     */
    protected function _prepareQuote($quote)
    {
        /** @var $rate \Magento\Sales\Model\Quote\Address\Rate */
        $rate = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Quote\Address\Rate');
        $rate->setCode('freeshipping_freeshipping');
        $rate->getPrice(1);

        $quote->getShippingAddress()->setShippingMethod('freeshipping_freeshipping');
        $quote->getShippingAddress()->addShippingRate($rate);
        $quote->setCheckoutMethod(\Magento\Checkout\Model\Type\Onepage::METHOD_REGISTER);
    }

    /**
     * Customer data for quote
     *
     * @return array
     */
    protected function _getCustomerData()
    {
        return array (
            'firstname' => 'John',
            'lastname' => 'Smith',
            'email' => 'John.Smith@example.com',
            'street' =>array (
                0 => '6131 Monterey Rd, Apt 1',
                1 => '',
            ),
            'city' => 'Los Angeles',
            'postcode' => '90042',
            'country_id' => 'US',
            'region_id' => '1',
            'telephone' => '(323) 255-5861',
            'customer_password' => 'password',
            'confirm_password' => 'password',
            'save_in_address_book' => '1',
            'use_for_shipping' => '1',
        );
    }
}
