<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\GiftRegistry\Test\Fixture\GiftRegistry;

/**
 * Class AssertGiftRegistryIsInactiveNotInShoppingCart
 */
class AssertGiftRegistryIsInactiveNotInShoppingCart extends AbstractConstraint
{
    /**
     * Assert that product can not be added to active gift registry from Shopping Cart
     *
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @param CatalogProductSimple $product
     * @param GiftRegistry $giftRegistry
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart,
        CatalogProductSimple $product,
        GiftRegistry $giftRegistry
    ) {
        $catalogProductView->init($product);
        $catalogProductView->open()->getViewBlock()->clickAddToCart();
        \PHPUnit_Framework_Assert::assertFalse(
            $checkoutCart->getCartBlock()->giftRegistryIsVisible($giftRegistry->getTitle()),
            'Product can be added to gift registry \'' . $giftRegistry->getTitle() . '\' from Shopping Cart.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product can not be added to active GiftRegistry from Shopping Cart.';
    }
}
