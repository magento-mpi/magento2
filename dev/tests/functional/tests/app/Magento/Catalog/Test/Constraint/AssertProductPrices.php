<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Magento\Catalog\Test\Page\Product\CatalogProductView;
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
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CatalogCategoryView $catalogCategoryView,
        CmsIndex $cmsIndex,
        CatalogProductSimple $product,
        Category $category
    ) {
        $this->assertOnCategoryList($cmsIndex,$category, $product, $catalogCategoryView);
        $this->assertOnProductView($product, $catalogProductView);
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
            ($price == '$10.00'),
            'Product price is wrong.'
        );
    }

    /**
     * @param CatalogProductSimple $product
     * @param CatalogProductView $catalogProductView
     */
    protected function assertOnProductView(CatalogProductSimple $product, CatalogProductView $catalogProductView)
    {
        $catalogProductView->init($product);
        $catalogProductView->open();
        $price = $catalogProductView->getViewBlock()->getProductPrice();
        \PHPUnit_Framework_Assert::assertTrue(
            ($price == 10),
            'Product price is wrong.'
        );
        $customOption = $catalogProductView->getCustomOptionBlock();
        $options = $customOption->getProductCustomOptions();
        $customOption->fillProductOptions($options[1]);
        $addToCart = $catalogProductView->getViewBlock();
        $addToCart->addToCart($product);
    }

    /**
     * @param CatalogProductSimple $product
     * @param CatalogProductView $catalogProductView
     */
    protected function assertOnShoppingCart(CatalogProductSimple $product, CatalogProductView $catalogProductView)
    {
        return true;
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

    protected $assertions = [
        'set1' => [
            'final_price' => 10
        ],
        'set2' => [
            'final_price' => 20
        ]
    ];
}
