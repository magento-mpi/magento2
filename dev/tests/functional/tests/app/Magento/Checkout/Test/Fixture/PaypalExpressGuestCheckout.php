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
 * Class PaypalExpressGuestCheckout
 *
 * @package Magento\Checkout\Test\Fixture
 */
class PaypalExpressGuestCheckout extends DataFixture
{
    /**
     * Prepare data for guest checkout with PayPal Express
     */
    protected function _initData()
    {
        $this->_data = array(
            'totals' => array(
                'grand_total'       => '10',
                'authorized_amount' => '10',
                'comment_history'   => '',
            )
        );
    }
}
