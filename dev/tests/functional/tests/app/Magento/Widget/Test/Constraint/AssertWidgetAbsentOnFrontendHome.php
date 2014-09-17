<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
use Magento\Widget\Test\Fixture\Widget;
use Mtf\Constraint\AbstractConstraint;
use Magento\Backend\Test\Page\Adminhtml\AdminCache;

/**
 * Class AssertWidgetAbsentOnFrontendHome
 * Check that created widget does NOT displayed on frontend on Home page
 */
class AssertWidgetAbsentOnFrontendHome extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created widget displayed on frontend on Home page
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
        $adminCache->getMessagesBlock()->assertSuccessMessage();

        $cmsIndex->open();
        $widgetCode = $widget->getCode();
        \PHPUnit_Framework_Assert::assertFalse(
            $cmsIndex->getCmsPageBlock()->isWidgetVisible($widgetCode),
            'Widget is present on Home page.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     * @return string
     */
    public function toString()
    {
        return "Widget is absent on Home page";
    }
}
