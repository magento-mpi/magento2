<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     \Magento\Validator
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test case for \Magento\Validator\Constraint
 */
class Magento_Validator_ConstraintTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Validator\Constraint
     */
    protected $_constraint;

    /**
     * @var \Magento\Validator\ValidatorInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_validatorMock;

    /**
     * Set up
     */
    protected function setUp()
    {
        $this->_validatorMock = $this->getMockBuilder('Magento\Validator\ValidatorAbstract')
            ->setMethods(array('isValid', 'getMessages'))
            ->getMock();
        $this->_constraint = new \Magento\Validator\Constraint($this->_validatorMock);
    }

    /**
     * Test getAlias method
     */
    public function testGetAlias()
    {
        $this->assertEmpty($this->_constraint->getAlias());
        $alias = 'foo';
        $constraint = new \Magento\Validator\Constraint($this->_validatorMock, $alias);
        $this->assertEquals($alias, $constraint->getAlias());
    }

    /**
     * Test isValid method
     *
     * @dataProvider isValidDataProvider
     *
     * @param mixed $value
     * @param bool $expectedResult
     * @param array $expectedMessages
     */
    public function testIsValid($value, $expectedResult, $expectedMessages = array())
    {
        $this->_validatorMock
            ->expects($this->once())
            ->method('isValid')
            ->with($value)
            ->will($this->returnValue($expectedResult));

        if ($expectedResult) {
            $this->_validatorMock
                ->expects($this->never())
                ->method('getMessages');
        } else {
            $this->_validatorMock
                ->expects($this->once())
                ->method('getMessages')
                ->will($this->returnValue($expectedMessages));
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
            array('test', true),
            array('test', false, array('foo'))
        );
    }

    /**
     * Check translator was set into wrapped validator
     */
    public function testSetTranslator()
    {
        /** @var \Magento\Translate\AdapterAbstract $translator */
        $translator = $this->getMockBuilder('Magento\Translate\AdapterInterface')
            ->getMockForAbstractClass();
        $this->_constraint->setTranslator($translator);
        $this->assertEquals($translator, $this->_validatorMock->getTranslator());
        $this->assertEquals($translator, $this->_constraint->getTranslator());
    }
}
