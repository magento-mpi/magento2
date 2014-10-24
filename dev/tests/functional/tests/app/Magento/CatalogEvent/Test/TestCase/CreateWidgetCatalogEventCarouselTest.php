<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\TestCase;

use Magento\Widget\Test\TestCase\AbstractCreateWidgetEntityTest;
use Magento\CatalogEvent\Test\Fixture\Widget;

/**
 * Class CreateWidgetCatalogEventCarouselTest
 * Test Creation for New Instance of WidgetEntity Catalog Event Carousel type
 */
class CreateWidgetCatalogEventCarouselTest extends AbstractCreateWidgetEntityTest
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
