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
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Catalog\Test\Fixture\Category;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AssertProductVisibleInCategory
 *
 * @package Magento\Catalog\Test\Constraint
 */
class AssertProductPrices extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Prices for verification
     *
     * @var array
     */
    protected $presetData;

    /**
     * @param CatalogProductView $catalogProductView
     * @param CatalogCategoryView $catalogCategoryView
     * @param CmsIndex $cmsIndex
     * @param CatalogProductSimple $product
     * @param Category $category
     * @param CheckoutCart $checkoutCart
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CatalogCategoryView $catalogCategoryView,
        CmsIndex $cmsIndex,
        CatalogProductSimple $product,
        Category $category,
        CheckoutCart $checkoutCart
    ) {
        $this->presetData = $product->getDataFieldConfig('price')['fixture']->getPreset();

        $this->assertOnCategoryList($cmsIndex,$category, $product, $catalogCategoryView);
        $this->assertOnProductView($product, $catalogProductView);
        $this->assertOnShoppingCart($product, $checkoutCart);
    }

    /**
     * @param $cmsIndex
     * @param $category
     * @param CatalogProductSimple $product
     * @param CatalogCategoryView $catalogCategoryView
     */
    protected function assertOnCategoryList(
        CmsIndex $cmsIndex,
        Category $category,
        CatalogProductSimple $product,
        CatalogCategoryView $catalogCategoryView
    ) {
        $cmsIndex->open();
        $cmsIndex->getTopmenu()->selectCategoryByName($category->getCategoryName());
        $price = $catalogCategoryView->getListProductBlock()->getProductPriceBlock(
            $product->getName()
        )->getRegularPrice();
        \PHPUnit_Framework_Assert::assertEquals(
            $this->presetData['category_price'],
            $price,
            'Product price on category is wrong.'
        );
        if (isset($this->presetData['category_special_price'])) {
            $specialPrice = $catalogCategoryView->getListProductBlock()->getProductPriceBlock(
                $product->getName()
            )->getSpecialPrice();
            \PHPUnit_Framework_Assert::assertEquals(
                $this->presetData['category_special_price'],
                $specialPrice,
                'Product price on category is wrong.'
            );
        }
    }

    /**
     * Assert prices on the product view Page
     *
     * @param CatalogProductSimple $product
     * @param CatalogProductView $catalogProductView
     * @param @dataProvider dataProvider
     */
    protected function assertOnProductView(CatalogProductSimple $product, CatalogProductView $catalogProductView)
    {
        $catalogProductView->init($product);
        $catalogProductView->open();
        $price = $catalogProductView->getViewBlock()->getProductPriceBlock()->getRegularPrice();
        \PHPUnit_Framework_Assert::assertEquals(
            $this->presetData['product_price'],
            $price,
            'Product price on product view page is wrong.'
        );
        if (isset($this->presetData['product_special_price'])) {
            $specialPrice = $catalogProductView->getViewBlock()->getProductPriceBlock()->getSpecialPrice();
            \PHPUnit_Framework_Assert::assertEquals(
                $this->presetData['product_special_price'],
                $specialPrice,
                'Product price on category is wrong.'
            );
        }
        $productOptions = $product->getCustomOptions();
        if ($productOptions) {
            $customOption = $catalogProductView->getOptionsBlock();
            $options = $customOption->getProductCustomOptions();
            $key = $productOptions[0]['title'];
            $customOption->selectProductCustomOption($options[$key][1]);
        }
        $addToCart = $catalogProductView->getViewBlock();
        $addToCart->clickAddToCart();
    }

    /**
     * Assert prices on the shopping Cart
     *
     * @param CatalogProductSimple $product
     * @param CheckoutCart $checkoutCart
     */
    protected function assertOnShoppingCart(
        CatalogProductSimple $product,
        CheckoutCart $checkoutCart
    )
    {
        $price = $checkoutCart->getCartBlock()->getProductPriceByName($product->getName());
        \PHPUnit_Framework_Assert::assertEquals(
            $this->presetData['cart_price'],
            $price,
            'Product price is wrong in shopping cart.'
        );
    }

    /**
     * Text of Visible in category assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Product price is wrong.';
    }
}
