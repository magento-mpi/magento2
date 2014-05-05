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
     */
    protected function assertOnShoppingCart(CatalogProductSimple $product, CheckoutCart $checkoutCart)
    {
        /** @var \Magento\Catalog\Test\Fixture\CatalogProductSimple\Price $priceFixture */
        $priceFixture = $product->getDataFieldConfig('price')['fixture'];
        $pricePresetData = $priceFixture->getPreset();

        $price = $checkoutCart->getCartBlock()->getProductPriceByName($product->getName());
        \PHPUnit_Framework_Assert::assertEquals(
            $pricePresetData['cart_price'],
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
