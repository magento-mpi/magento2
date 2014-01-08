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
 * Class AuthorizeNetOrder
 * Guest checkout using Authorize.Net
 *
 * @package Magento\Sales\Test\Fixture
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

    /**
     * Persists prepared data into application
     */
    public function persist()
    {
        $this->checkoutFixture->persist();
        if(!is_null($this->additionalProducts))
        {
            foreach($this->additionalProducts as $product)
            {
                $this->checkoutFixture->addProduct($product);
            }
        }
        $this->orderId = Factory::getApp()->magentoCheckoutCreateOrder($this->checkoutFixture);
    }
}
