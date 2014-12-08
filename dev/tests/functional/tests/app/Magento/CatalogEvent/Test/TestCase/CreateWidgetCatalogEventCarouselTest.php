<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\TestCase;

use Magento\CatalogEvent\Test\Fixture\Widget;
use Magento\Widget\Test\TestCase\AbstractCreateWidgetEntityTest;

/**
 * Test Flow:
 *
 * Steps:
 * 1. Login to the backend
 * 2. Open Content > Frontend Apps
 * 3. Click Add new Widget Instance
 * 4. Fill settings data for Catalog Event Carousel widget type according dataset
 * 5. Click button Continue
 * 6. Fill widget data according dataset
 * 7. Perform all assertions
 *
 * @group Widget_(PS)
 * @ZephyrId MAGETWO-27916
 */
class CreateWidgetCatalogEventCarouselTest extends AbstractCreateWidgetEntityTest
{
    /**
     * Delete all Catalog Events on backend.
     *
     * @return void
     */
    public function __prepare()
    {
        $this->objectManager->create('Magento\CatalogEvent\Test\TestStep\DeleteAllCatalogEventsStep')->run();
    }

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
