<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\Constraint;

use Magento\CatalogEvent\Test\Fixture\CatalogEventEntity;
use Mtf\Constraint\AbstractConstraint;
use Magento\Backend\Test\Page\Adminhtml\AdminCache;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Widget\Test\Fixture\Widget;

/**
 * Check that widget catalog event carousel is present on category page and link "Go To Sale" on widget redirects
 * you to category page
 */
class AssertWidgetCatalogEvent extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that widget catalog event carousel is present on category page and link "Go To Sale" on widget redirects
     * you to category page
     *
     * @param CmsIndex $cmsIndex
     * @param Widget $widget
     * @param CatalogCategoryView $catalogCategoryView
     * @param AdminCache $adminCache
     * @param CatalogEventEntity $event1
     * @param CatalogEventEntity $event2
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        Widget $widget,
        CatalogCategoryView $catalogCategoryView,
        AdminCache $adminCache,
        CatalogEventEntity $event1,
        CatalogEventEntity $event2
    ) {
        // Flush cache
        $adminCache->open();
        $adminCache->getActionsBlock()->flushMagentoCache();
        $adminCache->getMessagesBlock()->waitSuccessMessage();

        $event1->persist();
        $event2->persist();
        $cmsIndex->open();
        $categoryName = $event2->getCategoryId();
        $cmsIndex->getTopmenu()->selectCategoryByName($categoryName);
        \PHPUnit_Framework_Assert::assertTrue(
            $catalogCategoryView->getWidgetView()->isWidgetVisible($widget, $categoryName),
            'Widget is absent on Category page.'
        );

        $cmsIndex->getWidgetView()->clickToWidget($widget, 'Go To Sale');
        $pageTitle = $cmsIndex->getCmsPageBlock()->getPageTitle();
        \PHPUnit_Framework_Assert::assertEquals(
            $categoryName,
            $pageTitle,
            'Wrong page title on Category page.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return "After click no event carousel widget link on frontend redirecting to Category page was success.";
    }
}
