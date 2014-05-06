<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Checkout\Test\Page\CheckoutCartInjectable;
use Magento\GiftCardAccount\Test\Fixture\GiftCardAccount;
use Magento\GiftCardAccount\Test\Page\Adminhtml\Index;
use Mtf\Fixture\FixtureFactory;

/**
 * Class AssertGiftCardAccountIsNotUsableInCartOnFrontend
 *
 * @package Magento\GiftCardAccount\Test\Constraint
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
     * @param FixtureFactory $fixtureFactory
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCartInjectable $checkoutCart
     * @param Index $index
     * @param CatalogProductSimple $catalogProductSimple
     * @param GiftCardAccount $giftCardAccount
     * @return void
     */
    public function processAssert(
        FixtureFactory $fixtureFactory,
        CatalogProductView $catalogProductView,
        CheckoutCartInjectable $checkoutCart,
        Index $index,
        CatalogProductSimple $catalogProductSimple,
        GiftCardAccount $giftCardAccount
    ) {
        $index->open();
        /** @var array $filter */
        $filter = ['balance' => $giftCardAccount->getBalance()];
        /** @var string $value */
        $value = $index->getGiftCardAccount()->searchCode($filter, false);

        $catalogProductView->init($catalogProductSimple);
        $catalogProductView->open();
        $catalogProductView->getViewBlock()->clickAddToCart();
        $giftCardAccountFixture = $fixtureFactory->
            createByCode('giftCardAccount', ['data' => ['code' => $value]]);

        $checkoutCart->getGiftCardAccount()->fillGiftCardInCart($giftCardAccountFixture);
        $isErrorMassage = $checkoutCart->getMessages()->assertErrorMessage();

        \PHPUnit_Framework_Assert::assertTrue(
            $isErrorMassage,
            'Gift card is usable on frontend'
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
