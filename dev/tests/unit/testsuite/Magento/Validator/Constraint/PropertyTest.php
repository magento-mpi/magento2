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
 * Test case for Magento_Validator_Constraint_Property
 */
class Magento_Validator_Constraint_PropertyTest extends PHPUnit_Framework_TestCase
{
    const PROPERTY_NAME = 'test';

    /**
     * @var Magento_Validator_Constraint_Property
     */
    protected $_constraint;

    /**
     * @var Magento_Validator_ValidatorInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_validatorMock;

    /**
     * Set up
     */
    protected function setUp()
    {
        $this->_validatorMock = $this->getMock('Magento_Validator_ValidatorInterface');
        $this->_constraint = new Magento_Validator_Constraint_Property($this->_validatorMock, self::PROPERTY_NAME);
    }

    /**
     * Test getAlias method
     */
    public function testGetAlias()
    {
        $this->assertEmpty($this->_constraint->getAlias());
        $alias = 'foo';
        $constraint = new Magento_Validator_Constraint_Property($this->_validatorMock, self::PROPERTY_NAME, $alias);
        $this->assertEquals($alias, $constraint->getAlias());
    }

    /**
     * Test isValid method
     *
     * @dataProvider isValidDataProvider
     *
     * @param mixed $value
     * @param mixed $validateValue
     * @param bool $expectedResult
     * @param array $validatorMessages
     * @param array $expectedMessages
     */
    public function testIsValid($value, $validateValue, $expectedResult, $validatorMessages = array(),
        $expectedMessages = array()
    ) {
        $this->_validatorMock
            ->expects($this->once())->method('isValid')
            ->with($validateValue)->will($this->returnValue($expectedResult));

        if ($expectedResult) {
            $this->_validatorMock->expects($this->never())->method('getMessages');
        } else {
            $this->_validatorMock
                ->expects($this->once())->method('getMessages')
                ->will($this->returnValue($validatorMessages));
        }

        $this->assertEquals($expectedResult, $this->_constraint->isValid($value));
        $this->assertEquals($expectedMessages, $this->_constraint->getMessages());
    }

    /**
     * Data provider for testIsValid
     *
     * @return array
     */
    public function isValidDataProvider()
    {
        return array(
            array(
                array(self::PROPERTY_NAME => 'Property value', 'foo' => 'Foo value'),
                'Property value',
                true
            ),
            array(
                new Magento_Object(array(self::PROPERTY_NAME => 'Property value')),
                'Property value',
                true
            ),
            array(
                new ArrayObject(array(self::PROPERTY_NAME => 'Property value')),
                'Property value',
                true
            ),
            array(
                array(self::PROPERTY_NAME => 'Property value', 'foo' => 'Foo value'),
                'Property value',
                false,
                array('Error message 1', 'Error message 2'),
                array(self::PROPERTY_NAME => array('Error message 1', 'Error message 2')),
            ),
            array(
                array('foo' => 'Foo value'),
                null,
                false,
                array('Error message 1'),
                array(self::PROPERTY_NAME => array('Error message 1')),
            ),
            array(
                'scalar',
                null,
                false,
                array('Error message 1'),
                array(self::PROPERTY_NAME => array('Error message 1')),
            )
        );
    }
}
