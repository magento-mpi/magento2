<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CheckoutAgreements\Test\Constraint;

use Mtf\ObjectManager;
use Mtf\Client\Browser;
use Mtf\Fixture\FixtureFactory;
use Mtf\Constraint\AbstractConstraint;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Checkout\Test\Page\CheckoutOnepage;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Checkout\Test\Page\CheckoutOnepageSuccess;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Checkout\Test\Constraint\AssertOrderSuccessPlacedMessage;

/**
 * Class AssertTermsOnCheckout
 * Check that checkbox is present on the last checkout step - Order Review and check order successfully placed message.
 * Check that Place order without click on checkbox Terms and Conditions on order review step was not order
 * successfully placed.
 */
class AssertTermsOnCheckout extends AbstractConstraint
{
    const NOTIFICATION_MESSAGE = 'This is a required field.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert checkbox is present on the last checkout step - Order Review.
     * Click on checkbox and place order. Assert order successfully placed message.
     *
     * @param FixtureFactory $fixtureFactory
     * @param ObjectManager $objectManager
     * @param string $products
     * @param Browser $browser
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @param CheckoutOnepage $checkoutOnepage
     * @param CheckoutOnepageSuccess $checkoutOnepageSuccess
     * @param AssertOrderSuccessPlacedMessage $assertOrderSuccessPlacedMessage
     * @return void
     */
    public function processAssert(
        FixtureFactory $fixtureFactory,
        ObjectManager $objectManager,
        $products,
        Browser $browser,
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart,
        CheckoutOnepage $checkoutOnepage,
        CheckoutOnepageSuccess $checkoutOnepageSuccess,
        AssertOrderSuccessPlacedMessage $assertOrderSuccessPlacedMessage
    ) {
        $products = $objectManager->create(
            'Magento\Catalog\Test\TestStep\CreateProductsStep',
            ['products' => $products]
        );
        $product = $products->run();

        $billingAddress = $fixtureFactory->createByCode('addressInjectable', ['dataSet' => 'default']);

        $browser->open($_ENV['app_frontend_url'] . $product['products'][0]->getUrlKey() . '.html');
        $catalogProductView->getViewBlock()->clickAddToCartButton();
        $checkoutCart->getCartBlock()->getOnepageLinkBlock()->proceedToCheckout();
        $checkoutOnepage->getLoginBlock()->guestCheckout();
        $checkoutOnepage->getLoginBlock()->clickContinue();
        $checkoutOnepage->getBillingBlock()->fill($billingAddress);
        $checkoutOnepage->getBillingBlock()->clickContinue();
        $checkoutOnepage->getShippingMethodBlock()->clickContinue();
        $checkoutOnepage->getPaymentMethodsBlock()->clickContinue();
        $checkoutOnepage->getRewardReviewBlock()->placeOrder();

        \PHPUnit_Framework_Assert::assertEquals(
            self::NOTIFICATION_MESSAGE,
            $checkoutOnepage->getRewardReviewBlock()->getNotificationMassage(),
            'Wrong notification message is displayed.'
        );

        $checkoutOnepage->getRewardReviewBlock()->setAgreement('Yes');
        $checkoutOnepage->getRewardReviewBlock()->placeOrder();
        $assertOrderSuccessPlacedMessage->processAssert($checkoutOnepageSuccess);
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Order successfully placed message.';
    }
}
