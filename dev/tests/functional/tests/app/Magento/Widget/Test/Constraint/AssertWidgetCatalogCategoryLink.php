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
 * Check that created widget displayed on frontend on Home page and on Advanced Search and
 * after click on widget link on frontend system redirects you to catalog page
 */
class AssertWidgetCatalogCategoryLink extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created widget displayed on frontend on Home page and on Advanced Search and
     * after click on widget link on frontend system redirects you to catalog page
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
        $widgetText = $widget->getWidgetOptions()[0]['entities'][0]->getName();

        \PHPUnit_Framework_Assert::assertTrue(
            $cmsIndex->getWidgetView()->isWidgetVisible($widget, $widgetText),
            'Widget with type catalog category link is absent on Home page.'
        );

        $cmsIndex->getWidgetView()->clickToWidget($widget, $widgetText);
        $title = $categoryView->getTitleBlock()->getTitle();
        \PHPUnit_Framework_Assert::assertEquals(
            $widgetText,
            $title,
            'Wrong category title.'
        );

        $cmsIndex->getSearchBlock()->clickAdvancedSearchButton();
        \PHPUnit_Framework_Assert::assertTrue(
            $cmsIndex->getWidgetView()->isWidgetVisible($widget, $widgetText),
            'Widget with type catalog category link is absent on Advanced Search page.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return "Created widget displayed on frontend on Home and Advanced Search pages.";
    }
}
