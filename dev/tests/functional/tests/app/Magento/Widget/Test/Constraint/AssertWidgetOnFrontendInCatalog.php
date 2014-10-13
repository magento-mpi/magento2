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
use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\Widget\Test\Fixture\Widget;

/**
 * Class AssertWidgetOnFrontendInCatalog
 * Check that created widget displayed on frontent in Catalog
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
     * Assert that created widget displayed on frontent in Catalog
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogCategoryView $catalogCategoryView
     * @param Widget $widget
     * @param AdminCache $adminCache
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        CatalogCategoryView $catalogCategoryView,
        Widget $widget,
        AdminCache $adminCache
    ) {
        // Flush cache
        $adminCache->open();
        $adminCache->getActionsBlock()->flushMagentoCache();
        $adminCache->getMessagesBlock()->waitSuccessMessage();

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
            $catalogCategoryView->getWidgetBlock()->isWidgetVisible($widgetCode, $widgetText),
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
