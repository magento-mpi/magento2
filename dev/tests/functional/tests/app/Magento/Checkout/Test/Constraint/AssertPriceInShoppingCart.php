<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Checkout\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Checkout\Test\Fixture\Cart;
use Magento\Checkout\Test\Fixture\Cart\Items;
use Magento\Checkout\Test\Page\CheckoutCart;
use Mtf\Constraint\AbstractAssertForm;
use Mtf\Fixture\FixtureInterface;

/**
 * Class AssertPriceInShoppingCart
 * Assert that price in the shopping cart equals to expected price from data set
 */
class AssertPriceInShoppingCart extends AbstractAssertForm
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that price in the shopping cart equals to expected price from data set
     *
     * @param CheckoutCart $checkoutCart
     * @param Cart $cart
     * @return void
     */
    public function processAssert(CheckoutCart $checkoutCart, Cart $cart)
    {
        $checkoutCart->open();
        /** @var Items $sourceProducts */
        $sourceProducts = $cart->getDataFieldConfig('items')['source'];
        $products = $sourceProducts->getProducts();
        $items = $cart->getItems();
        $productsData = [];
        $cartData = [];

        foreach ($items as $key => $item) {
            /** @var CatalogProductSimple $product */
            $product = $products[$key];
            $productName = $product->getName();
            /** @var FixtureInterface $item */
            $checkoutItem = $item->getData();
            $cartItem = $checkoutCart->getCartBlock()->getCartItem($product);

            $productsData[$productName] = [
                'price' => $checkoutItem['price'],
            ];
            $cartData[$productName] = [
                'price' => $cartItem->getPrice(),
            ];
        }

        $error = $this->verifyData($productsData, $cartData, true);
        \PHPUnit_Framework_Assert::assertEmpty($error, $error);
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Price in the shopping cart equals to expected price from data set.';
    }
}
