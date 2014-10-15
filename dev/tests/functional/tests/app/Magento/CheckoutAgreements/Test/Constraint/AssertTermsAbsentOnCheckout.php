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
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\CheckoutAgreements\Test\Fixture\CheckoutAgreement;

/**
 * Class AssertTermsAbsentOnCheckout
 * Check that Checkout Agreement is absent in the Place order tab
 */
class AssertTermsAbsentOnCheckout extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Place order and verify there is no checkbox Terms and Conditions
     *
     * @param FixtureFactory $fixtureFactory
     * @param ObjectManager $objectManager
     * @param string $products
     * @param Browser $browser
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @param CheckoutOnepage $checkoutOnepage
     * @param CheckoutAgreement $conditions
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
        CheckoutAgreement $conditions
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

        \PHPUnit_Framework_Assert::assertFalse(
            $checkoutOnepage->getRewardReviewBlock()->checkAgreement(),
            'Checkout Agreement \'' . $conditions->getName() . '\' is not present in the Place order tab.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Checkout Agreement is absent in the Place order tab.';
    }
}
