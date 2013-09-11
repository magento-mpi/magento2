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
        $model = Mage::getModel('\Magento\Widget\Model\Widget\Instance');
        $config = $model->setType('\Magento\Reports\Block\Product\Widget\Viewed')->getWidgetConfig();
        $templates = $config->xpath('parameters/template/values');
        $templates = (array) $templates[0]->children();
        $this->assertArrayHasKey('default', $templates);
        $this->assertArrayHasKey('list', $templates);
        $this->assertArrayHasKey('list_default', $templates);
        $this->assertArrayHasKey('list_names', $templates);
        $this->assertArrayHasKey('list_images', $templates);

        $blocks = $config->xpath('supported_containers');
        $blocks = (array) $blocks[0]->children();
        $this->assertArrayHasKey('left_column', $blocks);
        $this->assertArrayHasKey('main_content', $blocks);
        $this->assertArrayHasKey('right_column', $blocks);
    }

    public function testComparedProductsWidget()
    {
        $model = Mage::getModel('\Magento\Widget\Model\Widget\Instance');
        $config = $model->setType('\Magento\Reports\Block\Product\Widget\Compared')->getWidgetConfig();
        $templates = $config->xpath('parameters/template/values');
        $templates = (array) $templates[0]->children();
        $this->assertArrayHasKey('default', $templates);
        $this->assertArrayHasKey('list', $templates);
        $this->assertArrayHasKey('list_default', $templates);
        $this->assertArrayHasKey('list_names', $templates);
        $this->assertArrayHasKey('list_images', $templates);

        $blocks = $config->xpath('supported_containers');
        $blocks = (array) $blocks[0]->children();
        $this->assertArrayHasKey('left_column', $blocks);
        $this->assertArrayHasKey('main_content', $blocks);
        $this->assertArrayHasKey('right_column', $blocks);
    }

}
