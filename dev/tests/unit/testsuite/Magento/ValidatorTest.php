<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Validator
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test case for Magento_Validator
 *
 * @group validator
 */
class Magento_ValidatorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructException()
    {
        new Magento_Validator(null, null);
    }

    /**
     * @param array $dataToValidate
     * @param PHPUnit_Framework_MockObject_MockObject $zendConstraintMock
     * @param PHPUnit_Framework_MockObject_MockObject $magentoConstraintMock
     * @param Magento_Validator_Config $configMock
     * @dataProvider dataProviderForValidator
     */
    public function testIsValid($dataToValidate, $zendConstraintMock, $magentoConstraintMock, $configMock)
    {
        $zendConstraintMock->expects($this->once())
            ->method('isValid')
            ->with($dataToValidate['test_field'])
            ->will($this->returnValue(true));

        $magentoConstraintMock->expects($this->once())
            ->method('isValidData')
            ->with($dataToValidate, 'test_field_constraint')
            ->will($this->returnValue(true));

        $validator = new Magento_Validator('test_entity', 'test_group_a', $configMock);
        $this->assertTrue($validator->isValid($dataToValidate));
    }

    /**
     * @param array $dataToValidate
     * @param PHPUnit_Framework_MockObject_MockObject $zendConstraintMock
     * @param PHPUnit_Framework_MockObject_MockObject $magentoConstraintMock
     * @param Magento_Validator_Config $configMock
     * @dataProvider dataProviderForValidator
     */
    public function testGetErrors($dataToValidate, $zendConstraintMock, $magentoConstraintMock, $configMock)
    {
        $expectedZendValidationErrors = array('Test Zend_Validate_Interface constraint error.');
        $zendConstraintMock->expects($this->once())
            ->method('isValid')
            ->with($dataToValidate['test_field'])
            ->will($this->returnValue(false));
        $zendConstraintMock->expects($this->once())
            ->method('getMessages')
            ->will($this->returnValue($expectedZendValidationErrors));

        $expectedMagentoValidationErrors = array(
            'test_field_constraint' => array('Test Magento_Validator_ConstraintInterface constraint error.')
        );
        $magentoConstraintMock->expects($this->once())
            ->method('isValidData')
            ->with($dataToValidate, 'test_field_constraint')
            ->will($this->returnValue(false));
        $magentoConstraintMock->expects($this->once())
            ->method('getErrors')
            ->will($this->returnValue($expectedMagentoValidationErrors));

        $validator = new Magento_Validator('test_entity', 'test_group_a', $configMock);
        $this->assertFalse($validator->isValid($dataToValidate));
        $expectedErrors = array_merge(array('test_field' => $expectedZendValidationErrors),
            $expectedMagentoValidationErrors);
        $actualErrors = $validator->getMessages();
        $this->assertEquals($expectedErrors, $actualErrors);
    }

    public function dataProviderForValidator()
    {
        $dataToValidate = array(
            'test_field' => 'test_value',
            'test_field_constraint' => 'test value constraint',
        );

        $zendConstraintMock = $this->getMock('Zend_Validate_Alnum', array('isValid', 'getMessages'));
        $magentoConstraintMock = $this->getMock('Magento_Validator_Constraint', array('isValidData', 'getErrors'));
        $validationRules = array(
            'test_rule' => array(
                array(
                    'constraint' => $zendConstraintMock,
                    'field' => 'test_field'
                ),
                array(
                    'constraint' => $magentoConstraintMock,
                    'field' => 'test_field_constraint'
                ),
            ),
        );

        $configMock = $this->getMockBuilder('Magento_Validator_Config')->disableOriginalConstructor()->getMock();
        $configMock->expects($this->once())
            ->method('getValidationRules')
            ->with('test_entity', 'test_group_a')
            ->will($this->returnValue($validationRules));

        return array(
            array($dataToValidate, $zendConstraintMock, $magentoConstraintMock, $configMock)
        );
    }
}