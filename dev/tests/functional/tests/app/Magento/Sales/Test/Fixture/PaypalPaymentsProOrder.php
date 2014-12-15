<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Sales\Test\Fixture;

use Mtf\Factory\Factory;

/**
 * Class PaypalPaymentsProOrder
 * Guest checkout using PayPal Payments Pro method
 *
 */
class PaypalPaymentsProOrder extends OrderCheckout
{
    /**
     * Prepare data for guest checkout using Paypal Payments Pro.
     */
    protected function _initData()
    {
        $this->checkoutFixture = Factory::getFixtureFactory()->getMagentoCheckoutGuestPaypalDirect();
        //Verification data
        $this->_data = [
            'totals' => [
                'grand_total' => '156.81',
            ],
        ];
    }
}
