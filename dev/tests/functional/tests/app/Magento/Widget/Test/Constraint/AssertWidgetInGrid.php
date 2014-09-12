<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Widget\Test\Fixture\Widget;
use Magento\Widget\Test\Page\Adminhtml\WidgetInstanceIndex;

/**
 * Class AssertWidgetInGrid
 */
class AssertWidgetInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert widget availability in widget grid
     *
     * @param Widget $widgetEdit
     * @param WidgetInstanceIndex $widgetInstanceIndex
     * @return void
     */
    public function processAssert(Widget $widgetEdit, WidgetInstanceIndex $widgetInstanceIndex)
    {
        $filter = ['title' => $widgetEdit->getTitle()];
        $widgetInstanceIndex->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $widgetInstanceIndex->getWidgetGrid()->isRowVisible($filter),
            'Widget with title \'' . $widgetEdit->getTitle() . '\' is absent in Widget grid.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Widget is present in widget grid.';
    }
}
