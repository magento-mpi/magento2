<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_ShellAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_ShellAbstract|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = $this->getMockBuilder('Magento_Core_Model_ShellAbstract')
            ->disableOriginalConstructor()
            ->setMethods(array('_applyPhpVariables'))
            ->getMockForAbstractClass();
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * @param array $arguments
     * @param string $argName
     * @param string $expectedValue
     *
     * @dataProvider setGetArgDataProvider
     */
    public function testSetGetArg($arguments, $argName, $expectedValue)
    {
        $this->_model->setRawArgs($arguments);
        $this->assertEquals($this->_model->getArg($argName), $expectedValue);
    }

    /**
     * @return array
     */
    public function setGetArgDataProvider()
    {
        return array(
            'argument with no value' => array(
                'arguments' => array(
                    'argument', 'argument2'
                ),
                'argName' => 'argument',
                'expectedValue' => true
            ),
            'dashed argument with value' => array(
                'arguments' => array(
                    '-argument',
                    'value'
                ),
                'argName' => 'argument',
                'expectedValue' => 'value'
            ),
            'double-dashed argument with separate value' => array(
                'arguments' => array(
                    '--argument-name',
                    'value'
                ),
                'argName' => 'argument-name',
                'expectedValue' => 'value'
            ),
            'double-dashed argument with included value' => array(
                'arguments' => array(
                    '--argument-name=value'
                ),
                'argName' => 'argument-name',
                'expectedValue' => 'value'
            ),
            'argument with value, then single argument with no value' => array(
                'arguments' => array(
                    '-argument',
                    'value',
                    'argument2'
                ),
                'argName' => 'argument',
                'expectedValue' => 'value'
            ),
        );
    }
}
