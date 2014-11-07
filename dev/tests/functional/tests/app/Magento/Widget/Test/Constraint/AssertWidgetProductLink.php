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
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Widget\Test\Fixture\Widget;

/**
 * Check that after click on widget link on frontend system redirects you to Product page defined in widget
 */
class AssertWidgetProductLink extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that after click on widget link on frontend system redirects you to Product page defined in widget
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogProductView $productView
     * @param Widget $widget
     * @param AdminCache $adminCache
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        CatalogProductView $productView,
        Widget $widget,
        AdminCache $adminCache
    ) {
        // Flush cache
        $adminCache->open();
        $adminCache->getActionsBlock()->flushMagentoCache();
        $adminCache->getMessagesBlock()->waitSuccessMessage();

        $cmsIndex->open();
        $widgetText = $widget->getWidgetOptions()[0]['entities']['name'];
        $cmsIndex->getWidgetView()->clickToWidget($widget, $widgetText);
        $title = $productView->getTitleBlock()->getTitle();
        \PHPUnit_Framework_Assert::assertEquals(
            $widgetText,
            $title,
            'Wrong product title.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return "Widget link on frontend system redirects to Product page defined in widget.";
    }
}
