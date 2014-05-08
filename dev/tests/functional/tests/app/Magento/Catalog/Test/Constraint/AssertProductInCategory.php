<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Catalog\Test\Fixture\Category;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AssertProductInCategory
 * @package Magento\Catalog\Test\Constraint
 */
class AssertProductInCategory extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * @param CatalogCategoryView $catalogCategoryView
     * @param CmsIndex $cmsIndex
     * @param CatalogProductSimple $product
     * @param Category $category
     */
    public function processAssert(
        CatalogCategoryView $catalogCategoryView,
        CmsIndex $cmsIndex,
        CatalogProductSimple $product,
        Category $category
    ) {
        //Open category view page
        $cmsIndex->open();
        $cmsIndex->getTopmenu()->selectCategoryByName($category->getCategoryName());

        //process asserts
        $this->assertPrice($product, $catalogCategoryView);
    }

    /**
     * Verify product price on category view page
     *
     * @param CatalogProductSimple $product
     * @param CatalogCategoryView $catalogCategoryView
     */
    protected function assertPrice(CatalogProductSimple $product, CatalogCategoryView $catalogCategoryView)
    {
        /** @var \Magento\Catalog\Test\Fixture\CatalogProductSimple\Price $priceFixture */
        $priceFixture = $product->getDataFieldConfig('price')['source'];
        $pricePresetData = $priceFixture->getPreset();

        //Regular price verification
        if (isset($pricePresetData['category_special_price'])) {
            $regularPrice = $catalogCategoryView->getListProductBlock()->getProductPriceBlock($product->getName())
                ->getRegularPrice();
            \PHPUnit_Framework_Assert::assertEquals(
                $pricePresetData['category_price'],
                $regularPrice,
                'Product regular price on category page is not correct.'
            );
            //Special price verification
            $specialPrice = $catalogCategoryView->getListProductBlock()->getProductPriceBlock(
                $product->getName()
            )->getSpecialPrice();
            \PHPUnit_Framework_Assert::assertEquals(
                $pricePresetData['category_special_price'],
                $specialPrice,
                'Product special price on category page is not correct.'
            );
        } else {
            //Price verification
            $price = $catalogCategoryView->getListProductBlock()->getProductPriceBlock($product->getName())
                ->getPrice();
            \PHPUnit_Framework_Assert::assertContains(
                (string)$price,
                $pricePresetData['category_price'],
                'Product price on category page is not correct.'
            );
        }
    }

    /**
     * Text of Visible in category assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Product price on category page is not correct.';
    }
}
