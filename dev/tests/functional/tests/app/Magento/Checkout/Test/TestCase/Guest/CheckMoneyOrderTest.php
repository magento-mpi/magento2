<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\TestCase\Guest;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Checkout\Test\Fixture\CheckMoneyOrder;
use Magento\Catalog\Test\Fixture;
use Magento\Catalog\Test\Block\Product;
use Magento\Checkout\Test\Block;
use Magento\Checkout\Test\Block\Onepage;

/**
 * Class CheckMoneyOrderTest
 * Guest checkout with Check/Money Order payment method and offline shipping method
 *
 */
class CheckMoneyOrderTest extends Functional
{
    /**
     * Place order on frontend via one page checkout.
     *
     * @ZephyrId MAGETWO-12412
     */
    public function testOnepageCheckout()
    {
        //Data
        $fixture = Factory::getFixtureFactory()->getMagentoCheckoutCheckMoneyOrder();
        $fixture->persist();

        //Steps and verification
        $this->addProducts($fixture);
        $this->checkoutProcess($fixture);
        $orderId = $this->findOrderId($fixture);
        $this->verifyOrderOnBackend($orderId, $fixture);
    }

    /**
     * Add products to cart
     *
     * @param CheckMoneyOrder $fixture
     */
    protected function addProducts(CheckMoneyOrder $fixture)
    {
        //Ensure shopping cart is empty
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->open();
        $checkoutCartPage->getCartBlock()->clearShoppingCart();

        $products = $fixture->getProducts();
        foreach ($products as $product) {
            $productPage = Factory::getPageFactory()->getCatalogProductView();
            $productPage->init($product);
            $productPage->open();
            $productPage->getViewBlock()->addToCart($product);
            $cartPage = Factory::getPageFactory()->getCheckoutCart();
            $cartPage->getMessagesBlock()->assertSuccessMessage();
            $this->checkProductPrice($fixture, $product, $cartPage->getCartBlock());
        }
    }

    /**
     * Process Magento Checkout
     *
     * @param CheckMoneyOrder $fixture
     */
    protected function checkoutProcess(CheckMoneyOrder $fixture)
    {
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->getCartBlock()->getOnepageLinkBlock()->proceedToCheckout();

        //Proceed Checkout
        $checkoutOnePage = Factory::getPageFactory()->getCheckoutOnepage();
        $checkoutOnePage->getLoginBlock()->checkoutMethod($fixture);
        $checkoutOnePage->getBillingBlock()->fillBilling($fixture);
        $checkoutOnePage->getShippingMethodBlock()->selectShippingMethod($fixture);
        $checkoutOnePage->getPaymentMethodsBlock()->selectPaymentMethod($fixture);
        $reviewBlock = $checkoutOnePage->getReviewBlock();
        $this->verifyOrderOnReview($reviewBlock, $fixture);
        $reviewBlock->placeOrder();
    }

    /**
     * Check product price in cart
     *
     * @param CheckMoneyOrder $fixture
     * @param Fixture\Product $product
     * @param Block\Cart $block
     */
    protected function checkProductPrice(CheckMoneyOrder $fixture, Fixture\Product $product, Block\Cart $block)
    {
        $expected = $fixture->getProductPriceWithTax($product);
        $this->assertEquals($expected, $block->getProductPriceByName($product->getProductName()));
    }

    /**
     * Find order id on the result page
     *
     * @param CheckMoneyOrder $fixture
     * @return string
     */
    protected function findOrderId(CheckMoneyOrder $fixture)
    {
        $successPage = Factory::getPageFactory()->getCheckoutOnepageSuccess();
        $this->assertContains(
            'Your order has been received.',
            $successPage->getTitleBlock()->getTitle(),
            'Order success page was not opened.'
        );
        return $successPage->getSuccessBlock()->getOrderId($fixture);
    }

    /**
     * Verify order in Backend
     *
     * @param string $orderId
     * @param CheckMoneyOrder $fixture
     */
    protected function verifyOrderOnBackend($orderId, CheckMoneyOrder $fixture)
    {
        Factory::getApp()->magentoBackendLoginUser();
        $orderPage = Factory::getPageFactory()->getSalesOrder();
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));

        $orderTotalsBlock = Factory::getPageFactory()->getSalesOrderView()->getOrderTotalsBlock();
        $this->assertContains(
            $fixture->getGrandTotal(),
            $orderTotalsBlock->getGrandTotal(),
            'Incorrect grand total value for the order #' . $orderId
        );
    }

    /**
     * Verify totals in order review
     *
     * @param Onepage\Review $block
     * @param CheckMoneyOrder $fixture
     */
    protected function verifyOrderOnReview(Onepage\Review $block, CheckMoneyOrder $fixture)
    {
        $this->assertContains(
            $fixture->getSubtotal(),
            $block->getSubtotal(),
            'Incorrect subtotal value for the order'
        );

        $this->assertContains(
            $fixture->getTax(),
            $block->getTax(),
            'Incorrect total tax value for the order'
        );
    }
}
