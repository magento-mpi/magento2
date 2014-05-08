<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\Category;
use Magento\Bundle\Test\Fixture\CatalogProductBundle;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;

/**
 * Class AssertProductInCategory
 */
class AssertBundleInCategory extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Check bundle product on the category page
     *
     * @param CatalogCategoryView $catalogCategoryView
     * @param CmsIndex $cmsIndex
     * @param CatalogProductBundle $bundle
     * @param Category $category
     */
    public function processAssert(
        CatalogCategoryView $catalogCategoryView,
        CmsIndex $cmsIndex,
        CatalogProductBundle $bundle,
        Category $category
    ) {
        //Open category view page
        $cmsIndex->open();
        $cmsIndex->getTopmenu()->selectCategoryByName($category->getCategoryName());

        //process asserts
        $this->assertPrice($bundle, $catalogCategoryView);
    }

    /**
     * Verify product price on category view page
     *
     * @param CatalogProductBundle $bundle
     * @param CatalogCategoryView $catalogCategoryView
     */
    protected function assertPrice(CatalogProductBundle $bundle, CatalogCategoryView $catalogCategoryView)
    {
        /** @var \Magento\Catalog\Test\Fixture\CatalogProductSimple\Price $priceFixture */
        $priceFixture = $bundle->getDataFieldConfig('price')['fixture'];
        $pricePresetData = $priceFixture->getPreset();

        //Price from/to verification
        $priceBlock = $catalogCategoryView->getListProductBlock()->getProductPriceBlock($bundle->getName());
        \PHPUnit_Framework_Assert::assertEquals(
            $pricePresetData['price_from'],
            $priceBlock->getPriceFrom(),
            'Bundle price From on category page is not correct.'
        );
        \PHPUnit_Framework_Assert::assertEquals(
            $pricePresetData['price_to'],
            $priceBlock->getPriceTo(),
            'Bundle price To on category page is not correct.'
        );
    }

    /**
     * Text of Visible in category assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Bundle price on category page is not correct.';
    }
}
