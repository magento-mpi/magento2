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
use Magento\GiftCardAccount\Test\Page\Adminhtml\Index;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\GiftCardAccount\Test\Fixture\GiftCardAccount;

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
     * @param Index $index
     * @param FixtureInterface $product
     * @param GiftCardAccount $giftCardAccount
     * @param Browser $browser
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart,
        Index $index,
        FixtureInterface $product,
        GiftCardAccount $giftCardAccount,
        Browser $browser
    ) {
        if ($giftCardAccount->hasData('code')) {
            $value = $giftCardAccount->getCode();
        } else {
            $index->open();
            $filter = ['balance' => $giftCardAccount->getBalance()];
            $value = $index->getGiftCardAccount()->getCode($filter, false);
        }

        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $catalogProductView->getViewBlock()->clickAddToCart();
        $checkoutCart->getGiftCardAccountBlock()->addGiftCard($value);

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
