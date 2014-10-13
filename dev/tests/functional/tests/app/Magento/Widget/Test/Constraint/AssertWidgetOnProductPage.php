<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Constraint;

use Magento\Backend\Test\Page\Adminhtml\AdminCache;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Mtf\Constraint\AbstractConstraint;
use Magento\Widget\Test\Fixture\Widget;
use Mtf\Client\Browser;

/**
 * Class AssertWidgetOnProductPage
 * Check that created widget displayed on frontend on Product page
 */
class AssertWidgetOnProductPage extends AbstractConstraint
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
        $widgetText = $widget->getWidgetOptions()[0]['link_text'];

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
