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
    /** @var  \Magento\Checkout\Model\Type\Onepage*/
    protected $_model;

    protected function setUp()
    {
        parent::setUp();
        $this->_model = Bootstrap::getObjectManager()
            ->create('Magento\Checkout\Model\Type\Onepage');
        /** @var \Magento\Sales\Model\Resource\Quote\Collection $quoteCollection */
        $quoteCollection = Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Resource\Quote\Collection');
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $quoteCollection->getLastItem();
        $this->_model->setQuote($quote);
        $this->_model->saveBilling($this->_getCustomerData(), null);
        $this->_prepareQuote($quote);
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
            'country_id' => 'AL',
            'telephone' => '(323) 255-5861',
            'customer_password' => 'password',
            'confirm_password' => 'password',
            'save_in_address_book' => '1',
            'use_for_shipping' => '1',
        );
    }
}
