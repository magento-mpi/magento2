<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Widget
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Widget
 */
class Mage_Widget_Model_Widget_InstanceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return Mage_Widget_Model_Widget_Instance
     */
    public function testGetWidgetConfig()
    {
        $model = new Mage_Widget_Model_Widget_Instance;
        $config = $model->setType('catalog/product_widget_new')->getWidgetConfig();
        $this->assertInstanceOf('Varien_Simplexml_Element', $config);
        /** @var Varien_Simplexml_Element $config */
        $element = $config->xpath('/widgets/new_products/parameters/template/values/list');
        $this->assertArrayHasKey(0, $element);
        $this->assertInstanceOf('Varien_Simplexml_Element', $element[0]);
        return $model;
    }

    /**
     * @param Mage_Widget_Model_Widget_Instance $model
     * @depends testGetWidgetConfig
     */
    public function testGenerateLayoutUpdateXml(Mage_Widget_Model_Widget_Instance $model)
    {
        $this->assertEquals('', $model->generateLayoutUpdateXml('content'));
        $model->setId('test_id')->setPackageTheme('default/default');
        $result = $model->generateLayoutUpdateXml('content');
        $this->assertContains('<reference name="content">', $result);
        $this->assertContains('<block type="' . $model->getType() . '"', $result);
    }

    public function testSetGetType()
    {
        $model = new Mage_Widget_Model_Widget_Instance();
        $this->assertEmpty($model->getType());

        $model->setType('test-test');
        $this->assertEquals('test/test', $model->getType());

        $model->setData('instance_type', 'test-test');
        $this->assertEquals('test/test', $model->getType());
    }
}
