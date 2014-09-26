<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Constraint;

use Mtf\Client\Browser;
use Mtf\Fixture\FixtureInterface;
use Mtf\Constraint\AbstractConstraint;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Class AssertGiftCardAccountIsNotUsableInCartOnFrontend
 * Assert that gift card is not usable in cart on frontend
 */
class AssertGiftCardAccountIsNotUsableInCartOnFrontend extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that gift card is not usable in cart on frontend
     *
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @param FixtureInterface $product
     * @param Browser $browser
     * @param string $code
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart,
        FixtureInterface $product,
        Browser $browser,
        $code
    ) {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $catalogProductView->getViewBlock()->clickAddToCart();
        $checkoutCart->getGiftCardAccountBlock()->addGiftCard($code);

        \PHPUnit_Framework_Assert::assertTrue(
            $checkoutCart->getMessagesBlock()->assertErrorMessage(),
            'Gift card is usable on frontend.'
        );
    }

    /**
     * Success assert that gift card is not usable in cart on frontend
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift card is not usable in cart on frontend.';
    }
}
