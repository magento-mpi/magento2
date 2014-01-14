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
 * Class PaypalPayflowProOrder
 * Guest checkout using PayPal Payflow Pro method
 *
 * @package Magento\Sales\Test\Fixture
 */
class PaypalPayflowProOrder extends OrderCheckout
{
    /**
     * Prepare data for guest checkout using Paypal Payflow Pro.
     */
    protected function _initData()
    {
        $this->checkoutFixture = Factory::getFixtureFactory()->getMagentoCheckoutGuestPayPalPayflowPro();
        //Verification data
        $this->_data = array(
            'totals' => array(
                'grand_total' => '$156.81'
            )
        );
    }
}
