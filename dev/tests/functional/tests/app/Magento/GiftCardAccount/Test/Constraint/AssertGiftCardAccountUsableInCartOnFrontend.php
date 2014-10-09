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
 * Class AssertGiftCardAccountUsableInCartOnFrontend
 * Assert that gift card usable in frontend
 */
class AssertGiftCardAccountUsableInCartOnFrontend extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that gift card usable in frontend
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
            $checkoutCart->getMessagesBlock()->waitSuccessMessage(),
            'Gift card is not usable on frontend.'
        );

    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift card usable in frontend.';
    }
}
