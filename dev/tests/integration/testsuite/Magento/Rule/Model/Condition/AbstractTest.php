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
 * Test class for \Magento\Rule\Model\Condition\AbstractCondition
 */
class Magento_Rule_Model_Condition_AbstractTest extends PHPUnit_Framework_TestCase
{
    public function testGetValueElement()
    {
        /** @var \Magento\Rule\Model\Condition\AbstractCondition $model */
        $model = $this->getMockForAbstractClass('Magento\Rule\Model\Condition\AbstractCondition', array(), '',
            false, true, true, array('getValueElementRenderer'));
        $editableBlock = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento\Rule\Block\Editable');
        $model->expects($this->any())
             ->method('getValueElementRenderer')
             ->will($this->returnValue($editableBlock));

        $rule = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create('Magento\Rule\Model\Rule');
        $model->setRule($rule->setForm(Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento\Data\Form')));

        $property = new ReflectionProperty('\Magento\Rule\Model\Condition\AbstractCondition', '_inputType');
        $property->setAccessible(true);
        $property->setValue($model, 'date');

        $element = $model->getValueElement();
        $this->assertNotNull($element);
        $this->assertNotEmpty($element->getDateFormat());
    }
}
