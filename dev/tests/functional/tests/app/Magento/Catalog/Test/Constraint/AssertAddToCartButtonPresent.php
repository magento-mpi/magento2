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
 * Checks the button on the category/product pages
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
     * @var CatalogCategoryView
     */
    protected $catalogCategoryView;

    /**
     * Index Page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
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
     * Assert that "Add to cart" button is present on page.
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

        $this->addToCardPresentOnCategory();
        $this->addToCardPresentOnProduct();
    }

    /**
     * "Add to cart" button is display on Category page
     *
     * @return void
     */
    protected function addToCardPresentOnCategory()
    {
        $this->cmsIndex->open();
        $this->cmsIndex->getTopmenuBlock()->selectCategoryByName($this->catalogProductSimple->getCategoryIds()[1]);
        \PHPUnit_Framework_Assert::assertTrue(
            $this->catalogCategoryView->getListProductBlock()->checkAddToCardButton(),
            "Button 'Add to Card' is absent on Category page"
        );
    }

    /**
     * "Add to cart" button is display on Product page
     *
     * @return void
     */
    protected function addToCardPresentOnProduct()
    {
        $this->cmsIndex->open();
        $this->cmsIndex->getTopmenuBlock()->selectCategoryByName($this->catalogProductSimple->getCategoryIds()[1]);
        $this->catalogCategoryView->getListProductBlock()->openProductViewPage($this->catalogProductSimple->getName());
        \PHPUnit_Framework_Assert::assertTrue(
            $this->catalogProductView->getViewBlock()->isVisibleAddToCart(),
            "Button 'Add to Card' is absent on Product page"
        );
    }

    /**
     * Text present button "Add to Cart"  on the category/product pages
     *
     * @return string
     */
    public function toString()
    {
        return "Button 'Add to Card' is present on Category page.";
    }
}
