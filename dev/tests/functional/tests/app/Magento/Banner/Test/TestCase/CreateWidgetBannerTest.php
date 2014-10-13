<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\TestCase;

use Magento\Widget\Test\TestCase\CreateWidgetEntityTest;
use Magento\Banner\Test\Fixture\Widget;

/**
 * Class CreateWidgetBannerTest
 * Test Creation for New Instance of WidgetEntity Banner Rotator
 */
class CreateWidgetBannerTest extends CreateWidgetEntityTest
{
    /**
     * Creation for New Instance of WidgetEntity
     *
     * @param Widget $widget
     * @return void
     */
    public function test(Widget $widget)
    {
        parent::test($widget);
    }

    /**
     * Removing widget, catalog rules and sales rules
     *
     * @return void
     */
    public function tearDown()
    {
        if ($this->widget !== null) {
            $filter = ['title' => $this->widget->getTitle()];
            $this->widgetInstanceIndex->open();
            $this->widgetInstanceIndex->getWidgetGrid()->searchAndOpen($filter);
            $this->widgetInstanceEdit->getPageActionsBlock()->delete();

            if (isset($this->widget->getWidgetOptions()[0]['entities']['banner_catalog_rules'])) {
                $deleteCatalogRule = $this->objectManager
                    ->create('Magento\CatalogRule\Test\TestStep\DeleteAllCatalogRulesStep');
                $deleteCatalogRule->run();
            }
            if (isset($this->widget->getWidgetOptions()[0]['entities']['banner_sales_rules'])) {
                $deleteSalesRule = $this->objectManager
                    ->create('Magento\SalesRule\Test\TestStep\DeleteAllSalesRuleStep');
                $deleteSalesRule->run();
            }
        }
    }
}
