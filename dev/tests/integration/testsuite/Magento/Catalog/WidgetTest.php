<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Catalog_WidgetTest extends PHPUnit_Framework_TestCase
{
    public function testNewProductsWidget()
    {
        /** @var $model \Magento\Widget\Model\Widget\Instance */
        $model = Mage::getModel('Magento\Widget\Model\Widget\Instance');
        $config = $model->setType('\Magento\Catalog\Block\Product\Widget\New')->getWidgetConfig();
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
