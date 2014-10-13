<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Constraint;

use Magento\Backend\Test\Page\Adminhtml\AdminCache;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Widget\Test\Fixture\Widget;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertWidgetCmsPageLinkOnFrontendAllPages
 * Check that created widget displayed on frontent on Home page and on Advanced Search
 */
class AssertWidgetCmsPageLinkOnFrontendAllPages extends AbstractConstraint
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
        $widgetText = $widget->getWidgetOptions()[0]['anchor_text'];
        \PHPUnit_Framework_Assert::assertTrue(
            $cmsIndex->getCmsPageBlock()->isWidgetVisible($widgetCode, $widgetText),
            'Widget with type CmsPageLink is absent on Home page.'
        );
        $cmsIndex->getSearchBlock()->clickAdvancedSearchButton();
        \PHPUnit_Framework_Assert::assertTrue(
            $cmsIndex->getCmsPageBlock()->isWidgetVisible($widgetCode, $widgetText),
            'Widget with type CmsPageLink is absent on Advanced Search page.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return "Widget with type CmsPageLink is present on Home page and on Advanced Search.";
    }
}
