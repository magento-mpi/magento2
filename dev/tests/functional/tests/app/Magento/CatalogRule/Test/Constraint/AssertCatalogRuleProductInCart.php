<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\Constraint;

use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\CatalogRule\Test\Fixture\CatalogRule;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Checkout\Test\Page\CheckoutCart;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AssertCatalogRuleProductInCart
 * @package Magento\CatalogRule\Test\Constraint
 */
class AssertCatalogRuleProductInCart extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * @param CatalogProductView $catalogProductView
     * @param CatalogCategoryView $catalogCategoryView
     * @param CatalogRule $catalogRule
     * @param CmsIndex $cmsIndex
     * @param CheckoutCart $checkoutCart
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CatalogCategoryView $catalogCategoryView,
        CatalogRule $catalogRule,
        CmsIndex $cmsIndex,
        CheckoutCart $checkoutCart
    ) {
        /** @var CatalogProductSimple $product */
        $product = $catalogRule->getDataFieldConfig('condition_value')['fixture']->getProduct();
        //Add product to cart
        $category = $product->getDataFieldConfig('category_ids')['fixture']->getCategory();
        $cmsIndex->open();
        $cmsIndex->getTopmenu()->selectCategoryByName($category->getCategoryName());
        //Open product view page
        $catalogCategoryView->getListProductBlock()->openProductViewPage($product->getName());
        $catalogProductView->init($product);

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
