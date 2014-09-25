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
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        CatalogCategoryView $catalogCategoryView,
        Widget $widget
    ) {
        $cmsIndex->open();
        if (isset($widget->getLayout()[0]['entities'])) {
            $categoryName = $widget->getLayout()[0]['entities']['name'];
        } else {
            $categoryName = $widget->getWidgetOptions()[0]['entities']['category_id'][0];
        }
        $widgetCode = $widget->getCode();
        if ($widget->getWidgetOptions()[0]['name'] == 'cmsStaticBlock') {
            $widgetText = $widget->getWidgetOptions()[0]['entities']['content'];
        } else {
            $widgetText = $widget->getWidgetOptions()[0]['entities']['name'];
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
