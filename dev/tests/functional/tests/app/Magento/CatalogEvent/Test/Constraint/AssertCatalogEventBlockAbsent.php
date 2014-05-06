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
 *
 * @package Magento\CatalogEvent\Test\Constraint
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
     * @var CatalogCategoryView $catalogCategoryView
     */
    protected $catalogCategoryView;

    /**
     * Index Page on Frontend
     *
     * @var CmsIndex $cmsIndex
     */
    protected $cmsIndex;

    /**
     * Product Page
     *
     * @var CatalogProductSimple $catalogProductSimple
     */
    protected $catalogProductSimple;

    /**
     * Product Page on Frontend
     *
     * @var CatalogProductView $catalogProductView
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
     * Assert that Event block is invisible on catalog page/product pages
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogCategoryView $catalogCategoryView
     * @param CatalogProductSimple $catalogProductSimple
     * @param CatalogProductView $catalogProductView
     *
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        CatalogCategoryView $catalogCategoryView,
        CatalogProductSimple $catalogProductSimple,
        CatalogProductView $catalogProductView
    ) {
        $this->catalogCategoryView = $catalogCategoryView;
        $this->cmsIndex = $cmsIndex;
        $this->catalogProductSimple = $catalogProductSimple;
        $this->catalogProductView = $catalogProductView;

        $this->categoryName = $this->catalogProductSimple->getDataFieldConfig('category_ids')['fixture']
            ->getCategory()[0]->getName();
        $this->productName = $this->catalogProductSimple->getData('name');

        $this->blockEventOnCategoryPageAbsent();
        $this->blockEventOnProductPageAbsent();
    }

    /**
     * Event block is invisible on Category page
     * @return void
     */
    protected function blockEventOnCategoryPageAbsent()
    {
        $this->cmsIndex->open();
        $this->cmsIndex->getTopmenuBlock()->selectCategoryByName($this->categoryName);
        \PHPUnit_Framework_Assert::assertFalse(
            $this->catalogCategoryView->getEventBlock()->isVisible(),
            "EventBlock is present on Category page"
        );
    }

    /**
     * Event block is invisible on Product page
     * @return void
     */
    protected function blockEventOnProductPageAbsent()
    {
        $this->cmsIndex->open();
        $this->catalogProductSimple->getDataFieldConfig('category_ids');
        $this->cmsIndex->getTopmenuBlock()->selectCategoryByName($this->categoryName);
        $this->catalogCategoryView->getListProductBlock()->openProductViewPage($this->productName);
        \PHPUnit_Framework_Assert::assertFalse(
            $this->catalogProductView->getEventBlock()->isVisible(),
            "EventBlock is present on Product page"
        );
    }

    /**
     * Text success invisible Event Block on catalog page/product pages
     *
     * @return string
     */
    public function toString()
    {
        return 'Event block is invisible on catalog page/product pages';
    }
}
