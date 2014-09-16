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

/**
 * Class AssertWidgetAbsentOnFrontendHome
 * Check that created widget does NOT displayed on frontent on Home page
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
     * Assert that created widget displayed on frontent on Home page and on Advanced Search
     *
     * @param CmsIndex $cmsIndex
     * @param Widget $widget
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        Widget $widget
    ) {
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
     */
    public function toString()
    {
        return "Widget is absent on Home page";
    }
}
