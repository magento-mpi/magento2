<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Constraint;

use Magento\Backend\Test\Page\Adminhtml\AdminCache;
use Mtf\Constraint\AbstractConstraint;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Widget\Test\Fixture\Widget;

/**
 * Class AssertWidgetCmsPageLink
 * Check that after click on widget link on frontend system redirects you to catalog page
 */
class AssertWidgetCmsPageLink extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that after click on widget link on frontend system redirects you to catalog page
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
        $title = $widget->getWidgetOptions()[0]['entities'][0]->getContentHeading();
        $cmsIndex->getCmsPageBlock()->clickToWidget($widgetCode, $widgetText);
        $pageTitle = $cmsIndex->getCmsPageBlock()->getPageTitle();
        \PHPUnit_Framework_Assert::assertEquals(
            $title,
            $pageTitle,
            'Wrong page title on Cms page.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return "After click no widget link on frontend redirecting to Cms page was success.";
    }
}
