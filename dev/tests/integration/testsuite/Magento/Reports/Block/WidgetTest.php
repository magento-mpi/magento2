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
<<<<<<< HEAD:dev/tests/integration/testsuite/Mage/Reports/Block/WidgetTest.php
        $model = Mage::getModel('Mage_Widget_Model_Widget_Instance');
        $config = $model->setType('Mage_Reports_Block_Product_Widget_Viewed')->getWidgetConfigInArray();

        $this->assertArrayHasKey('parameters', $config);
        $templates = $config['parameters'];
        $this->assertArrayHasKey('template', $templates);
        $templates = $templates['template'];
        $this->assertArrayHasKey('values', $templates);
        $templates = $templates['values'];

=======
        $model = Mage::getModel('Magento_Widget_Model_Widget_Instance');
        $config = $model->setType('Magento_Reports_Block_Product_Widget_Viewed')->getWidgetConfig();
        $templates = $config->xpath('parameters/template/values');
        $templates = (array) $templates[0]->children();
>>>>>>> upstream/develop:dev/tests/integration/testsuite/Magento/Reports/Block/WidgetTest.php
        $this->assertArrayHasKey('default', $templates);
        $this->assertArrayHasKey('list', $templates);
        $this->assertArrayHasKey('list_default', $templates);
        $this->assertArrayHasKey('list_names', $templates);
        $this->assertArrayHasKey('list_images', $templates);

        $this->assertArrayHasKey('supported_containers', $config);
        $blocks = $config['supported_containers'];

        $this->assertArrayHasKey('left_column', $blocks);
        $this->assertArrayHasKey('main_content', $blocks);
        $this->assertArrayHasKey('right_column', $blocks);
    }

    public function testComparedProductsWidget()
    {
<<<<<<< HEAD:dev/tests/integration/testsuite/Mage/Reports/Block/WidgetTest.php
        $model = Mage::getModel('Mage_Widget_Model_Widget_Instance');
        $config = $model->setType('Mage_Reports_Block_Product_Widget_Compared')->getWidgetConfigInArray();

        $this->assertArrayHasKey('parameters', $config);
        $templates = $config['parameters'];
        $this->assertArrayHasKey('template', $templates);
        $templates = $templates['template'];
        $this->assertArrayHasKey('values', $templates);
        $templates = $templates['values'];

=======
        $model = Mage::getModel('Magento_Widget_Model_Widget_Instance');
        $config = $model->setType('Magento_Reports_Block_Product_Widget_Compared')->getWidgetConfig();
        $templates = $config->xpath('parameters/template/values');
        $templates = (array) $templates[0]->children();
>>>>>>> upstream/develop:dev/tests/integration/testsuite/Magento/Reports/Block/WidgetTest.php
        $this->assertArrayHasKey('default', $templates);
        $this->assertArrayHasKey('list', $templates);
        $this->assertArrayHasKey('list_default', $templates);
        $this->assertArrayHasKey('list_names', $templates);
        $this->assertArrayHasKey('list_images', $templates);

        $this->assertArrayHasKey('supported_containers', $config);
        $blocks = $config['supported_containers'];

        $this->assertArrayHasKey('left_column', $blocks);
        $this->assertArrayHasKey('main_content', $blocks);
        $this->assertArrayHasKey('right_column', $blocks);
    }

}
