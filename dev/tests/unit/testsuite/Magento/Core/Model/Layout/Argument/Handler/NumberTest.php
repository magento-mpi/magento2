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

/**
 * Test class for Magento_Core_Model_Layout_Argument_Handler_Number
 */
class Magento_Core_Model_Layout_Argument_Handler_NumberTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Layout_Argument_Handler_Boolean
     */
    protected $_model;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManagerMock;

    protected function setUp()
    {
        $helperObjectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager');
        $this->_model = $helperObjectManager->getObject(
            'Magento_Core_Model_Layout_Argument_Handler_Number',
            array('objectManager' => $this->_objectManagerMock)
        );
    }

    /**
     * @dataProvider parseDataProvider()
     * @param Magento_Core_Model_Layout_Element $argument
     * @param array $expectedResult
     */
    public function testParse($argument, $expectedResult)
    {
        $result = $this->_model->parse($argument);
        $this->assertEquals($result, $expectedResult);
    }

    /**
     * @return array
     */
    public function parseDataProvider()
    {
        $layout = simplexml_load_file(
            __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'arguments.xml',
            'Magento_Core_Model_Layout_Element'
        );
        $result = $this->processDataProvider();
        $simpleArg = $layout->xpath('//argument[@name="testSimpleNumber"]');
        $complexArg = $layout->xpath('//argument[@name="testComplexNumber"]');
        return array(
            array($simpleArg[0], $result[0][0] + array('type' => 'number')),
            array($complexArg[0], $result[1][0] + array('type' => 'number')),
        );
    }

    /**
     * @dataProvider processDataProvider
     * @param array $argument
     * @param boolean $expectedResult
     */
    public function testProcess($argument, $expectedResult)
    {
        $this->assertEquals($this->_model->process($argument), $expectedResult);
    }

    /**
     * @return array
     */
    public function processDataProvider()
    {
        return array(
            array(array('value' => '1.5'), '1.5'),
            array(array('value' => '25'), '25'),
        );
    }

    /**
     * @dataProvider processExceptionDataProvider
     * @param array $argument
     * @param string $message
     */
    public function testProcessException($argument, $message)
    {
        $this->setExpectedException(
            'InvalidArgumentException', $message
        );
        $this->_model->process($argument);
    }

    /**
     * @return array
     */
    public function processExceptionDataProvider()
    {
        return array(
            array(array('value' => null), 'Value is required for argument'),
            array(array('value' => 'true'), 'Value is not number argument'),
        );
    }
}
