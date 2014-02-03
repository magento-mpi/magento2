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
 * Class OrderCheckout
 * Fixture with all necessary data for order creation on the frontend
 *
 * @package Magento\Checkout\Test\Fixture
 */
class OrderCheckout extends Checkout
{
    /**
     * Order ID
     *
     * @var string
     */
    protected $orderId;

    /**
     * Checkout fixture
     *
     * @var Checkout
     */
    protected $checkoutFixture;

    /**
     * Product Array
     * @var array
     */
    protected $additionalProducts;

    /**
     * Return the checkout fixture for this order checkout instance.
     *
     * @return Checkout
     */
    public function getCheckoutFixture()
    {
        return $this->checkoutFixture;
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

    /**
     * Get payment method
     *
     * @return \Magento\Payment\Test\Fixture\Method
     */
    public function getPaymentMethod()
    {
        return $this->checkoutFixture->getPaymentMethod();
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
