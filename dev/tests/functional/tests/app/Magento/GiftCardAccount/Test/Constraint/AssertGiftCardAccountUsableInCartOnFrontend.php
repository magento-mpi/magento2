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
use Magento\Checkout\Test\Page\CheckoutCartInjectable;
use Magento\GiftCardAccount\Test\Fixture\GiftCardAccount;
use Magento\GiftCardAccount\Test\Page\Adminhtml\Index;
use Mtf\Constraint\AbstractConstraint;
use Mtf\Fixture\FixtureFactory;

/**
 * Class AssertGiftCardAccountUsableInCartOnFrontend
 *
 * @package Magento\GiftCardAccount\Test\Constraint
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
        $giftCardAccountFixture = $fixtureFactory->createByCode(
            'giftCardAccount',
            ['data' => ['code' => $value]]
        );

        $checkoutCart->getGiftCardAccount()->fillGiftCardInCart($giftCardAccountFixture);
        $isSuccessMassage = $checkoutCart->getMessages()->assertSuccessMessage();

        \PHPUnit_Framework_Assert::assertTrue(
            $isSuccessMassage,
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
