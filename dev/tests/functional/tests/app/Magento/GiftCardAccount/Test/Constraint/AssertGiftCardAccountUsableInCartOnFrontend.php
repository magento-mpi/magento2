<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Constraint;

use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\GiftCardAccount\Test\Fixture\GiftCardAccount;
use Magento\GiftCardAccount\Test\Page\Adminhtml\Index;
use Mtf\Constraint\AbstractConstraint;
use Mtf\Fixture\FixtureInterface;

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
     * @param Index $index
     * @param FixtureInterface $product
     * @param GiftCardAccount $giftCardAccount
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart,
        Index $index,
        FixtureInterface $product,
        GiftCardAccount $giftCardAccount
    ) {
        $index->open();
        $filter = ['balance' => $giftCardAccount->getBalance()];
        $value = $index->getGiftCardAccount()->getCode($filter, false);

        $catalogProductView->init($product);
        $catalogProductView->open();
        $catalogProductView->getViewBlock()->clickAddToCart();
        $checkoutCart->getGiftCardAccountBlock()->addGiftCard($value);

        \PHPUnit_Framework_Assert::assertTrue(
            $checkoutCart->getMessagesBlock()->assertSuccessMessage(),
            'Gift card is not usable on frontend.'
        );

    }

    /**
     * Success assert that gift card usable in frontend
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift card usable in frontend.';
    }
}
