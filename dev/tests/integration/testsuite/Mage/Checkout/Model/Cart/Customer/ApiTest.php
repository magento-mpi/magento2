<?php
/**
 * Checkout Cart Customer API tests.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_Checkout_Model_Cart_Customer_ApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test setting customer to a quote.
     *
     * @magentoDataFixture Mage/Checkout/_files/quote.php
     */
    public function testSet()
    {
        /** @var Mage_Sales_Model_Resource_Quote_Collection $quoteCollection */
        $quoteCollection = Mage::getModel('Mage_Sales_Model_Resource_Quote_Collection');
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $quoteCollection->getFirstItem();

        $customerData = array(
            'firstname' => 'testFirstname',
            'lastname' => 'testLastName',
            'email' => 'testEmail@mail.com',
            'mode' => 'guest',
            'website_id' => '0'
        );

        $result = Magento_Test_Helper_Api::call(
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
        $this->assertEmpty($diff, 'Expected quote customer data is incorrect.');
    }

    /**
     * Test setting customer address data to a quote.
     *
     * @magentoDataFixture Mage/Checkout/_files/quote.php
     */
    public function testSetAddresses()
    {
        /** @var Mage_Sales_Model_Resource_Quote_Collection $quoteCollection */
        $quoteCollection = Mage::getModel('Mage_Sales_Model_Resource_Quote_Collection');
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $quoteCollection->getFirstItem();

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

        $result = Magento_Test_Helper_Api::call(
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
        $this->assertEmpty($billingDiff, 'Expected billing address is incorrect');
        $shippingDiff = array_diff($shippingAddress, $quote->getShippingAddress()->getData());
        $this->assertEmpty($shippingDiff, 'Expected shipping address is incorrect');
    }
}
