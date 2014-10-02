<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Centinel\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class CentinelPaymentsTestAbstract
 * Test for 3D Secure card validation
 *
 */
abstract class AbstractCentinelPaymentsTest extends Functional
{
    /**
     * Ensure shopping cart is empty
     */
    protected function clearShoppingCart()
    {
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCartIndex();
        $checkoutCartPage->open();
        $checkoutCartPage->getCartBlock()->clearShoppingCart();
    }

    /**
     * Add products to cart
     *
     * @param Checkout $fixture
     */
    protected function _addProducts(Checkout $fixture)
    {
        $products = $fixture->getProducts();
        foreach ($products as $product) {
            $productPage = Factory::getPageFactory()->getCatalogProductView();
            Factory::getClientBrowser()->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
            $productPage->getViewBlock()->addToCart($product);
            Factory::getPageFactory()->getCheckoutCartIndex()->getMessagesBlock()->waitSuccessMessage();
        }
    }

    /**
     * Process Magento Checkout
     *
     * @param Checkout $fixture
     */
    protected function _magentoCheckoutProcess(Checkout $fixture)
    {
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCartIndex();
        $checkoutCartPage->getCartBlock()->getOnepageLinkBlock()->proceedToCheckout();

        //Proceed Checkout
        /** @var \Magento\Checkout\Test\Page\CheckoutOnepage $checkoutOnePage */
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
    }

    /**
     * Submit 3D Secure Verification form
     */
    protected function _submitCc(Checkout $fixture)
    {
        /** @var \Magento\Checkout\Test\Page\CheckoutOnepage $checkoutOnePage */
        $checkoutOnePage = Factory::getPageFactory()->getCheckoutOnepage();
        /** @var  \Magento\Centinel\Test\Block\Authentication $centinelBlock */
        $centinelBlock = $checkoutOnePage->getCentinelAuthenticationBlock();
        $centinelBlock->verifyCard($fixture);
    }

    /**
     * Validate Success Submitting 3D Secure Verification form
     */
    protected function _validateCc(Checkout $fixture)
    {
        $this->_submitCc($fixture);
        /** @var \Magento\Checkout\Test\Page\CheckoutOnepage $checkoutOnePage */
        $checkoutOnePage = Factory::getPageFactory()->getCheckoutOnepage();
        $checkoutOnePage->getReviewBlock()->waitForCardValidation();
    }

    /**
     * Get Message after failed submit 3D Secure Verification form
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getFailedMessage(Checkout $fixture)
    {
        /** @var \Magento\Checkout\Test\Page\CheckoutOnepage $checkoutOnePage */
        $checkoutOnePage = Factory::getPageFactory()->getCheckoutOnepage();
        $checkoutOnePage->getReviewBlock();
        /** @var  \Magento\Centinel\Test\Block\Authentication $centinelBlock */
        $centinelBlock = $checkoutOnePage->getCentinelAuthenticationBlock();
        return $centinelBlock->getText();
    }

    /**
     * Submit order on Order Review page
     */
    protected function _placeOrder()
    {
        /** @var \Magento\Checkout\Test\Page\CheckoutOnepage $checkoutOnePage */
        $checkoutOnePage = Factory::getPageFactory()->getCheckoutOnepage();
        $checkoutOnePage->getReviewBlock()->placeOrder();
    }

    /**
     * Verify created order
     *
     * @param Checkout $fixture
     */
    protected function _verifyOrder(Checkout $fixture)
    {
        //Order placed
        $successPage = Factory::getPageFactory()->getCheckoutOnepageSuccess();
        $orderId = $successPage->getSuccessBlock()->getOrderId($fixture);

        //Check order data on backend
        Factory::getApp()->magentoBackendLoginUser();
        $orderPage = Factory::getPageFactory()->getSalesOrder();
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));

        $this->assertContains(
            $fixture->getGrandTotal(),
            Factory::getPageFactory()->getSalesOrderView()->getOrderTotalsBlock()->getGrandTotal(),
            'Incorrect grand total value for the order #' . $orderId
        );

        $this->assertContains(
            $fixture->getVerificationResult(),
            Factory::getPageFactory()->getSalesOrderView()->getOrderInfoBlock()->getVerificationResult(),
            'Incorrect "3D Secure Verification Result" the order #' . $orderId
        );

        $this->assertContains(
            $fixture->getCardholderValidation(),
            Factory::getPageFactory()->getSalesOrderView()->getOrderInfoBlock()->getCardholderValidation(),
            'Incorrect "3D Secure Cardholder Validation" for the order #' . $orderId
        );

        $this->assertContains(
            $fixture->getEcommerceIndicator(),
            Factory::getPageFactory()->getSalesOrderView()->getOrderInfoBlock()->getEcommerceIndicator(),
            'Incorrect "3D Secure Electronic Commerce Indicator" for the order #' . $orderId
        );
    }

    /**
     * Create Customer
     *
     * @param Checkout $fixture
     */
    protected function _createCustomer(Checkout $fixture)
    {
        //Data
        $customer = $fixture->getCustomer();

        //Page
        $homePage = Factory::getPageFactory()->getCmsIndexIndex();
        $createPage = Factory::getPageFactory()->getCustomerAccountCreate();
        $accountIndexPage = Factory::getPageFactory()->getCustomerAccountIndex();
        $addressEditPage = Factory::getPageFactory()->getCustomerAddressEdit();

        //Create Account
        $homePage->open();
        $topLinks = $homePage->getLinksBlock();
        $topLinks->openLink('Register');

        $createPage->getRegisterForm()->registerCustomer($customer);

        //Set Billing Address
        $accountIndexPage->getDashboardAddress()->editBillingAddress();
        $addressEditPage->getEditForm()->editCustomerAddress($customer->getAddressData());

        //Log Out
        $homePage->getLinksBlock()->openLink('Log Out');
    }
}
