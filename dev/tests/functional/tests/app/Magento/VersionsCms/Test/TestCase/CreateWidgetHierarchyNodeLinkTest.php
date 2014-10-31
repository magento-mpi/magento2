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
 * Test Creation for New Instance of WidgetEntity Hierarchy Node Link type
 *
 * Test Flow:
 *
 * Steps:
 * 1. Login to the backend
 * 2. Open Content > Frontend Apps
 * 3. Click Add new Widget Instance
 * 4. Fill settings data according dataset
 * 5. Click button Continue
 * 6. Fill widget data according dataset
 * 7. Perform all assertions
 *
 * @group Widget_(PS)
 * @ZephyrId MAGETWO-27916
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
