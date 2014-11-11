<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\TestCase;

use Magento\Widget\Test\TestCase\AbstractCreateWidgetEntityTest;
use Magento\Banner\Test\Fixture\Widget;

/**
 * Test Flow:
 *
 * Steps:
 * 1. Login to the backend
 * 2. Open Content > Frontend Apps
 * 3. Click Add new Widget Instance
 * 4. Fill settings data for Banner widget type according dataset
 * 5. Click button Continue
 * 6. Fill widget data according dataset
 * 7. Perform all assertions
 *
 * @group Widget_(PS)
 * @ZephyrId MAGETWO-27916
 */
class CreateWidgetBannerTest extends AbstractCreateWidgetEntityTest
{
    /**
     * Widget fixture
     *
     * @var Widget
     */
    protected $widget;

    /**
     * Creation for New Instance of WidgetEntity
     *
     * @param Widget $widget
     * @return void
     */
    public function test(Widget $widget)
    {
        // Steps
        $this->widget = $widget;
        $this->widgetInstanceIndex->open();
        $this->widgetInstanceIndex->getPageActionsBlock()->addNew();
        $this->widgetInstanceNew->getWidgetForm()->fill($widget);
        $this->widgetInstanceEdit->getPageActionsBlock()->save();
    }

    /**
     * Removing widget, catalog rules and sales rules
     *
     * @return void
     */
    public function tearDown()
    {
        if ($this->widget !== null) {
            $this->objectManager->create('Magento\Widget\Test\TestStep\DeleteAllWidgetsStep')->run();
            if (isset($this->widget->getWidgetOptions()[0]['entities']['banner_catalog_rules'])) {
                $this->objectManager->create('Magento\CatalogRule\Test\TestStep\DeleteAllCatalogRulesStep')->run();
            }
            if (isset($this->widget->getWidgetOptions()[0]['entities']['banner_sales_rules'])) {
                $this->objectManager->create('Magento\SalesRule\Test\TestStep\DeleteAllSalesRuleStep')->run();
            }
        }
    }
}
