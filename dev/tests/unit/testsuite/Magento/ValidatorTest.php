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
     * Cleanup validator instance to unset default translator if any
     */
    protected function tearDown()
    {
        unset($this->_validator);
    }

    /**
     * Test isValid method
     *
     * @dataProvider isValidDataProvider
     *
     * @param mixed $value
     * @param Magento_Validator_ValidatorInterface[] $validators
     * @param boolean $expectedResult
     * @param array $expectedMessages
     * @param boolean $breakChainOnFailure
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
        $validatorA = $this->getMock('Magento_Validator_ValidatorInterface');
        $validatorA->expects($this->once())->method('isValid')
            ->with($value)->will($this->returnValue(false));
        $validatorA->expects($this->once())->method('getMessages')
            ->will($this->returnValue(array('foo' => array('Foo message 1'), 'bar' => array('Foo message 2'))));

        $validatorB = $this->getMock('Magento_Validator_ValidatorInterface');
        $validatorB->expects($this->once())->method('isValid')
            ->with($value)->will($this->returnValue(false));
        $validatorB->expects($this->once())->method('getMessages')
            ->will($this->returnValue(array('foo' => array('Bar message 1'), 'bar' => array('Bar message 2'))));

        $result[] = array($value, array($validatorA, $validatorB), false, array(
            'foo' => array('Foo message 1', 'Bar message 1'),
            'bar' => array('Foo message 2', 'Bar message 2')
        ));

        // Case 2. Validators fails with breaking chain
        $validatorA = $this->getMock('Magento_Validator_ValidatorInterface');
        $validatorA->expects($this->once())->method('isValid')
            ->with($value)
            ->will($this->returnValue(false));
        $validatorA->expects($this->once())->method('getMessages')
            ->will($this->returnValue(array('field' => 'Error message')));

        $validatorB = $this->getMock('Magento_Validator_ValidatorInterface');
        $validatorB->expects($this->never())->method('isValid');

        $result[] = array($value, array($validatorA, $validatorB), false, array('field' => 'Error message'), true);

        // Case 3. Validators succeed
        $validatorA = $this->getMock('Magento_Validator_ValidatorInterface');
        $validatorA->expects($this->once())->method('isValid')
            ->with($value)->will($this->returnValue(true));
        $validatorA->expects($this->never())->method('getMessages');

        $validatorB = $this->getMock('Magento_Validator_ValidatorInterface');
        $validatorB->expects($this->once())->method('isValid')
            ->with($value)->will($this->returnValue(true));
        $validatorB->expects($this->never())->method('getMessages');

        $result[] = array($value, array($validatorA, $validatorB), true);

        return $result;
    }

    /**
     * Test addValidator
     */
    public function testAddValidator()
    {
        $fooValidator = new Magento_Validator_Test_True();
        $classConstraint = new Magento_Validator_Constraint($fooValidator, 'id');
        $propertyValidator = new Magento_Validator_Constraint_Property($classConstraint, 'name', 'id');

        /** @var Magento_Translate_AdapterAbstract $translator */
        $translator= $this->getMockBuilder('Magento_Translate_AdapterAbstract')
            ->getMockForAbstractClass();
        Magento_Validator_ValidatorAbstract::setDefaultTranslator($translator);

        $this->_validator->addValidator($classConstraint);
        $this->_validator->addValidator($propertyValidator);
        $expected = array(
            array(
                'instance' => $classConstraint,
                'breakChainOnFailure' => false
            ),
            array(
                'instance' => $propertyValidator,
                'breakChainOnFailure' => false
            )
        );
        $this->assertAttributeEquals($expected, '_validators', $this->_validator);
        $this->assertEquals($translator, $fooValidator->getTranslator(), 'Translator was not set');
    }

    /**
     * Check that translator passed into validator in chain
     */
    public function testSetTranslator()
    {
        $fooValidator = new Magento_Validator_Test_True();
        $this->_validator->addValidator($fooValidator);
        /** @var Magento_Translate_AdapterAbstract $translator */
        $translator= $this->getMockBuilder('Magento_Translate_AdapterAbstract')
            ->getMockForAbstractClass();
        $this->_validator->setTranslator($translator);
        $this->assertEquals($translator, $fooValidator->getTranslator());
        $this->assertEquals($translator, $this->_validator->getTranslator());
    }
}
