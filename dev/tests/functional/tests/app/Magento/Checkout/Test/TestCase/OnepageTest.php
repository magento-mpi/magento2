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
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class OnepageTest
 * Test one page checkout with different configurations
 *
 * @package Magento\Test\TestCase\Guest
 */
class OnepageTest extends Functional
{
    /**
     * Place order on frontend via one page checkout.
     *
     * @param Checkout $fixture
     * @dataProvider dataProviderOnepageCheckout
     */
    public function testOnepageCheckout(Checkout $fixture)
    {
        $fixture->persist();
        //Add products to cart
        $products = $fixture->getProducts();
        foreach ($products as $product) {
            $productPage = Factory::getPageFactory()->getCatalogProductView();
            $productPage->init($product);
            $productPage->open();
            $productPage->getViewBlock()->addToCart($product);
        }

        //Proceed to checkout
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->getCartBlock()->getOnepageLinkBlock()->proceedToCheckout();

        //Proceed Checkout
        $checkoutOnePage = Factory::getPageFactory()->getCheckoutOnepage();
        $checkoutOnePage->getLoginBlock()->checkoutMethod($fixture);
        $checkoutOnePage->getBillingBlock()->fillBilling($fixture);
        $checkoutOnePage->getShippingMethodBlock()->selectShippingMethod($fixture);
        $checkoutOnePage->getPaymentMethodsBlock()->selectPaymentMethod($fixture);
        $checkoutOnePage->getReviewBlock()->placeOrder();

        //Verify order in Backend TODO assert constraints
        $orderId = Factory::getPageFactory()->getCheckoutOnepageSuccess()->getSuccessBlock()->getOrderId($fixture);
        Factory::getApp()->magentoBackendLoginUser();
        $orderPage = Factory::getPageFactory()->getAdminSalesOrder();
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));
        $this->assertContains(
            $fixture->getGrandTotal(),
            Factory::getPageFactory()->getAdminSalesOrderView()->getOrderTotalsBlock()->getGrandTotal(),
            'Incorrect grand total value for the order #' . $orderId
        );

        if ($fixture->getData('totals/comment_history')) {
            $expectedAuthorizedAmount = $fixture->getData('totals/comment_history');
        } else {
            $expectedAuthorizedAmount = 'Authorized amount of ' . $fixture->getGrandTotal();
        }

        $actualAuthorizedAmount = Factory::getPageFactory()->getAdminSalesOrderView()->getOrderHistoryBlock()->getAuthorizedAmount();
        $this->assertContains($expectedAuthorizedAmount, $actualAuthorizedAmount, 'Incorrect authorized amount value for the order #' . $orderId);
    }

    /**
     * @return array
     */
    public static function dataProviderOnepageCheckout()
    {
        return array(
//            array(Factory::getFixtureFactory()->getMagentoCheckoutGuestAuthorizenet()),
//            array(Factory::getFixtureFactory()->getMagentoCheckoutGuestPaypalDirect()),
//            array(Factory::getFixtureFactory()->getMagentoCheckoutPaypalPayflowPro()),
            array(Factory::getFixtureFactory()->getMagentoCheckoutGuestPayPalPayflow())
        );
    }
}
