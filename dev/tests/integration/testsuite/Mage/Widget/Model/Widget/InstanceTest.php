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

class Mage_Widget_Model_Widget_InstanceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Widget_Model_Widget_Instance
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Mage_Widget_Model_Widget_Instance');
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
     * @return Mage_Widget_Model_Widget_Instance
     */
    public function testGetWidgetConfigAsArray()
    {
        $config = $this->_model->setType('Mage_Catalog_Block_Product_Widget_New')->getWidgetConfigAsArray();
        $this->assertTrue(is_array($config));
        $element = null;
        if (isset($config['parameters']) && isset($config['parameters']['template'])
            && isset($config['parameters']['template']['values'])
            && isset($config['parameters']['template']['values']['list'])
        ) {
            $element = $config['parameters']['template']['values']['list'];
        }
        $expected = array(
            '@' => array('translate' => 'label'),
            'value' => 'product/widget/new/content/new_list.phtml',
            'label' => 'New Products List Template'
        );
        $this->assertNotNull($element);
        $this->assertEquals($expected, $element);

        return $this->_model;
    }

    /**
     * @return Mage_Widget_Model_Widget_Instance
     */
    public function testGetWidgetSupportedContainers()
    {
        $this->_model->setType('Mage_Catalog_Block_Product_Widget_New');
        $containers = $this->_model->getWidgetSupportedContainers();
        $this->assertInternalType('array', $containers);
        $this->assertContains('left', $containers);
        $this->assertContains('content', $containers);
        $this->assertContains('right', $containers);
        return $this->_model;
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
     * @depends testGetWidgetConfigAsArray
     */
    public function testGenerateLayoutUpdateXml(Mage_Widget_Model_Widget_Instance $model)
    {
        $this->assertEquals('', $model->generateLayoutUpdateXml('content'));
        $model->setId('test_id')->setPackageTheme('magento_demo');
        $result = $model->generateLayoutUpdateXml('content');
        $this->assertContains('<reference name="content">', $result);
        $this->assertContains('<block type="' . $model->getType() . '"', $result);
    }
}
