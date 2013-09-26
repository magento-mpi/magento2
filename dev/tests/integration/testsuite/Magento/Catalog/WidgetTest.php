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
        /** @var $model Magento_Widget_Model_Widget_Instance */
        $model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Widget_Model_Widget_Instance');
        $config = $model->setType('Magento_Catalog_Block_Product_Widget_New')->getWidgetConfigAsArray();
        $templates = $config['parameters']['template']['values'];
        $this->assertArrayHasKey('default', $templates);
        $this->assertArrayHasKey('list', $templates);
        $this->assertArrayHasKey('list_default', $templates);
        $this->assertArrayHasKey('list_names', $templates);
        $this->assertArrayHasKey('list_images', $templates);

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
