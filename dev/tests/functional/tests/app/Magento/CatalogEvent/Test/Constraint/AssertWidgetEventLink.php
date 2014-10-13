<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Backend\Test\Page\Adminhtml\AdminCache;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Widget\Test\Fixture\Widget;

/**
 * Class AssertWidgetEventLink
 * Check that link "Go To Sale" on event carousel widget redirects you to category page
 */
class AssertWidgetEventLink extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that link "Go To Sale" on event carousel widget redirects you to category page
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
        $categoryName = $widget->getWidgetOptions()[0]['entities'][1]->getCategoryId()[0];
        $cmsIndex->getCmsPageBlock()->clickToWidget($widgetCode, 'Go To Sale');
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
