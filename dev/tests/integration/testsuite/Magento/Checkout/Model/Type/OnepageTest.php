<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea frontend
 */
class Magento_Checkout_Model_Type_OnepageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Checkout/_files/quote_with_product_and_payment.php
     * @dataProvider saveOrderDataProvider
     *
     * @param array $customerData
     */
    public function testSaveOrder($customerData)
    {
        /** @var $model Magento_Checkout_Model_Type_Onepage */
        $model = Mage::getModel('Magento_Checkout_Model_Type_Onepage');

        /** @var Magento_Sales_Model_Resource_Quote_Collection $quoteCollection */
        $quoteCollection = Mage::getModel('Magento_Sales_Model_Resource_Quote_Collection');
        /** @var Magento_Sales_Model_Quote $quote */
        $quote = $quoteCollection->getFirstItem();

        $model->setQuote($quote);
        $model->saveBilling($customerData, null);

        $this->_prepareQuote($quote);

        $model->saveOrder();

        /** @var $order Magento_Sales_Model_Order */
        $order = Mage::getModel('Magento_Sales_Model_Order');
        $order->loadByIncrementId($model->getLastOrderId());

        $this->assertNotEmpty($quote->getShippingAddress()->getCustomerAddressId(),
            'Quote shipping CustomerAddressId should not be ampty');
        $this->assertNotEmpty($quote->getBillingAddress()->getCustomerAddressId(),
            'Quote billing CustomerAddressId should not be ampty');

        $this->assertNotEmpty($order->getShippingAddress()->getCustomerAddressId(),
            'Order shipping CustomerAddressId should not be ampty');
        $this->assertNotEmpty($order->getBillingAddress()->getCustomerAddressId(),
            'Order billing CustomerAddressId should not be ampty');
    }


    public function saveOrderDataProvider()
    {
        return array(
            array($this->_getCustomerData()),
        );
    }

    /**
     * Prepare Quote
     *
     * @param Magento_Sales_Model_Quote $quote
     */
    protected function _prepareQuote($quote)
    {
        /** @var $rate Magento_Sales_Model_Quote_Address_Rate */
        $rate = Mage::getModel('Magento_Sales_Model_Quote_Address_Rate');
        $rate->setCode('freeshipping_freeshipping');
        $rate->getPrice(1);

        $quote->getShippingAddress()->setShippingMethod('freeshipping_freeshipping');
        $quote->getShippingAddress()->addShippingRate($rate);
        $quote->setCheckoutMethod(Magento_Checkout_Model_Type_Onepage::METHOD_REGISTER);
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
