<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Constraint;

use Magento\Backend\Test\Page\Adminhtml\AdminCache;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Mtf\Constraint\AbstractConstraint;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Widget\Test\Fixture\Widget;

/**
 * Class AssertWidgetCatalogLink
 * Check that after click on widget link on frontend system redirects you to catalog page
 */
class AssertWidgetCatalogLink extends AbstractConstraint
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
     * @param CatalogCategoryView $categoryView
     * @param Widget $widget
     * @param AdminCache $adminCache
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        CatalogCategoryView $categoryView,
        Widget $widget,
        AdminCache $adminCache
    ) {
        // Flush cache
        $adminCache->open();
        $adminCache->getActionsBlock()->flushMagentoCache();
        $adminCache->getMessagesBlock()->waitSuccessMessage();

        $cmsIndex->open();
        $widgetCode = $widget->getCode();
        $widgetText = $widget->getWidgetOptions()[0]['entities']['name'];
        $cmsIndex->getCmsPageBlock()->clickToWidget($widgetCode, $widgetText);
        $title = $categoryView->getTitleBlock()->getTitle();
        \PHPUnit_Framework_Assert::assertEquals(
            $widgetText,
            $title,
            'Wrong category title.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return "After click on widget link on frontend redirecting to catalog page was success.";
    }
}
