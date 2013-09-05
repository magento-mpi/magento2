<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Backend_Block_Widget_Form
 * @magentoAppArea adminhtml
 */
class Magento_Backend_Block_Widget_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testSetFieldset()
    {
        $objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
        Mage::getDesign()->setArea(Magento_Core_Model_App_Area::AREA_ADMINHTML)->setDefaultDesignTheme();
        $layout = $objectManager->create('Magento_Core_Model_Layout');
        $formBlock = $layout->addBlock('Magento_Backend_Block_Widget_Form');
        $fieldSet = $objectManager->create('Magento_Data_Form_Element_Fieldset');
        $arguments = array(
            'data' => array(
                'attribute_code' => 'date',
                'backend_type'   => 'datetime',
                'frontend_input' => 'date',
                'frontend_label' => 'Date',
            )
        );
        $attributes = array($objectManager->create('Magento_Eav_Model_Entity_Attribute', $arguments));
        $method = new ReflectionMethod('Magento_Backend_Block_Widget_Form', '_setFieldset');
        $method->setAccessible(true);
        $method->invoke($formBlock, $attributes, $fieldSet);
        $fields = $fieldSet->getElements();

        $this->assertEquals(1, count($fields));
        $this->assertInstanceOf('Magento_Data_Form_Element_Date', $fields[0]);
        $this->assertNotEmpty($fields[0]->getDateFormat());
    }
}
