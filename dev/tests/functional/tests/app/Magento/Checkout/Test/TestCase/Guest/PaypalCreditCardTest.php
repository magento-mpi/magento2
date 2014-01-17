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

namespace Magento\Checkout\Test\TestCase\Guest;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Checkout\Test\Fixture\Checkout;
use Magento\Checkout\Test\Page\CheckoutOnepage;
use Magento\Payment\Test\Block\Form\PayflowAdvanced\Cc;

/**
 * Class PaypalCreditCardTest
 *
 * Test one page checkout with PayPal credit card payments (payments advanced and payflow link).
 *
 * @package Magento\Test\TestCase\Guest
 */
class PaypalCreditCardTest extends Functional
{
    /**
     * Guest checkout using PayPal payment method specified by the dataprovider.
     *
     * @param Checkout $fixture
     * @param string $formBlockFunction
     * @dataProvider dataProviderCheckout
     *
     * @ZephyrId MAGETWO-12991, MAGETWO-12974
     */
    public function testOnepageCheckout(Checkout $fixture, $formBlockFunction)
    {
        $this->markTestSkipped('Bamboo inability to run tests on instance with public IP address');
        $fixture->persist();

        //Ensure shopping cart is empty
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->open();
        $checkoutCartPage->getCartBlock()->clearShoppingCart();

        //Add products to cart
        $products = $fixture->getProducts();
        foreach ($products as $product) {
            $productPage = Factory::getPageFactory()->getCatalogProductView();
            $productPage->init($product);
            $productPage->open();
            $productPage->getViewBlock()->addToCart($product);
            Factory::getPageFactory()->getCheckoutCart()->getMessageBlock()->assertSuccessMessage();
        }

        //Proceed to checkout
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->getCartBlock()->getOnepageLinkBlock()->proceedToCheckout();

        //Proceed Checkout
        /** @var \Magento\Checkout\Test\Page\CheckoutOnepage $checkoutOnePage */
        $checkoutOnePage = Factory::getPageFactory()->getCheckoutOnepage();
        $checkoutOnePage->getLoginBlock()->checkoutMethod($fixture);
        $checkoutOnePage->getBillingBlock()->fillBilling($fixture);
        $checkoutOnePage->getShippingMethodBlock()->selectShippingMethod($fixture);
        $checkoutOnePage->getPaymentMethodsBlock()->selectPaymentMethod($fixture);
        $checkoutOnePage->getReviewBlock()->placeOrder();

        /** @var \Magento\Payment\Test\Block\Form\PayflowAdvanced\Cc $formBlock */
        $formBlock = call_user_func_array(array($this, $formBlockFunction), array($checkoutOnePage));
        $formBlock->fill($fixture);
        $formBlock->pressContinue();

        //Verify order in Backend
        $successPage = Factory::getPageFactory()->getCheckoutOnepageSuccess();
        $this->assertContains(
            'Your order has been received.',
            $successPage->getTitleBlock()->getTitle(),
            'Order success page was not opened.');
        $orderId = $successPage->getSuccessBlock()->getOrderId($fixture);
        $this->_verifyOrder($orderId, $fixture);
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
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));
        $this->assertContains(
            $fixture->getGrandTotal(),
            Factory::getPageFactory()->getSalesOrderView()->getOrderTotalsBlock()->getGrandTotal(),
            'Incorrect grand total value for the order #' . $orderId
        );

        if ($fixture->getCommentHistory()) {
            $expectedAuthorizedAmount = $fixture->getCommentHistory();
        } else {
            $expectedAuthorizedAmount = 'Authorized amount of ' . $fixture->getGrandTotal();
        }
        $this->assertContains(
            $expectedAuthorizedAmount,
            Factory::getPageFactory()->getSalesOrderView()->getOrderHistoryBlock()->getCommentsHistory(),
            'Incorrect authorized amount value for the order #' . $orderId
        );
    }

    /**
     * Data providers for checking out
     *
     * @return array
     */
    public function dataProviderCheckout()
    {
        return array(
            array(Factory::getFixtureFactory()->getMagentoCheckoutGuestPaypalAdvanced(),
                  'getPayflowAdvancedCcBlock'),
            array(Factory::getFixtureFactory()->getMagentoCheckoutGuestPaypalPayflowLink(),
                  'getPayflowLinkCcBlock')
        );
    }

    /**
     * Return the block associated with the PayPal Payments Advanced credit card form.
     *
     * @param CheckoutOnepage $checkoutOnePage
     * @return Cc
     */
    public function getPayflowAdvancedCcBlock(CheckoutOnepage $checkoutOnePage) {
        return $checkoutOnePage->getPayflowAdvancedCcBlock();
    }

    /**
     * Return the block associated with the PayPal Payflow Link credit card form.
     *
     * @param CheckoutOnepage $checkoutOnePage
     * @return Cc
     */
    public function getPayflowLinkCcBlock(CheckoutOnepage $checkoutOnePage) {
        return $checkoutOnePage->getPayflowLinkCcBlock();
    }
}
