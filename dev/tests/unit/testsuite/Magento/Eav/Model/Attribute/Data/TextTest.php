<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Eav\Model\Attribute\Data;

class TextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Eav\Model\Attribute\Data\Text
     */
    protected $_model;

    protected function setUp()
    {
        $locale = $this->getMock('Magento\Core\Model\LocaleInterface', array(), array(), '', false, false);
        $logger = $this->getMock('Magento\Core\Model\Logger', array(), array(), '', false, false);
        $helper = $this->getMock('Magento\Core\Helper\String', array(), array(), '', false, false);

        $attributeData = array(
            'store_label' => 'Test',
            'attribute_code' => 'test',
            'is_required' => 1,
            'validate_rules' => array(
                'min_text_length' => 0,
                'max_text_length' => 0,
                'input_validation' => 0
            )
        );

        $attributeClass = 'Magento\Eav\Model\Entity\Attribute\AbstractAttribute';
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $eavTypeFactory = $this->getMock('Magento\Eav\Model\Entity\TypeFactory', array(), array(), '', false, false);
        $arguments = $objectManagerHelper->getConstructArguments(
            $attributeClass,
            array('eavTypeFactory' => $eavTypeFactory, 'data' => $attributeData)
        );

        /** @var $attribute \Magento\Eav\Model\Entity\Attribute\AbstractAttribute|\PHPUnit_Framework_MockObject_MockObject */
        $attribute = $this->getMock($attributeClass, array('_init'), $arguments);
        $this->_model = new \Magento\Eav\Model\Attribute\Data\Text($locale, $logger, $helper);
        $this->_model->setAttribute($attribute);
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    public function testValidateValueString()
    {
        $inputValue = '0';
        $expectedResult = true;
        $this->assertEquals($expectedResult, $this->_model->validateValue($inputValue));
    }

    public function testValidateValueInteger()
    {
        $inputValue = 0;
        $expectedResult = array('"Test" is a required value.');
        $result = $this->_model->validateValue($inputValue);
        $this->assertEquals($expectedResult, array((string)$result[0]));
    }
}
