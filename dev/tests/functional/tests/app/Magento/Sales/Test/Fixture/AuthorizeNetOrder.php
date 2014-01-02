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
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class AuthorizeNetOrder
 * Guest checkout using Authorize.Net
 *
 * @ZephyrId MAGETWO-12833
 * @package Magento\Checkout\Test\Fixture
 */
class AuthorizeNetOrder extends Checkout
{
    /**
     * Order ID
     *
     * @var string
     */
    private $orderId;

    /**
     * Checkout fixture
     *
     * @var Checkout
     */
    private $checkoutFixture;

    /**
     * Product Array
     * @var array
     * */
    private $additionalProducts;

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

    /**
     * Get order grand total
     *
     * @return string
     */
    public function getGrandTotal()
    {
        return $this->checkoutFixture->getGrandTotal();
    }

    /**
     * Get order id
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }
}
