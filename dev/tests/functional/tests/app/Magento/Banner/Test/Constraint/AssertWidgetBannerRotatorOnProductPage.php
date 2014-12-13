<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Banner\Test\Constraint;

use Magento\Backend\Test\Page\Adminhtml\AdminCache;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Widget\Test\Constraint\AssertWidgetOnProductPage;
use Magento\Widget\Test\Fixture\Widget;
use Mtf\Client\Browser;

/**
 * Check that created Banner Rotator widget displayed on frontend on Product page
 */
class AssertWidgetBannerRotatorOnProductPage extends AssertWidgetOnProductPage
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that created Banner Rotator widget displayed on frontend on Product page
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
        $widgetText = $widget->getWidgetOptions()[0]['entities'][0]->getStoreContents()['value_0'];
        \PHPUnit_Framework_Assert::assertTrue(
            $productView->getWidgetView()->isWidgetVisible($widget, $widgetText),
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
