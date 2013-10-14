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
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class GuestAuthorizenet
 * Credit Card (Authorize.net)
 * Guest checkout with Authorize.Net payment method and offline shipping method
 *
 * @ZephyrId MAGETWO-12832
 * @package Magento\Checkout\Test\Fixture
 */
class GuestAuthorizenet extends Checkout
{
    /**
     * Prepare Authorize.Net data
     */
    protected function _initData()
    {
        //Configuration
        Factory::getFixtureFactory()->getMagentoCoreConfig()->switchData('flat_rate')->persist();
        Factory::getFixtureFactory()->getMagentoCoreConfig()->switchData('authorizenet')->persist();
        Factory::getFixtureFactory()->getMagentoCoreConfig()->switchData('default_tax_config')->persist();
        //Products
        $simple1 = Factory::getFixtureFactory()->getMagentoCatalogProduct()->switchData('simple');
        $simple2 = Factory::getFixtureFactory()->getMagentoCatalogProduct()->switchData('simple');
        $simple1->persist();
        $simple2->persist();
        $this->products = array(
            $simple1,
            $simple2
        );
        //Checkout data
        $this->billingAddress = Factory::getFixtureFactory()->getMagentoCustomerAddress()->switchData('address_US_1');
        $this->shippingMethods = Factory::getFixtureFactory()->getMagentoShippingMethod()->switchData('flat_rate');
        $this->paymentMethod = Factory::getFixtureFactory()->getMagentoPaymentMethod()->switchData('authorizenet');
        $this->creditCard = Factory::getFixtureFactory()->getMagentoPaymentCc()->switchData('visa_authorizenet');
        //Verification data
        $this->_data = array(
            'totals' => array(
                'grand_total' => 30
            )
        );
    }
}
