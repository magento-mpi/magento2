<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Constraint;

use Mtf\Constraint\AbstractAssertForm;
use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Fixture\FixtureInterface;
use Magento\Checkout\Test\Fixture\Cart;
use Magento\Checkout\Test\Fixture\Cart\Items;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AssertProductQtyInMiniShoppingCart
 * Assert that product quantity in the mini shopping cart is equals to expected quantity from data set
 */
class AssertProductQtyInMiniShoppingCart extends AbstractAssertForm
{
    /* tags */
     const SEVERITY = 'low';
     /* end tags */

    /**
     * Assert that product quantity in the mini shopping cart is equals to expected quantity from data set
     *
     * @param CmsIndex $cmsIndex
     * @param Cart $cart
     * @return void
     */
    public function processAssert(CmsIndex $cmsIndex, Cart $cart)
    {
        $cmsIndex->open();
        /** @var Items $sourceProducts */
        $sourceProducts = $cart->getDataFieldConfig('items')['source'];
        $products = $sourceProducts->getProducts();
        $items = $cart->getItems();
        $productsData = [];
        $miniCartData = [];

        foreach ($items as $key => $item) {
            /** @var CatalogProductSimple $product */
            $product = $products[$key];
            $productName = $product->getName();
            /** @var FixtureInterface $item */
            $checkoutItem = $item->getData();

            $productsData[$productName] = [
                'qty' => $checkoutItem['qty']
            ];
            $miniCartData[$productName] = [
                'qty' => $cmsIndex->getCartSidebarBlock()->getProductQty($productName)
            ];
        }

        $error = $this->verifyData($productsData, $miniCartData, true);
        \PHPUnit_Framework_Assert::assertEmpty($error, $error);
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Quantity in the mini shopping cart equals to expected quantity from data set.';
    }
}
