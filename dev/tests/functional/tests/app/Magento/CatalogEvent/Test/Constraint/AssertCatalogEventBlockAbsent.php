<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;

/**
 * Class AssertCatalogEventBlockAbsent
 * Check invisible Event block on category/product pages
 */
class AssertCatalogEventBlockAbsent extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Category Page on Frontend
     *
     * @var CatalogCategoryView
     */
    protected $catalogCategoryView;

    /**
     * Index Page on Frontend
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Product Page on Frontend
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Category Name
     *
     * @var string
     */
    protected $categoryName;

    /**
     * Product Name
     *
     * @var string
     */
    protected $productName;

    /**
     * Assert that Event block is invisible on category/product pages
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogCategoryView $catalogCategoryView
     * @param CatalogProductSimple $product
     * @param CatalogProductView $catalogProductView
     *
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        CatalogCategoryView $catalogCategoryView,
        CatalogProductSimple $product,
        CatalogProductView $catalogProductView
    ) {
        $this->catalogCategoryView = $catalogCategoryView;
        $this->cmsIndex = $cmsIndex;
        $this->catalogProductView = $catalogProductView;

        $this->categoryName = $product->getCategoryIds()[0];
        $this->productName = $product->getName();

        $this->checkEventBlockOnCategoryPageAbsent();
        $this->checkEventBlockOnProductPageAbsent();
    }

    /**
     * Event block is invisible on Category page
     *
     * @return void
     */
    protected function checkEventBlockOnCategoryPageAbsent()
    {
        $this->cmsIndex->open();
        $this->cmsIndex->getTopmenu()->selectCategoryByName($this->categoryName);
        \PHPUnit_Framework_Assert::assertFalse(
            $this->catalogCategoryView->getEventBlock()->isVisible(),
            "EventBlock is present on Category page."
        );
    }

    /**
     * Event block is invisible on Product page
     *
     * @return void
     */
    protected function checkEventBlockOnProductPageAbsent()
    {
        $this->cmsIndex->open();
        $this->cmsIndex->getTopmenu()->selectCategoryByName($this->categoryName);
        $this->catalogCategoryView->getListProductBlock()->openProductViewPage($this->productName);
        \PHPUnit_Framework_Assert::assertFalse(
            $this->catalogProductView->getEventBlock()->isVisible(),
            "EventBlock is present on Product page."
        );
    }

    /**
     * Text invisible Event Block on category/product pages
     *
     * @return string
     */
    public function toString()
    {
        return 'Event block is invisible on category/product pages';
    }
}
