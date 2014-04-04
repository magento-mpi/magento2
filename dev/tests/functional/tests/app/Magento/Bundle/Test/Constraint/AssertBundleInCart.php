<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Constraint;

use Magento\Bundle\Test\Fixture\CatalogProductBundle;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Checkout\Test\Page\CheckoutCart;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AssertProductInCart
 * @package Magento\Catalog\Test\Constraint
 */
class AssertBundleInCart extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductBundle $bundle
     * @param CheckoutCart $checkoutCart
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CatalogProductBundle $bundle,
        CheckoutCart $checkoutCart
    ) {
        //Add product to cart
        $catalogProductView->init($bundle);
        $catalogProductView->open();

        $productOptions = $bundle->getCustomOptions();
        if ($productOptions) {
            $customOption = $catalogProductView->getOptionsBlock();
            $options = $customOption->getProductCustomOptions();
            $key = $productOptions[0]['title'];
            $customOption->selectProductCustomOption($options[$key][1]);
        }
        $catalogProductView->getViewBlock()->clickAddToCart();

        $this->assertOnShoppingCart($bundle, $checkoutCart);
    }

    /**
     * Assert prices on the shopping Cart
     *
     * @param CatalogProductBundle $bundle
     * @param CheckoutCart $checkoutCart
     */
    protected function assertOnShoppingCart(CatalogProductBundle $bundle, CheckoutCart $checkoutCart)
    {
        /** @var \Magento\Catalog\Test\Fixture\CatalogProductSimple\Price $priceFixture */
        $priceFixture = $bundle->getDataFieldConfig('price')['fixture'];
        $pricePresetData = $priceFixture->getPreset();

        $price = $checkoutCart->getCartBlock()->getProductPriceByName($bundle->getName());
        \PHPUnit_Framework_Assert::assertEquals(
            $pricePresetData['cart_price'],
            $price,
            'Bundle price in shopping cart is not correct.'
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
