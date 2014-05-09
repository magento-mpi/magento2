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
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AssertCatalogRuleProductView
 */
class AssertCatalogRuleProductView extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * @param CatalogCategoryView $catalogCategoryView
     * @param CatalogProductView $catalogProductView
     * @param CmsIndex $cmsIndex
     * @param CatalogRule $catalogRule
     */
    public function processAssert(
        CatalogCategoryView $catalogCategoryView,
        CatalogProductView $catalogProductView,
        CmsIndex $cmsIndex,
        CatalogRule $catalogRule
    ) {
        /** @var CatalogProductSimple $product */
        $product = $catalogRule->getDataFieldConfig('condition_value')['source']->getProduct();
        /** @var Category $category */
        $category = $product->getDataFieldConfig('category_ids')['fixture']->getCategory();
        $cmsIndex->open();
        $cmsIndex->getTopmenu()->selectCategoryByName($category->getCategoryName());
        //Open product view page
        $catalogCategoryView->getListProductBlock()->openProductViewPage($product->getName());
        $catalogProductView->init($product);

        //Process assertions
        $this->assertOnProductView($product, $catalogProductView);
    }

    /**
     * Assert prices on the product view Page
     *
     * @param CatalogProductSimple $product
     * @param CatalogProductView $catalogProductView
     */
    protected function assertOnProductView(CatalogProductSimple $product, CatalogProductView $catalogProductView)
    {
        /** @var \Magento\Catalog\Test\Fixture\CatalogProductSimple\Price $priceFixture */
        $priceFixture = $product->getDataFieldConfig('price')['fixture'];
        $pricePresetData = $priceFixture->getPreset();

        if (isset($pricePresetData['product_special_price'])) {
        $regularPrice = $catalogProductView->getViewBlock()->getProductPriceBlock()->getRegularPrice();
        \PHPUnit_Framework_Assert::assertEquals(
            $pricePresetData['product_price'],
            $regularPrice,
            'Product regular price on product view page is not correct.'
        );

            $specialPrice = $catalogProductView->getViewBlock()->getProductPriceBlock()->getSpecialPrice();
            \PHPUnit_Framework_Assert::assertEquals(
                $pricePresetData['product_special_price'],
                $specialPrice,
                'Product special price on product view page is not correct.'
            );
        } else {
            //Price verification
            $price = $catalogProductView->getViewBlock()->getProductPriceBlock($product->getName())
                ->getPrice();
            \PHPUnit_Framework_Assert::assertContains(
                (string)$price,
                $pricePresetData['product_price'],
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
        return 'Product price on product view page is not correct.';
    }
}
