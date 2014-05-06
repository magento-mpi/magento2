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
     * Category Page on Frontend
     *
     * @var CatalogCategoryView $catalogCategoryView
     */
    protected $catalogCategoryView;

    /**
     * Index Page
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
     * Assert that "Add to cart" button is presented on page.
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogCategoryView $catalogCategoryView
     * @param CatalogProductSimple $catalogProductSimple
     * @param CatalogProductView $catalogProductView
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

        $this->addToCardPresentOnCategory();
        $this->addToCardPresentOnProduct();
    }

    /**
     * "Add to cart" button is displayed on Category page
     *
     * @return void
     */
    protected function addToCardPresentOnCategory()
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

    /**
     * "Add to cart" button is displayed on Product page
     *
     * @return void
     */
    protected function addToCardPresentOnProduct()
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
