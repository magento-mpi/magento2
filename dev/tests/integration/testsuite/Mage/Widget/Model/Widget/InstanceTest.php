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
    public function testSetGetType()
    {
        $model = new Mage_Widget_Model_Widget_Instance;
        $this->assertEmpty($model->getType());
        $this->assertSame('test', $model->setType('test')->getType());
        $this->assertSame('test', $model->getInstanceType());
    }

    /**
     * @return Mage_Widget_Model_Widget_Instance
     */
    public function testGetWidgetConfig()
    {
        $model = new Mage_Widget_Model_Widget_Instance;
        $config = $model->setType('Mage_Catalog_Block_Product_Widget_New')->getWidgetConfig();
        $this->assertInstanceOf('Varien_Simplexml_Element', $config);
        /** @var Varien_Simplexml_Element $config */
        $element = $config->xpath('/widgets/new_products/parameters/template/values/list');
        $this->assertArrayHasKey(0, $element);
        $this->assertInstanceOf('Varien_Simplexml_Element', $element[0]);
        return $model;
    }

    /**
     * @return Mage_Widget_Model_Widget_Instance
     */
    public function testGetWidgetSupportedContainers()
    {
        $model = new Mage_Widget_Model_Widget_Instance;
        $model->setType('Mage_Catalog_Block_Product_Widget_New');
        $containers = $model->getWidgetSupportedContainers();
        $this->assertInternalType('array', $containers);
        $this->assertContains('left', $containers);
        $this->assertContains('content', $containers);
        $this->assertContains('right', $containers);
        return $model;
    }

    /**
     * @param Mage_Widget_Model_Widget_Instance $model
     * @depends testGetWidgetSupportedContainers
     */
    public function testGetWidgetSupportedTemplatesByContainer($model)
    {
        $templates = $model->getWidgetSupportedTemplatesByContainer('content');
        $this->assertNotEmpty($templates);
        $this->assertInternalType('array', $templates);
        foreach ($templates as $row) {
            $this->assertArrayHasKey('value', $row);
            $this->assertArrayHasKey('label', $row);
        }
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
}
