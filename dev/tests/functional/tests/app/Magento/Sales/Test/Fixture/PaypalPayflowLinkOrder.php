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
 * Class PaypalPayflowLinkOrder
 * Guest checkout using PayPal Payflow Link method
 *
 * @package Magento\Sales\Test\Fixture
 */
class PaypalPayflowLinkOrder extends OrderCheckout
{
    /**
     * Prepare data for guest checkout using Paypal Payflow Link.
     */
    protected function _initData()
    {
        $this->checkoutFixture = Factory::getFixtureFactory()->getMagentoCheckoutGuestPaypalPayflowLink();
        //Verification data
        $this->_data = array(
            'totals' => array(
                'grand_total' => '$168.72'
            )
        );
    }
}
