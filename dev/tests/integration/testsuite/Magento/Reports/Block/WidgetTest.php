<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Reports_Block_WidgetTest extends PHPUnit_Framework_TestCase
{
    public function testViewedProductsWidget()
    {
        $model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Widget_Model_Widget_Instance');
        $config = $model->setType('Magento_Reports_Block_Product_Widget_Viewed')->getWidgetConfigAsArray();

        $this->assertArrayHasKey('parameters', $config);
        $templates = $config['parameters'];
        $this->assertArrayHasKey('template', $templates);
        $templates = $templates['template'];
        $this->assertArrayHasKey('values', $templates);
        $templates = $templates['values'];

        $this->assertArrayHasKey('default', $templates);
        $this->assertArrayHasKey('list', $templates);
        $this->assertArrayHasKey('list_default', $templates);
        $this->assertArrayHasKey('list_names', $templates);
        $this->assertArrayHasKey('list_images', $templates);

        $this->assertArrayHasKey('supported_containers', $config);
        $blocks = $config['supported_containers'];

        $containers = array();
        foreach ($blocks as $block) {
            $containers[] = $block['container_name'];
        }

        $this->assertContains('left', $containers);
        $this->assertContains('content', $containers);
        $this->assertContains('right', $containers);
    }

    public function testComparedProductsWidget()
    {
        $model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Widget_Model_Widget_Instance');
        $config = $model->setType('Magento_Reports_Block_Product_Widget_Compared')->getWidgetConfigAsArray();

        $this->assertArrayHasKey('parameters', $config);
        $templates = $config['parameters'];
        $this->assertArrayHasKey('template', $templates);
        $templates = $templates['template'];
        $this->assertArrayHasKey('values', $templates);
        $templates = $templates['values'];

        $this->assertArrayHasKey('default', $templates);
        $this->assertArrayHasKey('list', $templates);
        $this->assertArrayHasKey('list_default', $templates);
        $this->assertArrayHasKey('list_names', $templates);
        $this->assertArrayHasKey('list_images', $templates);

        $this->assertArrayHasKey('supported_containers', $config);
        $blocks = $config['supported_containers'];
        $containers = array();
        foreach ($blocks as $block) {
            $containers[] = $block['container_name'];
        }

        $this->assertContains('left', $containers);
        $this->assertContains('content', $containers);
        $this->assertContains('right', $containers);
    }

}
