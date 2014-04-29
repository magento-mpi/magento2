<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\CatalogEvent\Test\Fixture\CatalogEventEntity;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;

/**
 * Class AssertAddToCartButtonPresent
 *
 * @package Magento\Catalog\Test\Constraint
 */
class AssertAddToCartButtonPresent extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Constraint severeness
     *
     * @var string
     */
    public $pageDisplayState;

    /**
     * Constraint severeness
     *
     * @var CatalogCategoryView $catalogCategoryView
     */
    protected  $catalogCategoryView;

    /**
     * Constraint severeness
     *
     * @var CmsIndex $cmsIndex
     */
    protected  $cmsIndex;

    /**
     * Constraint severeness
     *
     * @var CatalogProductSimple $catalogProductSimple
     */
    protected  $catalogProductSimple;

    /**
     * Constraint severeness
     *
     * @var CatalogProductView $catalogProductView
     */
    protected $catalogProductView;

    /**
     * @param CmsIndex $cmsIndex
     * @param CatalogEventEntity $catalogEvent
     * @param CatalogCategoryView $catalogCategoryView
     * @param CatalogProductSimple $catalogProductSimple
     * @param CatalogProductView $catalogProductView
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        CatalogEventEntity $catalogEvent,
        CatalogCategoryView $catalogCategoryView,
        CatalogProductSimple $catalogProductSimple,
        CatalogProductView $catalogProductView
    ) {
        $this->catalogCategoryView = $catalogCategoryView;
        $this->cmsIndex = $cmsIndex;
        $this->catalogProductSimple = $catalogProductSimple;
        $this->catalogProductView = $catalogProductView;

        $pageEvent = $catalogEvent->getDisplayState();
        if($pageEvent['category_page'] == "Yes") {
            $this->blockEventOnCategoryPage();
        }
        if($pageEvent['product_page'] == "Yes") {
            $this->blockEventOnProductPage();
        }
    }

    protected function blockEventOnCategoryPage()
    {
        $category = $this->catalogProductSimple->getDataFieldConfig('category_ids')['fixture']->getCategory();
        $categoryName = $category[0]->getName();
        $this->cmsIndex->open();
        $this->cmsIndex->getTopmenuBlock()->selectCategoryByName($categoryName);
        $findOnCategoryPage = $this->catalogCategoryView->getListProductBlock()->getAddToCardButton();
        \PHPUnit_Framework_Assert::assertTrue(
            $findOnCategoryPage,
            "Button 'Add to Card' is absent on Category page"
        );

    }

    protected function blockEventOnProductPage()
    {
        $category = $this->catalogProductSimple->getDataFieldConfig('category_ids')['fixture']->getCategory();
        $categoryName = $category[0]->getName();
        $this->cmsIndex->open();
        $this->cmsIndex->getTopmenuBlock()->selectCategoryByName($categoryName);

        $productName = $this->catalogProductSimple->getData('name');
        $this->catalogCategoryView->getListProductBlock()->openProductViewPage($productName);
        $findOnProductPage = $this->catalogProductView->getViewBlock()->isVisibleAddToCart();
        \PHPUnit_Framework_Assert::assertTrue(
            $findOnProductPage,
            "Button 'Add to Card' is absent on Product page"
        );
    }

    /**
     * Text success present button "Add to Cart"
     *
     * @return string
     */
    public function toString()
    {
        return "Button 'Add to Card' is present on Category page.";
    }
}
