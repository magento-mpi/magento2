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
namespace Magento\Rule\Model\Condition;

class AbstractTest extends \PHPUnit_Framework_TestCase
{
    public function testGetValueElement()
    {
        $layoutMock = $this->getMock('Magento\View\Layout', array(), array(), '', false);

        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $context = $objectManager->create('Magento\Rule\Model\Condition\Context', array('layout' => $layoutMock));

        /** @var \Magento\Rule\Model\Condition\AbstractCondition $model */
        $model = $this->getMockForAbstractClass(
            'Magento\Rule\Model\Condition\AbstractCondition',
            array($context),
            '',
            true,
            true,
            true,
            array('getValueElementRenderer')
        );
        $editableBlock = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Rule\Block\Editable'
        );
        $model->expects($this->any())->method('getValueElementRenderer')->will($this->returnValue($editableBlock));

        $rule = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Rule\Model\Rule');
        $model->setRule(
            $rule->setForm(
                \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Framework\Data\Form')
            )
        );

        $property = new \ReflectionProperty('Magento\Rule\Model\Condition\AbstractCondition', '_inputType');
        $property->setAccessible(true);
        $property->setValue($model, 'date');

        $element = $model->getValueElement();
        $this->assertNotNull($element);
        $this->assertNotEmpty($element->getDateFormat());
    }
}
