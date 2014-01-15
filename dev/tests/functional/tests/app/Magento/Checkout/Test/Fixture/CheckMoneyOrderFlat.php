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

use Mtf\System\Config;
use Mtf\Factory\Factory;
use Magento\Catalog\Test\Fixture;

/**
 * Guest checkout with Check/Money order payment method, flat shipping method, no tax.
 *
 * @package Magento\CatalogRule\Test\Fixture
 */
class CheckMoneyOrderFlat extends Checkout
{
    /**
     * Custom constructor
     *
     * @param Config $configuration
     * @param array $placeholders
     */
    public function __construct(Config $configuration, $placeholders = array())
    {
        parent::__construct($configuration, $placeholders);

        $this->products = $placeholders['products'];
    }

    /**
     * Prepare data
     */
    protected function _initData()
    {
        //Verification data
        $this->_data = array(
            'totals' => array(
                'grand_total' => '$21.00',
                'sub_total' => '$11.00'
            )
        );
    }

    /**
     * Persist prepared data into application
     */
    public function persist()
    {
        //Configuration
        $this->_persistConfiguration(array('flat_rate', 'check_money_order'));

        //Tax
        Factory::getApp()->magentoTaxRemoveTaxRule();

        //Checkout data
        $this->billingAddress = Factory::getFixtureFactory()->getMagentoCustomerAddress();
        $this->billingAddress->switchData('address_US_1');

        $this->shippingMethods = Factory::getFixtureFactory()->getMagentoShippingMethod();
        $this->shippingMethods->switchData('flat_rate');

        $this->paymentMethod = Factory::getFixtureFactory()->getMagentoPaymentMethod();
        $this->paymentMethod->switchData('check_money_order');
    }
}