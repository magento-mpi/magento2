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

/**
 * Class AssertWidgetBannerRotatorOnFrontendAllPages
 * Check that created widget displayed on frontent on Home page and on Advanced Search
 */
class AssertWidgetBannerRotatorOnFrontendAllPages extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created widget displayed on frontent on Home page and on Advanced Search
     *
     * @param CmsIndex $cmsIndex
     * @param Widget $widget
     * @param AdminCache $adminCache
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        Widget $widget,
        AdminCache $adminCache
    ) {
        // Flush cache
        $adminCache->open();
        $adminCache->getActionsBlock()->flushMagentoCache();
        $adminCache->getMessagesBlock()->waitSuccessMessage();

        $cmsIndex->open();
        $widgetCode = $widget->getCode();
        $widgetText = $widget->getWidgetOptions()[0]['entities'][0]->getStoreContents()['value_0'];
        \PHPUnit_Framework_Assert::assertTrue(
            $cmsIndex->getCmsPageBlock()->isWidgetVisible($widgetCode, $widgetText),
            'Widget with type ' . $widgetCode . ' is absent on Home page.'
        );
        $cmsIndex->getSearchBlock()->clickAdvancedSearchButton();
        \PHPUnit_Framework_Assert::assertTrue(
            $cmsIndex->getCmsPageBlock()->isWidgetVisible($widgetCode, $widgetText),
            'Widget with type ' . $widgetCode . ' is absent on Home page.'
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
