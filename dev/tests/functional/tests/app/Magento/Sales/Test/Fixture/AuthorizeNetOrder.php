<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
        $this->_data = array(
            'totals' => array(
                'grand_total' => '$156.81'
            )
        );
    }
}
