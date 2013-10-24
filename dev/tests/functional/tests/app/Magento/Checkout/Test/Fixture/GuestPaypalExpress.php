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

use Mtf\Fixture\DataFixture;

/**
 * Fixture PaypalExpress
 */
class GuestPaypalExpress extends DataFixture
{
    /**
     * Prepare data for guest checkout using "Checkout with PayPal" button on product page
     */
    protected function _initData()
    {
        $this->_data = array(
            'totals' => array(
                'grand_total'       => '$10.00',
                'authorized_amount' => '$10.00',
                'comment_history'   => '',
            )
        );
    }
}
