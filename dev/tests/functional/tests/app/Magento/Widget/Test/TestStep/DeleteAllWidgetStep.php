<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\TestStep;

use Mtf\TestStep\TestStepInterface;
use Magento\Widget\Test\Page\Adminhtml\WidgetInstanceEdit;
use Magento\Widget\Test\Page\Adminhtml\WidgetInstanceIndex;

/**
 * Class DeleteAllWidgetStep
 * Delete all Widget on backend
 */
class DeleteAllWidgetStep implements TestStepInterface
{
    /**
     * WidgetInstanceIndex page
     *
     * @var WidgetInstanceIndex
     */
    protected $widgetInstanceIndex;

    /**
     * WidgetInstanceEdit page
     *
     * @var WidgetInstanceEdit
     */
    protected $widgetInstanceEdit;

    /**
     * @construct
     * @param WidgetInstanceIndex $widgetInstanceIndex
     * @param WidgetInstanceEdit $widgetInstanceEdit
     */
    public function __construct(
        WidgetInstanceIndex $widgetInstanceIndex,
        WidgetInstanceEdit $widgetInstanceEdit
    ) {
        $this->widgetInstanceIndex = $widgetInstanceIndex;
        $this->widgetInstanceEdit = $widgetInstanceEdit;
    }

    /**
     * Delete Widget on backend
     *
     * @return array
     */
    public function run()
    {
        $this->widgetInstanceIndex->open();
        while ($this->widgetInstanceIndex->getWidgetGrid()->isFirstRowVisible()) {
            $this->widgetInstanceIndex->getWidgetGrid()->openFirstRow();
            $this->widgetInstanceEdit->getPageActionsBlock()->delete();
        }
    }
}
