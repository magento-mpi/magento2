<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Fixture;

use Mtf\Factory\Factory;

/**
 * Class ExistingCustomerCheckMoneyOrder
 * Registered shoppers checkout using check or money order
 *
 * @package Magento\Checkout\Test\Fixture
 */
class ExistingCustomerCheckMoneyOrder extends Checkout
{

    /**
     * Setup fixture
     */
    public function persist()
    {
        // Checkout data
        $this->customer = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        $this->customer->switchData('customer_US_1');
        $this->customer->persist();

        // Specify existing customer data-set (does not contain email address or password)
        $this->billingAddress = Factory::getFixtureFactory()->getMagentoCustomerAddress();
        $this->billingAddress->switchData('address_data_US_1');

        $this->paymentMethod = Factory::getFixtureFactory()->getMagentoPaymentMethod();
        $this->paymentMethod->switchData('check_money_order');

        return $this;
    }
}
