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
        \PHPUnit_Framework_Assert::assertTrue(
            ($price == '$100.00'),
            'Product price is wrong.'
        );
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
        $price = $catalogProductView->getViewBlock()->getProductPrice();
        \PHPUnit_Framework_Assert::assertTrue(
            ($price == 100),
            'Product price is wrong.'
        );
        $customOption = $catalogProductView->getOptionsBlock();
        $options = $customOption->getProductCustomOptions();
        $productOptions = $product->getCustomOptions();
        $key = $productOptions[0]['title'];
        $customOption->selectProductCustomOption($options[$key][1]);
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
        \PHPUnit_Framework_Assert::assertTrue(
            ($price == "$130.00"),
            'Product price is wrong.'
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


    /**
     * @var array
     */
    protected $assertions = [
            'MAGETWO-23062' => [
                'catalog_price' => '$100.00',
                'product_price' => '100.00',
                'cart_price' => '$130.00'
            ],
            'MAGETWO-23063' => [
                'catalog_price' => '$100.00',
                'product_price' => '100.00',
                'cart_price' => '$140.00'
            ]
        ];
}
