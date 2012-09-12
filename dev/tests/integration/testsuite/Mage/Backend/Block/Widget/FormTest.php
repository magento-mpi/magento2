<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Backend_Block_Widget_Form.
 *
 * @group module:Mage_Backend
 */
class Mage_Backend_Block_Widget_FormTest extends PHPUnit_Framework_TestCase
{
    public function testSetFieldset()
    {
        $layout = new Mage_Core_Model_Layout;
        $formBlock = $layout->addBlock('Mage_Backend_Block_Widget_Form');
        $fieldSet = new Varien_Data_Form_Element_Fieldset();
        $attributes = array(
            new Mage_Eav_Model_Entity_Attribute(
                array(
                    'attribute_code' => 'date',
                    'backend_type' => 'datetime',
                    'frontend_input' => 'date',
                    'frontend_label' => 'Date',
                )
            )
        );
        $method = new ReflectionMethod('Mage_Backend_Block_Widget_Form', '_setFieldset');
        $method->setAccessible(true);
        $method->invoke($formBlock, $attributes, $fieldSet);
        $fields = $fieldSet->getElements();

        $this->assertEquals(1, count($fields));
        $this->assertInstanceOf('Varien_Data_Form_Element_Date', $fields[0]);
        $this->assertNotEmpty($fields[0]->getDateFormat());
    }
}
