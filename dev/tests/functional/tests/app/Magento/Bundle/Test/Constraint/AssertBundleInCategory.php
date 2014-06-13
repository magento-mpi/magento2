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
use Magento\Bundle\Test\Fixture\CatalogProductBundle;
use Magento\Catalog\Test\Fixture\CatalogCategory;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;

/**
 * Class AssertProductInCategory
 * Assert that product is visible in the assigned category and checks the price of the product
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
     * @param CatalogProductBundle $product
     * @param CatalogCategory $category
     * @return void
     */
    public function processAssert(
        CatalogCategoryView $catalogCategoryView,
        CmsIndex $cmsIndex,
        CatalogProductBundle $product,
        CatalogCategory $category
    ) {
        //Open category view page
        $cmsIndex->open();
        $cmsIndex->getTopmenu()->selectCategoryByName($category->getName());

        //process asserts
        $this->assertPrice($product, $catalogCategoryView);
    }

    /**
     * Verify product price on category view page
     *
     * @param CatalogProductBundle $bundle
     * @param CatalogCategoryView $catalogCategoryView
     * @return void
     */
    protected function assertPrice(CatalogProductBundle $bundle, CatalogCategoryView $catalogCategoryView)
    {
        $priceData = $bundle->getDataFieldConfig('price')['source']->getPreset();
        //Price from/to verification
        $priceBlock = $catalogCategoryView->getListProductBlock()->getProductPriceBlock($bundle->getName());
        \PHPUnit_Framework_Assert::assertEquals(
            $priceData['price_from'],
            $priceBlock->getPriceFrom(),
            'Bundle price From on category page is not correct.'
        );
        \PHPUnit_Framework_Assert::assertEquals(
            $priceData['price_to'],
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
