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

namespace Magento\Checkout\Model\Type;

/**
 * @magentoAppArea frontend
 */
class OnepageTest extends \PHPUnit_Framework_TestCase
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
        /** @var $model \Magento\Checkout\Model\Type\Onepage */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Checkout\Model\Type\Onepage');

        /** @var \Magento\Sales\Model\Resource\Quote\Collection $quoteCollection */
        $quoteCollection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Resource\Quote\Collection');
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $quoteCollection->getLastItem();

        $model->setQuote($quote);
        $model->saveBilling($customerData, null);

        $this->_prepareQuote($quote);

        $model->saveOrder();

        /** @var $order \Magento\Sales\Model\Order */
        $order = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Order');
        $order->loadByIncrementId($model->getLastOrderId());

        $this->assertNotEmpty($quote->getShippingAddress()->getCustomerAddressId(),
            'Quote shipping CustomerAddressId should not be empty');
        $this->assertNotEmpty($quote->getBillingAddress()->getCustomerAddressId(),
            'Quote billing CustomerAddressId should not be empty');

        $this->assertNotEmpty($order->getShippingAddress()->getCustomerAddressId(),
            'Order shipping CustomerAddressId should not be empty');
        $this->assertNotEmpty($order->getBillingAddress()->getCustomerAddressId(),
            'Order billing CustomerAddressId should not be empty');
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
