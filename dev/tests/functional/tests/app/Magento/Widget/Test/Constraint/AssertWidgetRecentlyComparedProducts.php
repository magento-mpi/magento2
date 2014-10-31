<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Constraint;

use Magento\Catalog\Test\Constraint\AssertProductCompareSuccessAddMessage;
use Magento\Catalog\Test\Constraint\AssertProductCompareSuccessRemoveMessage;
use Magento\Catalog\Test\Page\Product\CatalogProductCompare;
use Mtf\Constraint\AbstractConstraint;
use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Client\Browser;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Mtf\Fixture\InjectableFixture;
use Magento\Backend\Test\Page\Adminhtml\AdminCache;
use Magento\Widget\Test\Fixture\Widget;

/**
 * Check that widget with type Recently Compared Products is present on Product Compare page
 */
class AssertWidgetRecentlyComparedProducts extends AbstractConstraint
{
    /**
     * Browser
     *
     * @var Browser
     */
    protected $browser;

    /**
     * Catalog product compare page
     *
     * @var CatalogProductCompare
     */
    protected $catalogProductCompare;

    /**
     * Catalog product page
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Cms index page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Assert that widget with type Recently Compared Products is present on Product Compare page
     *
     * @param AssertProductCompareSuccessAddMessage $assertProductCompareSuccessAddMessage
     * @param AssertProductCompareSuccessRemoveMessage $assertProductCompareSuccessRemoveMessage
     * @param CatalogProductCompare $catalogProductCompare
     * @param CmsIndex $cmsIndex
     * @param CatalogProductView $catalogProductView
     * @param Browser $browser
     * @param Widget $widget
     * @param AdminCache $adminCache
     * @var string
     * @return void
     */

    public function processAssert(
        AssertProductCompareSuccessAddMessage $assertProductCompareSuccessAddMessage,
        AssertProductCompareSuccessRemoveMessage $assertProductCompareSuccessRemoveMessage,
        CatalogProductCompare $catalogProductCompare,
        CmsIndex $cmsIndex,
        CatalogProductView $catalogProductView,
        Browser $browser,
        Widget $widget,
        AdminCache $adminCache
    ) {
        // Flush cache
        $adminCache->open();
        $adminCache->getActionsBlock()->flushMagentoCache();
        $adminCache->getMessagesBlock()->waitSuccessMessage();

        $this->catalogProductCompare = $catalogProductCompare;
        $this->catalogProductView = $catalogProductView;
        $this->browser = $browser;
        $this->cmsIndex = $cmsIndex;
        $products = [];

        $entities = $widget->getDataFieldConfig('widgetOptions')['source']->getEntities();
        foreach ($entities as $product) {
            $products[] = $product;
        }
        $cmsIndex->open();
        $this->addProducts($products, $assertProductCompareSuccessAddMessage);
        $this->removeCompareProduct($products, $assertProductCompareSuccessRemoveMessage);

        \PHPUnit_Framework_Assert::assertTrue(
            $this->catalogProductCompare->getWidgetView()->isWidgetVisible($widget, 'Recently Compared'),
            'Widget is absent on Product Compare page.'
        );
    }

    /**
     * Add products to compare list
     *
     * @param array $products
     * @param AbstractConstraint $assert
     * @return void
     */
    protected function addProducts(array $products, AbstractConstraint $assert)
    {
        foreach ($products as $itemProduct) {
            $this->browser->open($_ENV['app_frontend_url'] . $itemProduct->getUrlKey() . '.html');
            $this->catalogProductView->getViewBlock()->clickAddToCompare();
            $assert->processAssert($this->catalogProductView, $itemProduct);
        }
    }

    /**
     * Remove compare product
     *
     * @param array $products
     * @param AbstractConstraint $assert
     * @return void
     */
    protected function removeCompareProduct(array $products, AbstractConstraint $assert)
    {
        $this->cmsIndex->open();
        $this->cmsIndex->getLinksBlock()->openLink("Compare Products");
        foreach ($products as $itemProduct) {
            $this->catalogProductCompare->getCompareProductsBlock()->removeProduct();
            $assert->processAssert($this->catalogProductCompare, $itemProduct);
        }
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return "Widget with type Recently Compared Products is present on Product Compare page";
    }
}
