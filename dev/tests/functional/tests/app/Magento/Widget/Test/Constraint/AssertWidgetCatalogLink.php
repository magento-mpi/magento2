<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Constraint;

use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Mtf\Constraint\AbstractConstraint;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Widget\Test\Fixture\Widget;

/**
 * Class AssertWidgetCatalogLink
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
     * @param Widget $widgetEdit
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        CatalogCategoryView $categoryView,
        Widget $widget,
        Widget $widgetEdit
    ) {
        $cmsIndex->open();
        $widgetCode = $widget->getCode();
        $widgetText = $widgetEdit->getWidgetOptions()[0]['entities'];
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
        return "Widget link on frontend system redirects to catalog page.";
    }
}
