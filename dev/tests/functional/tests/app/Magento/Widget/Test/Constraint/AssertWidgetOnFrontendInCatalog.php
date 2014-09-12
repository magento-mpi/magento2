<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Constraint;

use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\Widget\Test\Fixture\Widget;

/**
 * Class AssertWidgetOnFrontendInCatalog
 */
class AssertWidgetOnFrontendInCatalog extends AbstractConstraint
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
     * @param CatalogCategoryView $catalogCategoryView
     * @param Widget $widget
     * @param Widget $widgetEdit
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        CatalogCategoryView $catalogCategoryView,
        Widget $widget,
        Widget $widgetEdit
    ) {
        $cmsIndex->open();
        if (isset($widgetEdit->getLayout()[0]['entities'])) {
            $categoryName = $widgetEdit->getLayout()[0]['entities']['name'];
        } else {
            $categoryName = $widgetEdit->getWidgetOptions()[0]['entities']['category_id'][1];
        }
        $widgetCode = $widget->getCode();
        if ($widgetEdit->getWidgetOptions()[0]['name'] == 'cmsStaticBlock') {
            $widgetText = $widgetEdit->getWidgetOptions()[0]['entities']['content'];
        } else {
            $widgetText = $widgetEdit->getWidgetOptions()[0]['entities'];
        }
        $cmsIndex->getTopmenu()->selectCategoryByName($categoryName);
        \PHPUnit_Framework_Assert::assertTrue(
            $catalogCategoryView->getViewBlock()->isWidgetVisible($widgetCode, $widgetText),
            'Widget is absent on Category page.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return "Widget is present on Category page";
    }
}
