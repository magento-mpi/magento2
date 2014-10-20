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
     * @param string $product
     * @param Browser $browser
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @param CheckoutOnepage $checkoutOnepage
     * @param CheckoutAgreement $agreement
     * @param array $shipping
     * @param array $payment
     * @param CheckoutAgreement|null $agreementOrigin
     * @return void
     */
    public function processAssert(
        FixtureFactory $fixtureFactory,
        ObjectManager $objectManager,
        $product,
        Browser $browser,
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart,
        CheckoutOnepage $checkoutOnepage,
        CheckoutAgreement $agreement,
        $shipping,
        $payment,
        CheckoutAgreement $agreementOrigin = null
    ) {
        $createProductsStep = $objectManager->create(
            'Magento\Catalog\Test\TestStep\CreateProductsStep',
            ['products' => $product]
        );
        $product = $createProductsStep->run();

        $billingAddress = $fixtureFactory->createByCode('addressInjectable', ['dataSet' => 'default']);

        $browser->open($_ENV['app_frontend_url'] . $product['products'][0]->getUrlKey() . '.html');
        $catalogProductView->getViewBlock()->clickAddToCartButton();
        $checkoutCart->getCartBlock()->getOnepageLinkBlock()->proceedToCheckout();
        $checkoutOnepage->getLoginBlock()->guestCheckout();
        $checkoutOnepage->getLoginBlock()->clickContinue();
        $checkoutOnepage->getBillingBlock()->fill($billingAddress);
        $checkoutOnepage->getBillingBlock()->clickContinue();
        $checkoutOnepage->getShippingMethodBlock()->selectShippingMethod($shipping);
        $checkoutOnepage->getShippingMethodBlock()->clickContinue();
        $checkoutOnepage->getPaymentMethodsBlock()->selectPaymentMethod($payment);
        $checkoutOnepage->getPaymentMethodsBlock()->clickContinue();

        $agreement = ($agreementOrigin !== null) ? $agreementOrigin : $agreement;
        \PHPUnit_Framework_Assert::assertFalse(
            $checkoutOnepage->getAgreementReview()->checkAgreement($agreement),
            'Checkout Agreement \'' . $agreement->getName() . '\' is present in the Place order step.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Checkout Agreement is absent in the Place order step.';
    }
}
