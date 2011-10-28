<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Report
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Report
 */
class Mage_Report_WidgetTest extends PHPUnit_Framework_TestCase
{
    public function testViewedProductsWidget()
    {
        $model = new Mage_Widget_Model_Widget_Instance;
        $config = $model->setType('reports/product_widget_viewed')->getWidgetConfig();
        $templates = $config->xpath('parameters/template/values');
        $templates = (array) $templates[0]->children();
        $this->assertArrayHasKey('default', $templates);
        $this->assertArrayHasKey('list', $templates);
        $this->assertArrayHasKey('list_default', $templates);
        $this->assertArrayHasKey('list_names', $templates);
        $this->assertArrayHasKey('list_images', $templates);

        $blocks = $config->xpath('supported_blocks');
        $blocks = (array) $blocks[0]->children();
        $this->assertArrayHasKey('left_column', $blocks);
        $this->assertArrayHasKey('main_content', $blocks);
        $this->assertArrayHasKey('right_column', $blocks);
    }

    public function testComparedProductsWidget()
    {
        $model = new Mage_Widget_Model_Widget_Instance;
        $config = $model->setType('reports/product_widget_compared')->getWidgetConfig();
        $templates = $config->xpath('parameters/template/values');
        $templates = (array) $templates[0]->children();
        $this->assertArrayHasKey('default', $templates);
        $this->assertArrayHasKey('list', $templates);
        $this->assertArrayHasKey('list_default', $templates);
        $this->assertArrayHasKey('list_names', $templates);
        $this->assertArrayHasKey('list_images', $templates);

        $blocks = $config->xpath('supported_blocks');
        $blocks = (array) $blocks[0]->children();
        $this->assertArrayHasKey('left_column', $blocks);
        $this->assertArrayHasKey('main_content', $blocks);
        $this->assertArrayHasKey('right_column', $blocks);
    }

}
