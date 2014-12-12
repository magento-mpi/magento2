<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Sales\Test\Fixture;

use Mtf\Factory\Factory;

/**
 * Class AuthorizeNetOrder
 * Guest checkout using Authorize.Net
 *
 */
class AuthorizeNetOrder extends OrderCheckout
{
    /**
     * Prepare data for guest checkout using Authorize.Net.
     */
    protected function _initData()
    {
        $this->checkoutFixture = Factory::getFixtureFactory()->getMagentoCheckoutGuestAuthorizenet();
        //Verification data
        $this->_data = [
            'totals' => [
                'grand_total' => '156.81',
            ],
        ];
    }
}
