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

class CheckoutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Paypal/_files/quote_payment_express.php
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testPrepareCustomerQuote()
    {
        $this->markTestIncomplete('Enable after refactoring of place() method');
        $objectManager = Bootstrap::getObjectManager();
        /** @var \Magento\Customer\Service\V1\CustomerAddressServiceInterface $addressService */
        $addressService = $objectManager->get('Magento\Customer\Service\V1\CustomerAddressServiceInterface');
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $objectManager->create('Magento\Sales\Model\Quote')->load(1);

        $quote->setCheckoutMethod(Onepage::METHOD_CUSTOMER); // to dive into _prepareCustomerQuote() on switch
        $quote->getShippingAddress()->setSameAsBilling(0);
        $customer = $objectManager->create('Magento\Customer\Model\Customer')->load(1);
        $customer->setDefaultBilling(false)
            ->setDefaultShipping(false)
            ->save();

        /** @var \Magento\Customer\Model\Session $customerSession */
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');
        $customerSession->loginById(1);
        /** @var Checkout $checkout */
        $checkout = $objectManager->create(
            'Magento\Paypal\Model\Express\Checkout',
            [
                'params' => [
                    'config' => $this->getMock('Magento\Paypal\Model\Config', [], [], '', false),
                    'quote' => $quote,
                ]
            ]
        );

        $checkout->place('token');

        $this->assertEquals(1, $quote->getCustomerId());
        $addressService->getAddresses($quote->getCustomerId());
        $this->assertEquals(2, count($addressService->getAddresses($quote->getCustomerId())));
        $this->assertTrue($quote->getBillingAddress()->getCustomerAddress()->getIsDefaultBilling());
        $this->assertTrue($quote->getShippingAddress()->getCustomerAddress()->getIsDefaultShipping());
    }
}
