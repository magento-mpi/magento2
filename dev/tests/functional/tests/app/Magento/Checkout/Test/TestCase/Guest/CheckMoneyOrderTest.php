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
use Magento\Checkout\Test\Block\Cart;
use Magento\Catalog\Test\Block\Product;
use Magento\Checkout\Test\Block\Onepage;
use Magento\Catalog\Test\Fixture\Product as FixtureProduct;
use Magento\Checkout\Test\Fixture\CheckMoneyOrder;

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
     * @return void
     */
    protected function addProducts(CheckMoneyOrder $fixture)
    {
        //Ensure shopping cart is empty
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCartIndex();
        $checkoutCartPage->open();
        $checkoutCartPage->getCartBlock()->clearShoppingCart();

        $products = $fixture->getProducts();
        foreach ($products as $product) {
            $productPage = Factory::getPageFactory()->getCatalogProductView();
            Factory::getClientBrowser()->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
            $productPage->getViewBlock()->addToCart($product);
            $cartPage = Factory::getPageFactory()->getCheckoutCartIndex();
            $cartPage->getMessagesBlock()->waitSuccessMessage();
            $this->checkProductPrice($fixture, $product, $cartPage->getCartBlock());
        }
    }

    /**
     * Process Magento Checkout
     *
     * @param CheckMoneyOrder $fixture
     * @return void
     */
    protected function checkoutProcess(CheckMoneyOrder $fixture)
    {
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCartIndex();
        $checkoutCartPage->getCartBlock()->getOnepageLinkBlock()->proceedToCheckout();

        //Proceed Checkout
        $checkoutOnePage = Factory::getPageFactory()->getCheckoutOnepage();
        $checkoutOnePage->getLoginBlock()->checkoutMethod($fixture);
        $billingAddress = $fixture->getBillingAddress();
        $checkoutOnePage->getBillingBlock()->fillBilling($billingAddress);
        $checkoutOnePage->getBillingBlock()->clickContinue();
        if ($fixture instanceof \Magento\Shipping\Test\Fixture\Method) {
            $shippingMethod = $fixture->getData('fields');
        } else {
            $shippingMethod = $fixture->getShippingMethods()->getData('fields');
        }
        $checkoutOnePage->getShippingMethodBlock()->selectShippingMethod($shippingMethod);
        $checkoutOnePage->getShippingMethodBlock()->clickContinue();
        $payment = [
            'method' => $fixture->getPaymentMethod()->getPaymentCode(),
            'dataConfig' => $fixture->getPaymentMethod()->getDataConfig(),
            'credit_card' => $fixture->getCreditCard(),
        ];
        $checkoutOnePage->getPaymentMethodsBlock()->selectPaymentMethod($payment);
        $checkoutOnePage->getPaymentMethodsBlock()->clickContinue();
        $reviewBlock = $checkoutOnePage->getReviewBlock();
        $this->verifyOrderOnReview($reviewBlock, $fixture);
        $reviewBlock->placeOrder();
    }

    /**
     * Check product price in cart
     *
     * @param CheckMoneyOrder $fixture
     * @param FixtureProduct $product
     * @param Cart $block
     * @return void
     */
    protected function checkProductPrice(CheckMoneyOrder $fixture, FixtureProduct $product, Cart $block)
    {
        $expected = $fixture->getProductPriceWithTax($product);
        $this->assertEquals($expected, $block->getCartItem($product)->getPrice());
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
            'Thank you for your purchase!',
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
     * @return void
     */
    protected function verifyOrderOnBackend($orderId, CheckMoneyOrder $fixture)
    {
        Factory::getApp()->magentoBackendLoginUser();
        $orderPage = Factory::getPageFactory()->getSalesOrder();
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(['id' => $orderId]);

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
     * @return void
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
