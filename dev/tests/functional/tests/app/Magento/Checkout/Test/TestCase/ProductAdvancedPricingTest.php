<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Catalog\Test\Fixture\Product;
use Magento\Checkout\Test\Fixture\SpecialPriceCheckMoneyOrder;

/**
 * Class ProductAdvancedPricingTest
 * Test checking out with a product that has special prices
 *
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
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCartIndex();
        $checkoutCartPage->open();
        $checkoutCartPage->getCartBlock()->clearShoppingCart();

        // Frontend
        // Login with customer created in checkout fixture
        $customerAccountLoginPage = Factory::getPageFactory()->getCustomerAccountLogin();
        $customerAccountLoginPage->open();
        $customerAccountLoginPage->getLoginBlock()->login($checkoutFixture->getCustomer());

        // Add simple & configurable products with special price to cart
        $this->addProductToCart($simpleProduct);
        $this->addProductToCart($configurableProduct);

        // Verifying
        // Verify unit price and sub-total for each item in the cart
        $this->verifyCartItem($simpleProduct);
        $this->verifyCartItem($configurableProduct);

        // Proceed to one page checkout
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCartIndex();
        $checkoutCartPage->getCartBlock()->getOnepageLinkBlock()->proceedToCheckout();

        // Place order
        // Use customer from checkout fixture
        $checkoutOnePage = Factory::getPageFactory()->getCheckoutOnepage();
        $checkoutOnePage->open();
        $billingAddress = $checkoutFixture->getBillingAddress();
        $checkoutOnePage->getBillingBlock()->fillBilling($billingAddress);
        $checkoutOnePage->getBillingBlock()->clickContinue();
        $shippingMethod = $checkoutFixture->getShippingMethods()->getData('fields');
        $checkoutOnePage->getShippingMethodBlock()->selectShippingMethod($shippingMethod);
        $checkoutOnePage->getShippingMethodBlock()->clickContinue();
        $payment = [
            'method' => $checkoutFixture->getPaymentMethod()->getPaymentCode(),
            'dataConfig' => $checkoutFixture->getPaymentMethod()->getDataConfig(),
            'credit_card' => $checkoutFixture->getCreditCard(),
        ];
        $checkoutOnePage->getPaymentMethodsBlock()->selectPaymentMethod($payment);
        $checkoutOnePage->getPaymentMethodsBlock()->clickContinue();
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
        Factory::getClientBrowser()->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $productPage->getViewBlock()->addToCart($product);
        // Make sure the item is added to the cart before continuing on.
        Factory::getPageFactory()->getCheckoutCartIndex()->getMessagesBlock()->waitSuccessMessage();
    }

    /**
     * Verifies the unit price and subtotal for cart item.
     *
     * @param Product $product
     */
    private function verifyCartItem(Product $product)
    {
        $productName = $product->getName();
        $specialPrice = $product->getProductSpecialPrice();
        $cartItem = Factory::getPageFactory()->getCheckoutCartIndex()->getCartBlock()->getCartItem($product);
        $unitPrice = $cartItem->getPrice();
        $subTotal = $cartItem->getSubtotalPrice();

        $this->assertEquals(
            $specialPrice,
            $unitPrice,
            'Incorrect unit price for ' . $productName
        );
        $this->assertEquals(
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
        $orderPage->getOrderGridBlock()->searchAndOpen(['id' => $orderId]);

        // Validate each of the products.
        $itemOrderedBlock = Factory::getPageFactory()->getSalesOrderView()->getItemsOrderedBlock();
        foreach ($checkoutFixture->getProducts() as $product) {
            $specialPrice = $product->getProductSpecialPrice();
            $this->assertContains(
                $specialPrice,
                $itemOrderedBlock->getPrice($product),
                'Incorrect price for item ' . $product->getName()
            );
        }
    }
}
