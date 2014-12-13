<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftRegistry\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\GiftRegistry\Test\Fixture\GiftRegistry;
use Magento\GiftRegistry\Test\Page\GiftRegistryItems;
use Mtf\Client\Browser;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGiftRegistryActiveInShoppingCart
 * Assert that any product can be added to appropriate active GiftRegistry from Shopping Cart
 */
class AssertGiftRegistryActiveInShoppingCart extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that product can be added to active gift registry from Shopping Cart
     *
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @param CatalogProductSimple $product
     * @param GiftRegistry $giftRegistry
     * @param GiftRegistryItems $giftRegistryItems
     * @param Browser $browser
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart,
        CatalogProductSimple $product,
        GiftRegistry $giftRegistry,
        GiftRegistryItems $giftRegistryItems,
        Browser $browser
    ) {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $catalogProductView->getViewBlock()->clickAddToCart();
        $checkoutCart->getGiftRegistryCart()->addToGiftRegistry($giftRegistry->getTitle());
        \PHPUnit_Framework_Assert::assertTrue(
            $giftRegistryItems->getGiftRegistryItemsBlock()->isProductInGrid($product),
            'Product can not be added to active gift registry \'' . $giftRegistry->getTitle() . '\' from Shopping Cart.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product can be added to active gift registry from Shopping Cart.';
    }
}
