<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Checkout\Test\Page\CheckoutCart;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AssertProductInCart
 * @package Magento\Catalog\Test\Constraint
 */
class AssertProductInCart extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductSimple $product
     * @param CheckoutCart $checkoutCart
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CatalogProductSimple $product,
        CheckoutCart $checkoutCart
    ) {
        //Add product to cart
        $catalogProductView->init($product);
        $catalogProductView->open();
        $productOptions = $product->getCustomOptions();
        if ($productOptions) {
            $customOption = $catalogProductView->getOptionsBlock();
            $options = $customOption->getProductCustomOptions();
            $key = $productOptions[0]['title'];
            $customOption->selectProductCustomOption($options[$key][1]);
        }
        $catalogProductView->getViewBlock()->clickAddToCart();

        $this->assertOnShoppingCart($product, $checkoutCart);
    }

    /**
     * Assert prices on the shopping Cart
     *
     * @param CatalogProductSimple $product
     * @param CheckoutCart $checkoutCart
     * @return void
     */
    protected function assertOnShoppingCart(CatalogProductSimple $product, CheckoutCart $checkoutCart)
    {
        $cartBlock = $checkoutCart->getCartBlock();
        $productName = $product->getName();
        $productOptions = $product->getCustomOptions();
        $priceComparing = $product->getPrice();

        if ($groupPrice = $product->getGroupPrice()) {
            $groupPrice = reset($groupPrice);
            $priceComparing = $groupPrice['price'];
        }

        if ($specialPrice = $product->getSpecialPrice()) {
            $priceComparing = $specialPrice;
        }

        if (!empty($productOptions)) {
            $productOption = reset($productOptions);
            $optionsData = reset($productOption['options']);
            $optionName = $cartBlock->getCartItemOptionsNameByProductName($productName);
            $optionValue = $cartBlock->getCartItemOptionsValueByProductName($productName);

            \PHPUnit_Framework_Assert::assertTrue(
                trim($optionName) === $productOption['title']
                && trim($optionValue) === $optionsData['title'],
                'In the cart wrong option product.'
            );

            if ($optionsData['price_type'] === 'Percent') {
                $priceComparing = $priceComparing * (1 + $optionsData['price'] / 100);
            } else {
                $priceComparing += $optionsData['price'];
            }
        }

        $price = $checkoutCart->getCartBlock()->getProductPriceByName($productName);
        \PHPUnit_Framework_Assert::assertEquals(
            '$' . number_format($priceComparing, 2),
            $price,
            'Product price in shopping cart is not correct.'
        );
    }

    /**
     * Text of Visible in category assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Product price in shopping cart is not correct.';
    }
}
