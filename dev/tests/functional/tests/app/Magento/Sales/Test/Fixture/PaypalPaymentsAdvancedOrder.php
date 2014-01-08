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

namespace Magento\Sales\Test\Fixture;

use Mtf\Factory\Factory;

/**
 * Class PaypalPaymentsAdvancedOrder
 * Guest checkout using PayPal Payments Advanced method
 *
 * @package Magento\Sales\Test\Fixture
 */
class PaypalPaymentsAdvancedOrder extends OrderCheckout
{
    /**
     * Prepare data for guest checkout using Paypal Payments Advanced.
     */
    protected function _initData()
    {
        $this->checkoutFixture = Factory::getFixtureFactory()->getMagentoCheckoutGuestPayPalAdvanced();
        //Verification data
        $this->_data = array(
            'totals' => array(
                'grand_total' => '$156.81'
            )
        );
    }
}
