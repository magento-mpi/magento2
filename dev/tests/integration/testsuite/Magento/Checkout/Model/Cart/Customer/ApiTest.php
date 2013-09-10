<?php
/**
 * Checkout Cart Customer API tests.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @magentoDataFixture Magento/Checkout/_files/quote_with_simple_product.php
 */
class Magento_Checkout_Model_Cart_Customer_ApiTest extends Magento_Checkout_Model_Cart_AbstractTest
{
    protected function setUp()
    {
        $this->markTestSkipped('Api tests were skipped');
    }

    /**
     * Test setting customer to a quote.
     */
    public function testSet()
    {
        $quote = $this->_getQuote();

        $customerData = array(
            'firstname' => 'testFirstname',
            'lastname' => 'testLastName',
            'email' => 'testEmail@mail.com',
            'mode' => 'guest',
            'website_id' => '0'
        );

        $result = Magento_TestFramework_Helper_Api::call(
            $this,
            'shoppingCartCustomerSet',
            array(
                'quoteId' => $quote->getId(),
                'customerData' => (object)$customerData,
            )
        );
        $this->assertTrue($result);

        $quote->load($quote->getId());
        $expectedQuoteData = array(
            'customer_firstname' => 'testFirstname',
            'customer_lastname' => 'testLastName',
            'customer_email' => 'testEmail@mail.com',
        );
        $diff = array_diff_assoc($expectedQuoteData, $quote->getData());
        $this->assertEmpty($diff, 'Customer data in quote is incorrect.');
    }

    /**
     * Test setting customer address data to a quote.
     */
    public function testSetAddresses()
    {
        $quote = $this->_getQuote();

        $billingAddress = array(
            'mode' => 'billing',
            'firstname' => 'first name',
            'lastname' => 'last name',
            'street' => 'street address',
            'city' => 'city',
            'postcode' => 'postcode',
            'country_id' => 'US',
            'region_id' => 1,
            'telephone' => '123456789',
            'is_default_billing' => 1
        );
        $shippingAddress = array(
            'mode' => 'shipping',
            'firstname' => 'testFirstname',
            'lastname' => 'testLastname',
            'company' => 'testCompany',
            'street' => 'testStreet',
            'city' => 'testCity',
            'postcode' => 'testPostcode',
            'country_id' => 'US',
            'region_id' => 1,
            'telephone' => '0123456789',
            'is_default_shipping' => 0,
        );

        $result = Magento_TestFramework_Helper_Api::call(
            $this,
            'shoppingCartCustomerAddresses',
            array(
                'quoteId' => $quote->getId(),
                'customerAddressData' => array(
                    (object)$billingAddress,
                    (object)$shippingAddress,
                ),
            )
        );
        $this->assertTrue($result);

        $quote->load($quote->getId());
        $billingDiff = array_diff($billingAddress, $quote->getBillingAddress()->getData());
        $this->assertEmpty($billingDiff, 'Billing address in quote is incorrect.');
        $shippingDiff = array_diff($shippingAddress, $quote->getShippingAddress()->getData());
        $this->assertEmpty($shippingDiff, 'Shipping address in quote is incorrect.');
    }
}
