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

namespace Magento\Checkout\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Catalog\Test\Fixture\ConfigurableProduct;
use Magento\Catalog\Test\Fixture\Product;
use Magento\Checkout\Test\Fixture\SpecialPriceCheckMoneyOrder;


/**
 * Class ProductAdvancedPricingTest
 * Test checking out with a product that has special prices
 *
 * @package Magento\Test\TestCase
 */
class ProductAdvancedPricingTest extends Functional
{
    /**
     * Place order on frontend that contains a product with special prices.
     *
     * @ZephyrId MAGETWO-12429
     */
    public function testProductSpecialPriceCheckout()
    {
        $checkoutFixture = Factory::getFixtureFactory()->getMagentoCheckoutSpecialPriceCheckMoneyOrder();
        // Persist Checkout fixture data for this test
        // Preconditions are set in checkout fixture
        $checkoutFixture->persist();

        // Get products from checkout fixture
        $simpleProduct = $checkoutFixture->getSimpleProduct();
        $configurableProduct = $checkoutFixture->getConfigurableProduct();

        // Steps
        // Ensure shopping cart is empty
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->open();
        $checkoutCartPage->getCartBlock()->clearShoppingCart();
        // Add simple & configurable products with special price to cart
        $this->addProductToCart($simpleProduct);
        $this->addProductToCart($configurableProduct);

        // Verifying
        // Verify unit price and sub-total for each item in the cart
        $this->verifyCartItem($simpleProduct);
        $this->verifyCartItem($configurableProduct);

        // Frontend
        // Proceed to one page checkout
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->getCartBlock()->getOnepageLinkBlock()->proceedToCheckout();
        // Place order
        // Use customer from checkout fixture
        $checkoutOnePage = Factory::getPageFactory()->getCheckoutOnepage();
        $checkoutOnePage->getLoginBlock()->checkoutMethod($checkoutFixture);
        $checkoutOnePage->getBillingBlock()->fillBilling($checkoutFixture);
        $checkoutOnePage->getShippingMethodBlock()->selectShippingMethod($checkoutFixture);
        $checkoutOnePage->getPaymentMethodsBlock()->selectPaymentMethod($checkoutFixture);
        $checkoutOnePage->getReviewBlock()->placeOrder();

        //Verify order in Backend
        $successPage = Factory::getPageFactory()->getCheckoutOnepageSuccess();
        $orderId = $successPage->getSuccessBlock()->getOrderId($checkoutFixture);
        $this->verifyOrderOnBackend($orderId, $checkoutFixture);
    }

    /**
     * Add a product to the cart.
     *
     * @param Product $product
     */
    private function addProductToCart(Product $product)
    {
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productPage->init($product);
        $productPage->open();
        $productPage->getViewBlock()->addToCart($product);
        // Make sure the item is added to the cart before continuing on.
        Factory::getPageFactory()->getCheckoutCart()->getMessageBlock()->assertSuccessMessage();
    }

    /**
     * Verifies the unit price and subtotal for cart item.
     *
     * @param Product|ConfigurableProduct $product
     */
    private function verifyCartItem(Product $product)
    {
        $productName = $product->getProductName();
        $specialPrice = $product->getProductSpecialPrice();

        $productOptions = array();
        if ($product instanceof ConfigurableProduct) {
            $productOptions = $product->getProductOptions();
        }

        if (!empty($productOptions)) {
            // Working with a configurable product. Make sure we find the correct item in the cart.
            // This test deals with the first option being selected at checkout.
            $productName = $product->getProductName()
                . ' ' . key($productOptions)
                . ' ' . current($productOptions);
        }

        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();

        $unitPrice = $checkoutCartPage->getCartBlock()->getCartItemUnitPrice($productName);
        $subTotal = $checkoutCartPage->getCartBlock()->getCartItemSubTotal($productName);
        $this->assertContains(
            $specialPrice,
            $unitPrice,
            'Incorrect unit price for ' . $productName
        );
        $this->assertContains(
            $specialPrice,
            $subTotal,
            'Incorrect sub-total for ' . $productName
        );
    }

    /**
     * Verifies order in Backend.  Checks order data (price) against products in order.
     *
     * @param string $orderId
     * @param SpecialPriceCheckMoneyOrder $checkoutFixture
     */
    protected function verifyOrderOnBackend($orderId, SpecialPriceCheckMoneyOrder $checkoutFixture)
    {
        Factory::getApp()->magentoBackendLoginUser();
        $orderPage = Factory::getPageFactory()->getSalesOrder();
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));

        // Validate each of the products.
        foreach ($checkoutFixture->getProducts() as $product) {
            $specialPrice = $product->getProductSpecialPrice();
            $this->assertContains(
                $specialPrice,
                Factory::getPageFactory()->getSalesOrderView()->getItemsOrderedBlock()->getPrice($product),
                'Incorrect price for item ' . $product->getProductName()
            );
        }
    }
}
