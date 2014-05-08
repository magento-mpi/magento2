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
 * Class AssertAddToCartButtonAbsent
 * Checks the button on the category/product pages
 */
class AssertAddToCartButtonAbsent extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Category Page
     *
     * @var CatalogCategoryView
     */
    protected $catalogCategoryView;

    /**
     * Index Page
     *

    *
* /**
     * Product simple fixture
     *
     * @var CatalogProductSimple
     */
    protected $catalogProductSimple;

    /**
     * Product Page on Frontend
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Assert that "Add to cart" button is not display on page.
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

        $this->addToCardAbsentOnCategory();
        $this->addToCardAbsentOnProduct();
    }

    /**
     * "Add to cart" button is not displayed on Category page
     *
     * @return void
     */
    protected function addToCardAbsentOnCategory()
    {
        $this->cmsIndex->open();
        $this->cmsIndex->getTopmenuBlock()->selectCategoryByName($this->catalogProductSimple->getCategoryIds()[1]);
        \PHPUnit_Framework_Assert::assertFalse(
            $this->catalogCategoryView->getListProductBlock()->checkAddToCardButton(),
            "Button 'Add to Card' is present on Category page"
        );
    }

    /**
     * "Add to cart" button is not display on Product page
     *
     * @return void
     */
    protected function addToCardAbsentOnProduct()
    {
        $this->cmsIndex->open();
        $this->cmsIndex->getTopmenuBlock()->selectCategoryByName($this->catalogProductSimple->getCategoryIds()[1]);
        $this->catalogCategoryView->getListProductBlock()->openProductViewPage($this->catalogProductSimple->getName());
        \PHPUnit_Framework_Assert::assertFalse(
            $this->catalogProductView->getViewBlock()->isVisibleAddToCart(),
            "Button 'Add to Card' is present on Product page"
        );
    }

    /**
     * Text absent button "Add to Cart" on the category/product pages
     *
     * @return string
     */
    public function toString()
    {
        return "Button 'Add to Card' is absent on Category page and Product Page.";
    }
}
