<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\GiftCardAccount\Test\Page\CheckoutCart;
use Magento\GiftCardAccount\Test\Fixture\GiftCardAccount;
use Magento\GiftCardAccount\Test\Page\Adminhtml\Index;
use Mtf\Constraint\AbstractConstraint;

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
     * @param CatalogProductSimple $catalogProductSimple
     * @param GiftCardAccount $giftCardAccount
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart,
        Index $index,
        CatalogProductSimple $catalogProductSimple,
        GiftCardAccount $giftCardAccount
    ) {
        $index->open();
        $filter = ['balance' => $giftCardAccount->getBalance()];
        $value = $index->getGiftCardAccount()->getCode($filter, false);

        $catalogProductView->init($catalogProductSimple);
        $catalogProductView->open();
        $catalogProductView->getViewBlock()->clickAddToCart();
        $checkoutCart->getGiftCardAccount()->addGiftCard($value);

        \PHPUnit_Framework_Assert::assertTrue(
            $checkoutCart->getMessages()->assertSuccessMessage(),
            'Gift card is not usable on frontend'
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
