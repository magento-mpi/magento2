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
        $this->checkoutFixture->persist();
        if(!is_null($this->additionalProducts))
        {
            foreach($this->additionalProducts as $product)
            {
                $this->checkoutFixture->addProduct($product);
            }
        }
        $this->orderId = Factory::getApp()->magentoCheckoutCreatePaypalExpressOrder($this->checkoutFixture);
    }

    /**
     * Get order grans total
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
     * @return \Magento\Customer\Test\Fixture\Address
     */
    public function getBillingAddress()
    {
        return $this->checkoutFixture->getBillingAddress();
    }

    /**
     * @return \Magento\Customer\Test\Fixture\Customer
     */
    public function getCustomer()
    {
        return $this->checkoutFixture->getPayPalCustomer();
    }

    /**
     * @return \Magento\Catalog\Test\Fixture\SimpleProduct
     */
    public function getProduct($index)
    {
        return $this->checkoutFixture->products[$index];
    }

    /**
     * @returns array
     */
    public function getProducts()
    {
        return $this->checkoutFixture->getProducts();
    }

    /**
     * @param array
     */
    public function setAdditionalProducts($products = null)
    {
        $this->additionalProducts = $products;
    }
}
