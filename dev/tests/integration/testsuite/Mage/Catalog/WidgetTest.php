<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Catalog_WidgetTest extends PHPUnit_Framework_TestCase
{
    public function testNewProductsWidget()
    {
        /** @var $model Mage_Widget_Model_Widget_Instance */
        $model = Mage::getModel('Mage_Widget_Model_Widget_Instance');
        $config = $model->setType('Mage_Catalog_Block_Product_Widget_New')->getWidgetConfigInArray();
        $templates = $config['parameters']['template']['values'];
        $this->assertArrayHasKey('default', $templates);
        $this->assertArrayHasKey('list', $templates);
        $this->assertArrayHasKey('list_default', $templates);
        $this->assertArrayHasKey('list_names', $templates);
        $this->assertArrayHasKey('list_images', $templates);

        $blocks = $config['supported_containers'];
        $this->assertArrayHasKey('left_column', $blocks);
        $this->assertArrayHasKey('main_content', $blocks);
        $this->assertArrayHasKey('right_column', $blocks);
    }
}
