<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Widget_Model_Widget_InstanceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Widget_Model_Widget_Instance
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Magento_Widget_Model_Widget_Instance');
    }

    public function testSetGetType()
    {
        $this->assertEmpty($this->_model->getType());
        $this->assertSame('test', $this->_model->setType('test')->getType());
        $this->assertSame('test', $this->_model->getInstanceType());
    }

    public function testSetThemeId()
    {
        $theme = Mage::getDesign()->setDefaultDesignTheme()->getDesignTheme();
        $this->_model->setThemeId($theme->getId());

        $this->assertEquals($theme->getId(), $this->_model->getThemeId());
    }

    /**
     * @return Magento_Widget_Model_Widget_Instance
     */
    public function testGetWidgetConfig()
    {
        $config = $this->_model->setType('Magento_Catalog_Block_Product_Widget_New')->getWidgetConfig();
        $this->assertInstanceOf('Magento_Simplexml_Element', $config);
        /** @var Magento_Simplexml_Element $config */
        $element = $config->xpath('/widgets/new_products/parameters/template/values/list');
        $this->assertArrayHasKey(0, $element);
        $this->assertInstanceOf('Magento_Simplexml_Element', $element[0]);
        return $this->_model;
    }

    /**
     * @return Magento_Widget_Model_Widget_Instance
     */
    public function testGetWidgetSupportedContainers()
    {
        $this->_model->setType('Magento_Catalog_Block_Product_Widget_New');
        $containers = $this->_model->getWidgetSupportedContainers();
        $this->assertInternalType('array', $containers);
        $this->assertContains('left', $containers);
        $this->assertContains('content', $containers);
        $this->assertContains('right', $containers);
        return $this->_model;
    }

    /**
     * @param Magento_Widget_Model_Widget_Instance $model
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
     * @param Magento_Widget_Model_Widget_Instance $model
     * @depends testGetWidgetConfig
     */
    public function testGenerateLayoutUpdateXml(Magento_Widget_Model_Widget_Instance $model)
    {
        $this->assertEquals('', $model->generateLayoutUpdateXml('content'));
        $model->setId('test_id')->setPackageTheme('magento_demo');
        $result = $model->generateLayoutUpdateXml('content');
        $this->assertContains('<reference name="content">', $result);
        $this->assertContains('<block type="' . $model->getType() . '"', $result);
    }
}
