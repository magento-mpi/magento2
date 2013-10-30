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
 * Class PaypalExpress
 * PayPal Express Method
 * Guest checkout using "Checkout with PayPal" button from product page and Free Shipping
 *
 * @ZephyrId MAGETWO-12415
 * @package Magento\Checkout\Test\Fixture
 */
class PaypalExpressOrder extends Checkout
{
    /**
     * @var string
     */
    private $orderId;

    /**
     * @var Checkout
     */
    private $checkoutFixture;

    /**
     * Prepare data for guest checkout using "Checkout with PayPal" button on product page
     */
    protected function _initData()
    {
        $this->checkoutFixture = Factory::getFixtureFactory()->getMagentoCheckoutPaypalExpress();
        //Verification data
        $this->_data = array(
            'totals' => array(
                'grand_total' => '$10.83'
            )
        );
    }

    /**
     * Persists prepared data into application
     */
    public function persist()
    {
        $this->orderId = Factory::getApp()->magentoCheckoutCreatePaypalExpressOrder($this->checkoutFixture);
    }

    /**
     * @return string
     */
    public function getGrandTotal()
    {
        return $this->checkoutFixture->getGrandTotal();
    }

    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }
}
