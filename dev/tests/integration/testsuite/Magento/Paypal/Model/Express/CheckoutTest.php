<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paypal\Model\Express;

use Magento\Customer\Model\Customer;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Checkout\Model\Type\Onepage;
use Magento\Sales\Model\Quote;

class CheckoutTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\ObjectManager */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = Bootstrap::getObjectManager();
    }

    /**
     * @magentoDataFixture Magento/Paypal/_files/quote_payment_express_with_customer.php
     * @magentoAppIsolation enabled
     */
    public function testPrepareCustomerQuote()
    {
        /** @var \Magento\Customer\Service\V1\CustomerAddressServiceInterface $addressService */
        $addressService = $this->_objectManager->get('Magento\Customer\Service\V1\CustomerAddressServiceInterface');
        /** @var Quote $quote */
        $quote = $this->_getQuote();
        $quote->setCheckoutMethod(Onepage::METHOD_CUSTOMER); // to dive into _prepareCustomerQuote() on switch
        $quote->getShippingAddress()->setSameAsBilling(0);
        $customer = $this->_objectManager->create('Magento\Customer\Model\Customer')->load(1);
        $customer->setDefaultBilling(false)
            ->setDefaultShipping(false)
            ->save();

        /** @var \Magento\Customer\Model\Session $customerSession */
        $customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
        $customerSession->loginById(1);
        $checkout = $this->_getCheckout($quote);
        $checkout->place('token');

        $this->assertEquals(1, $quote->getCustomerId());
        $addressService->getAddresses($quote->getCustomerId());
        $this->assertEquals(2, count($addressService->getAddresses($quote->getCustomerId())));

        $this->assertEquals(1, $quote->getBillingAddress()->getCustomerAddressId());
        $this->assertEquals(2, $quote->getShippingAddress()->getCustomerAddressId());

        $order = $checkout->getOrder();
        $this->assertEquals(1, $order->getBillingAddress()->getCustomerAddressId());
        $this->assertEquals(2, $order->getShippingAddress()->getCustomerAddressId());
    }

    /**
     * @magentoDataFixture Magento/Paypal/_files/quote_payment_express.php
     * @magentoAppIsolation enabled
     */
    public function testPrepareNewCustomerQuote()
    {
        /** @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerService */
        $customerService = $this->_objectManager->get('Magento\Customer\Service\V1\CustomerAccountServiceInterface');

        /** @var Quote $quote */
        $quote = $this->_getQuote();

        $quote->setCheckoutMethod(Onepage::METHOD_REGISTER); // to dive into _prepareNewCustomerQuote() on switch
        $quote->setCustomerEmail('user@example.com');
        $quote->setCustomerFirstname('Firstname');
        $quote->setCustomerLastname('Lastname');
        $checkout = $this->_getCheckout($quote);
        $checkout->place('token');
        $customer = $customerService->getCustomer($quote->getCustomerId());
        $customerDetails = $customerService->getCustomerDetails($customer->getId());
        $this->assertEquals('user@example.com', $customer->getEmail());
        $this->assertEquals('11111111', $customerDetails->getAddresses()[0]->getTelephone());
    }

    /**
     * @magentoDataFixture Magento/Paypal/_files/quote_payment_express.php
     * @magentoAppIsolation enabled
     */
    public function testPrepareNewCustomerQuoteConfirmationRequired()
    {
        /** @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerService */
        $customerService = $this->_objectManager->get('Magento\Customer\Service\V1\CustomerAccountServiceInterface');

        /** @var Quote $quote */
        $quote = $this->_getQuote();

        $quote->setCheckoutMethod(Onepage::METHOD_REGISTER); // to dive into _prepareNewCustomerQuote() on switch
        $quote->setCustomerEmail('user@example.com');
        $quote->setCustomerFirstname('Firstname');
        $quote->setCustomerLastname('Lastname');

        /** @var \Magento\Core\Model\StoreManagerInterface $storeManager */
        $storeManager = $this->_objectManager->get('\Magento\Core\Model\StoreManagerInterface');
        $storeManager->getStore()->setConfig(Customer::XML_PATH_IS_CONFIRM, true);

        $checkout = $this->_getCheckout($quote);
        $checkout->place('token');
        $customer = $customerService->getCustomer($quote->getCustomerId());
        $customerDetails = $customerService->getCustomerDetails($customer->getId());
        $this->assertEquals('user@example.com', $customer->getEmail());
        $this->assertEquals('11111111', $customerDetails->getAddresses()[0]->getTelephone());

        /** @var \Magento\Message\ManagerInterface $messageManager */
        $messageManager = $this->_objectManager->get('\Magento\Message\ManagerInterface');
        $confirmationText = sprintf(
            'customer/account/confirmation/email/%s/key/',
            $customerDetails->getCustomer()->getEmail()
        );
        $this->assertTrue(
            strpos($messageManager->getMessages()->getLastAddedMessage()->getText(), $confirmationText) !== false
        );

    }


    /**
     * @magentoDataFixture Magento/Paypal/_files/quote_payment_express.php
     * @magentoAppIsolation enabled
     */
    public function testPlaceGuestQuote()
    {
        /** @var Quote $quote */
        $quote = $this->_getQuote();
        $quote->setCheckoutMethod(Onepage::METHOD_GUEST); // to dive into _prepareCustomerQuote() on switch
        $quote->getShippingAddress()->setSameAsBilling(0);

        $checkout = $this->_getCheckout($quote);
        $checkout->place('token');

        $this->assertNull($quote->getCustomerId());
        $this->assertTrue($quote->getCustomerIsGuest());
        $this->assertEquals(\Magento\Customer\Model\Group::NOT_LOGGED_IN_ID, $quote->getCustomerGroupId());

        $this->assertNotEmpty($quote->getBillingAddress());
        $this->assertNotEmpty($quote->getShippingAddress());

        $order = $checkout->getOrder();
        $this->assertNotEmpty($order->getBillingAddress());
        $this->assertNotEmpty($order->getShippingAddress());
    }

    /**
     * @param Quote $quote
     * @return Checkout
     */
    protected function _getCheckout(Quote $quote)
    {
        return $this->_objectManager->create(
            'Magento\Paypal\Model\Express\Checkout',
            [
                'params' => [
                    'config' => $this->getMock('Magento\Paypal\Model\Config', [], [], '', false),
                    'quote' => $quote,
                ]
            ]
        );
    }

    /**
     * @return Quote
     */
    protected function _getQuote()
    {
        /** @var \Magento\Sales\Model\Resource\Quote\Collection $quoteCollection */
        $quoteCollection = $this->_objectManager->create('Magento\Sales\Model\Resource\Quote\Collection');

        return $quoteCollection->getLastItem();
    }
}
