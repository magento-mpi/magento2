<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\TestCase;

use Magento\Widget\Test\TestCase\AbstractCreateWidgetEntityTest;
use Magento\VersionsCms\Test\Fixture\Widget;

/**
 * Class CreateWidgetHierarchyNodeLinkTest
 * Test Creation for New Instance of WidgetEntity Hierarchy Node Link type
 */
class CreateWidgetHierarchyNodeLinkTest extends AbstractCreateWidgetEntityTest
{
    /**
     * Creation for New Instance of WidgetEntity
     *
     * @param Widget $widget
     * @return void
     */
    public function test(Widget $widget)
    {
        // Steps
        $this->widgetInstanceIndex->open();
        $this->widgetInstanceIndex->getPageActionsBlock()->addNew();
        $this->widgetInstanceNew->getWidgetForm()->fill($widget);
        $this->widgetInstanceEdit->getPageActionsBlock()->save();
    }
}
