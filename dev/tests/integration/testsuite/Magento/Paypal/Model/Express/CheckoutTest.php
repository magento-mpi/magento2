<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paypal\Model\Express;

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
     * @magentoDataFixture Magento/Paypal/_files/quote_payment_express.php
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testPrepareCustomerQuote()
    {
        $this->markTestIncomplete('Enable after refactoring of place() method');
        /** @var \Magento\Customer\Service\V1\CustomerAddressServiceInterface $addressService */
        $addressService = $this->_objectManager->get('Magento\Customer\Service\V1\CustomerAddressServiceInterface');
        /** @var Quote $quote */
        $quote = $this->_objectManager->create('Magento\Sales\Model\Quote')->load(1);
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
        $this->assertTrue($quote->getBillingAddress()->getCustomerAddress()->getIsDefaultBilling());
        $this->assertTrue($quote->getShippingAddress()->getCustomerAddress()->getIsDefaultShipping());
    }

    /**
     * @magentoDataFixture Magento/Paypal/_files/quote_payment_express.php
     */
    public function testPrepareNewCustomerQuote()
    {
        /** @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerService */
        $customerService = $this->_objectManager->get('Magento\Customer\Service\V1\CustomerAccountServiceInterface');
        /** @var Quote $quote */
        $quote = $this->_objectManager->create('Magento\Sales\Model\Quote')->load(1);
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
}
