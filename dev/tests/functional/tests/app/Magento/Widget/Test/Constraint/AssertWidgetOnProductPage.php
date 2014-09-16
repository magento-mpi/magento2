<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Constraint;

use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Mtf\Constraint\AbstractConstraint;
use Magento\Widget\Test\Fixture\Widget;
use Mtf\Client\Browser;

/**
 * Class AssertWidgetOnProductPage
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
     * @return void
     */
    public function processAssert(
        CatalogProductView $productView,
        Browser $browser,
        Widget $widget
    ) {
        $urlKey = $widget->getLayout()[0]['entities']['url_key'];
        $browser->open($_ENV['app_frontend_url'] . $urlKey . '.html');
        $widgetCode = $widget->getCode();
        if ($widget->getWidgetOptions()[0]['name'] == 'bannerRotatorCatalogRules') {
            $widgetText = $widget->getWidgetOptions()[0]['entities']['store_contents']['value_0'];
        } else {
            $widgetText = $widget->getWidgetOptions()[0]['link_text'];
        }
        \PHPUnit_Framework_Assert::assertTrue(
            $productView->getViewBlock()->isWidgetVisible($widgetCode, $widgetText),
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
