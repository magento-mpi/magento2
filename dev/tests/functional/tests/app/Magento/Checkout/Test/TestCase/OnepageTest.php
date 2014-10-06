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
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class OnepageTest
 * Test one page checkout with different configurations
 *
 */
class OnepageTest extends Functional
{
    /**
     * Place order on frontend via one page checkout.
     *
     * @param Checkout $fixture
     * @dataProvider dataProviderOnepageCheckout
     *
     * @ZephyrId MAGETWO-12832, MAGETWO-12968, MAGETWO-12994
     */
    public function testOnepageCheckout(Checkout $fixture)
    {
        $fixture->persist();

        //Ensure shopping cart is empty
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCartIndex();
        $checkoutCartPage->open();
        $checkoutCartPage->getCartBlock()->clearShoppingCart();

        //Add products to cart
        $products = $fixture->getProducts();
        foreach ($products as $product) {
            $productPage = Factory::getPageFactory()->getCatalogProductView();
            Factory::getClientBrowser()->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
            $productPage->getViewBlock()->addToCart($product);
            Factory::getPageFactory()->getCheckoutCartIndex()->getMessagesBlock()->waitSuccessMessage();
        }

        //Proceed to checkout
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCartIndex();
        $checkoutCartPage->getCartBlock()->getOnepageLinkBlock()->proceedToCheckout();

        //Proceed Checkout
        $checkoutOnePage = Factory::getPageFactory()->getCheckoutOnepage();
        $checkoutOnePage->getLoginBlock()->checkoutMethod($fixture);
        $billingAddress = $fixture->getBillingAddress();
        $checkoutOnePage->getBillingBlock()->fillBilling($billingAddress);
        $checkoutOnePage->getBillingBlock()->clickContinue();
        $shippingMethod = $fixture->getShippingMethods()->getData('fields');
        $checkoutOnePage->getShippingMethodBlock()->selectShippingMethod($shippingMethod);
        $checkoutOnePage->getShippingMethodBlock()->clickContinue();
        $payment = [
            'method' => $fixture->getPaymentMethod()->getPaymentCode(),
            'dataConfig' => $fixture->getPaymentMethod()->getDataConfig(),
            'credit_card' => $fixture->getCreditCard(),
        ];
        $checkoutOnePage->getPaymentMethodsBlock()->selectPaymentMethod($payment);
        $checkoutOnePage->getPaymentMethodsBlock()->clickContinue();
        $checkoutOnePage->getReviewBlock()->placeOrder();

        //Verify order in Backend
        $successPage = Factory::getPageFactory()->getCheckoutOnepageSuccess();
        $orderId = $successPage->getSuccessBlock()->getOrderId($fixture);
        $this->_verifyOrder($orderId, $fixture);
    }

    /**
     * @return array
     */
    public function dataProviderOnepageCheckout()
    {
        return [
            [Factory::getFixtureFactory()->getMagentoCheckoutGuestAuthorizenet()],
            [Factory::getFixtureFactory()->getMagentoCheckoutGuestPaypalDirect()],
            [Factory::getFixtureFactory()->getMagentoCheckoutGuestPayPalPayflowPro()],
        ];
    }

    /**
     * Verify order in Backend
     *
     * @param string $orderId
     * @param Checkout $fixture
     */
    protected function _verifyOrder($orderId, Checkout $fixture)
    {
        Factory::getApp()->magentoBackendLoginUser();
        $orderPage = Factory::getPageFactory()->getSalesOrder();
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(['id' => $orderId]);
        $this->assertContains(
            $fixture->getGrandTotal(),
            Factory::getPageFactory()->getSalesOrderView()->getOrderTotalsBlock()->getGrandTotal(),
            'Incorrect grand total value for the order #' . $orderId
        );

        if ($fixture->getCommentHistory()) {
            $expectedAuthorizedAmount = $fixture->getCommentHistory();
        } else {
            $expectedAuthorizedAmount = 'Authorized amount of $' . $fixture->getGrandTotal();
        }
        $this->assertContains(
            $expectedAuthorizedAmount,
            Factory::getPageFactory()->getSalesOrderView()->getOrderHistoryBlock()->getCommentsHistory(),
            'Incorrect authorized amount value for the order #' . $orderId
        );
    }
}
