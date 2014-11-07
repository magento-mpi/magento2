<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Constraint;

use Magento\Backend\Test\Page\Adminhtml\AdminCache;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Widget\Test\Fixture\Widget;
use Mtf\Constraint\AbstractConstraint;
use Magento\CatalogSearch\Test\Page\AdvancedSearch;

/**
 * Check that created Banner Rotator widget displayed on frontend on Home page and on Advanced Search
 */
class AssertWidgetBannerRotator extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created Banner Rotator widget displayed on frontent on Home page and on Advanced Search
     *
     * @param CmsIndex $cmsIndex
     * @param AdvancedSearch $advancedSearch
     * @param Widget $widget
     * @param AdminCache $adminCache
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        AdvancedSearch $advancedSearch,
        Widget $widget,
        AdminCache $adminCache
    ) {
        // Flush cache
        $adminCache->open();
        $adminCache->getActionsBlock()->flushMagentoCache();
        $adminCache->getMessagesBlock()->waitSuccessMessage();

        $cmsIndex->open();
        $widgetText = $widget->getWidgetOptions()[0]['entities'][0]->getStoreContents()['value_0'];
        \PHPUnit_Framework_Assert::assertTrue(
            $cmsIndex->getWidgetView()->isWidgetVisible($widget, $widgetText),
            'Widget with type ' . $widget->getCode() . ' is absent on Home page.'
        );
        $cmsIndex->getSearchBlock()->clickAdvancedSearchButton();
        \PHPUnit_Framework_Assert::assertTrue(
            $advancedSearch->getWidgetView()->isWidgetVisible($widget, $widgetText),
            'Widget with type ' . $widget->getCode() . ' is absent on Advanced Search page.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return "Widget is present on Home page and on Advanced Search.";
    }
}
