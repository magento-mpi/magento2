<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Constraint;

use Mtf\Client\Browser;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\GiftRegistry\Test\Fixture\GiftRegistry;

/**
 * Class AssertGiftRegistryInactiveNotInShoppingCart
 * Assert that product can't be added to Inactive GiftRegistry from Shopping Cart
 */
class AssertGiftRegistryInactiveNotInShoppingCart extends AbstractConstraint
{
    /**
     * Assert that product can not be added to inactive gift registry from Shopping Cart
     *
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @param CatalogProductSimple $product
     * @param GiftRegistry $giftRegistry
     * @param Browser $browser
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart,
        CatalogProductSimple $product,
        GiftRegistry $giftRegistry,
        Browser $browser
    ) {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $catalogProductView->getViewBlock()->clickAddToCart();
        \PHPUnit_Framework_Assert::assertFalse(
            $checkoutCart->getGiftRegistryCart()->isGiftRegistryAvailable($giftRegistry),
            'Product can be added to inactive gift registry \'' . $giftRegistry->getTitle() . '\' from Shopping Cart.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product can not be added to inactive gift registry from Shopping Cart.';
    }
}
