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

namespace Magento\Centinel\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class CentinelPaymentsTestAbstract
 * Test for 3D Secure card validation
 *
 * @package Magento\Centinel
 */
abstract class AbstractCentinelPaymentsTest extends Functional
{
    /**
     * Add products to cart
     *
     * @param Checkout $fixture
     */
    protected function _addProducts(Checkout $fixture)
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
            Factory::getPageFactory()->getCheckoutCart()->getMessageBlock()->assertSuccessMessage();
        }
    }

    /**
     * Process Magento Checkout
     *
     * @param Checkout $fixture
     */
    protected function _magentoCheckoutProcess(Checkout $fixture)
    {
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->getCartBlock()->getOnepageLinkBlock()->proceedToCheckout();

        //Proceed Checkout
        /** @var \Magento\Checkout\Test\Page\CheckoutOnepage $checkoutOnePage */
        $checkoutOnePage = Factory::getPageFactory()->getCheckoutOnepage();
        $checkoutOnePage->getLoginBlock()->checkoutMethod($fixture);
        $checkoutOnePage->getBillingBlock()->fillBilling($fixture);
        $checkoutOnePage->getShippingMethodBlock()->selectShippingMethod($fixture);
        $checkoutOnePage->getPaymentMethodsBlock()->selectPaymentMethod($fixture);
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
        $this->assertContains(
            'Your order has been received.',
            $successPage->getTitleBlock()->getTitle(),
            'Order success page was not opened.');
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

        $createPage->getCreateForm()->create($customer);

        //Set Billing Address
        $accountIndexPage->getDashboardAddress()->editBillingAddress();
        $addressEditPage->getEditForm()->editCustomerAddress($customer->getAddressData());

        //Log Out
        $customerMenu = $homePage->getCustomerMenuBlock();
        $customerMenu->toggle();
        $customerMenu->openLink('Log Out');
    }
}
