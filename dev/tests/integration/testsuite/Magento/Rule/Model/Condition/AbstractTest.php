<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rule
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Rule_Model_Condition_Abstract
 */
class Magento_Rule_Model_Condition_AbstractTest extends PHPUnit_Framework_TestCase
{
    public function testGetValueElement()
    {
        /** @var Magento_Rule_Model_Condition_Abstract $model */
        $model = $this->getMockForAbstractClass('Magento_Rule_Model_Condition_Abstract', array(), '',
            false, true, true, array('getValueElementRenderer'));
        $editableBlock = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Rule_Block_Editable');
        $model->expects($this->any())
             ->method('getValueElementRenderer')
             ->will($this->returnValue($editableBlock));

        $rule = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create('Magento_Rule_Model_Rule');
        $model->setRule($rule->setForm(Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento\Data\Form')));

        $property = new ReflectionProperty('Magento_Rule_Model_Condition_Abstract', '_inputType');
        $property->setAccessible(true);
        $property->setValue($model, 'date');

        $element = $model->getValueElement();
        $this->assertNotNull($element);
        $this->assertNotEmpty($element->getDateFormat());
    }
}
