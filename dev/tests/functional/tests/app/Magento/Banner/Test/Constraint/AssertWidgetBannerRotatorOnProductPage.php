<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Constraint;

use Magento\Backend\Test\Page\Adminhtml\AdminCache;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Widget\Test\Constraint\AssertWidgetOnProductPage;
use Magento\Widget\Test\Fixture\Widget;
use Mtf\Client\Browser;

/**
 * Class AssertWidgetBannerRotatorOnProductPage
 * Check that created widget displayed on frontend on Product page
 */
class AssertWidgetBannerRotatorOnProductPage extends AssertWidgetOnProductPage
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created widget displayed on frontend on Product page
     *
     * @param CatalogProductView $productView
     * @param Browser $browser
     * @param Widget $widget
     * @param AdminCache $adminCache
     * @return void
     */
    public function processAssert(
        CatalogProductView $productView,
        Browser $browser,
        Widget $widget,
        AdminCache $adminCache
    ) {
        // Flush cache
        $adminCache->open();
        $adminCache->getActionsBlock()->flushMagentoCache();
        $adminCache->getMessagesBlock()->waitSuccessMessage();

        $urlKey = $widget->getLayout()[0]['entities']['url_key'];
        $browser->open($_ENV['app_frontend_url'] . $urlKey . '.html');
        $widgetCode = $widget->getCode();
        $widgetText = $widget->getWidgetOptions()[0]['entities']['store_contents']['value_0'];

        \PHPUnit_Framework_Assert::assertTrue(
            $productView->getWidgetBlock()->isWidgetVisible($widgetCode, $widgetText),
            'Widget is absent on Product page.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return "Widget is present on Product page";
    }
}
