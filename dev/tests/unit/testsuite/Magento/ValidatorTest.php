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
 */
class Magento_ValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Validator
     */
    protected $_validator;

    /**
     * Set up
     */
    protected function setUp()
    {
        $this->_validator = new Magento_Validator();
    }

    /**
     * Test isValid method
     *
     * @dataProvider isValidDataProvider
     *
     * @param mixed $value
     * @param Magento_Validator_Interface[] $validators
     * @param bool $expectedResult
     * @param array $expectedMessages
     * @param bool $breakChainOnFailure
     */
    public function testIsValid($value, $validators, $expectedResult, $expectedMessages = array(),
                                $breakChainOnFailure = false
    ) {
        foreach ($validators as $validator) {
            $this->_validator->addValidator($validator, $breakChainOnFailure);
        }

        $this->assertEquals($expectedResult, $this->_validator->isValid($value));
        $this->assertEquals($expectedMessages, $this->_validator->getMessages($value));
    }

    /**
     * Data provider for testIsValid
     *
     * @return array
     */
    public function isValidDataProvider()
    {
        $result = array();
        $value = 'test';

        // Case 1. Validators fails without breaking chain
        $validators1 = $this->getMock('Magento_Validator_Interface');
        $validators1->expects($this->once())->method('isValid')
            ->with($value)->will($this->returnValue(false));
        $validators1->expects($this->once())->method('getMessages')
            ->will($this->returnValue(array('foo' => array('Foo message 1'), 'bar' => array('Foo message 2'))));

        $validators2 = $this->getMock('Magento_Validator_Interface');
        $validators2->expects($this->once())->method('isValid')
            ->with($value)->will($this->returnValue(false));
        $validators2->expects($this->once())->method('getMessages')
            ->will($this->returnValue(array('foo' => array('Bar message 1'), 'bar' => array('Bar message 2'))));

        $result[] = array($value, array($validators1, $validators2), false, array(
            'foo' => array('Foo message 1', 'Bar message 1'),
            'bar' => array('Foo message 2', 'Bar message 2')
        ));

        // Case 2. Validators fails with breaking chain
        $validators1 = $this->getMock('Magento_Validator_Interface');
        $validators1->expects($this->once())->method('isValid')
            ->with($value)
            ->will($this->returnValue(false));
        $validators1->expects($this->once())->method('getMessages')
            ->will($this->returnValue(array('field' => 'Error message')));

        $validators2 = $this->getMock('Magento_Validator_Interface');
        $validators2->expects($this->never())->method('isValid');

        $result[] = array($value, array($validators1, $validators2), false, array('field' => 'Error message'), true);

        // Case 3. Validators succeed
        $validators1 = $this->getMock('Magento_Validator_Interface');
        $validators1->expects($this->once())->method('isValid')
            ->with($value)->will($this->returnValue(true));
        $validators1->expects($this->never())->method('getMessages');

        $validators2 = $this->getMock('Magento_Validator_Interface');
        $validators2->expects($this->once())->method('isValid')
            ->with($value)->will($this->returnValue(true));
        $validators2->expects($this->never())->method('getMessages');

        $result[] = array($value, array($validators1, $validators2), true);

        return $result;
    }

    public function test()
    {
        $fooValidator = $this->getMock('Magento_Validator_Interface');
        $classConstraint = new Magento_Validator_Constraint($fooValidator, 'id');
        $propertyValidator = new Magento_Validator_Constraint_Property($classConstraint, 'name', 'id');
        $this->_validator->addValidator($classConstraint);
        $this->_validator->addValidator($propertyValidator);
    }
}
