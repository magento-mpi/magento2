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
 * Class PaypalExpress
 * PayPal Express Method
 * Guest checkout using "Checkout with PayPal" button from product page and Free Shipping
 *
 * @ZephyrId MAGETWO-12415
 */
class PaypalExpressOrder extends OrderCheckout
{
    /**
     * Prepare data for guest checkout using "Checkout with PayPal" button on product page
     */
    protected function _initData()
    {
        $this->checkoutFixture = Factory::getFixtureFactory()->getMagentoCheckoutPaypalExpress();
        //Verification data
        $this->_data = [
            'totals' => [
                'grand_total' => '10.83'
            ]
        ];
    }

    /**
     * Persists prepared data into application
     */
    public function persist()
    {
        $this->checkoutFixture->persist();
        if (!is_null($this->additionalProducts)) {
            foreach ($this->additionalProducts as $product) {
                $this->checkoutFixture->addProduct($product);
            }
        }
        $this->orderId = Factory::getApp()->magentoCheckoutCreatePaypalExpressOrder($this->checkoutFixture);
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
     * @param int $index
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
