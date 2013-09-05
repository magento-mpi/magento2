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
 * Test class for Magento_Core_Model_Layout_Argument_Handler_Object
 */
class Magento_Core_Model_Layout_Argument_Handler_ObjectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Layout_Argument_Handler_Object
     */
    protected $_model;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManagerMock;

    protected function setUp()
    {
        include_once(__DIR__ . '/_files/TestObject.php');

        $helperObjectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager');
        $this->_model = $helperObjectManager->getObject(
            'Magento_Core_Model_Layout_Argument_Handler_Object',
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
        $this->assertEquals($this->_model->parse($argument), $expectedResult);
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
        $simpleObject = $layout->xpath('//argument[@name="testSimpleObject"]');
        $complexObject = $layout->xpath('//argument[@name="testComplexObject"]');
        return array(
            array(
                reset($simpleObject), array(
                    'value' => array(
                        'object' => 'Magento_Core_Model_Layout_Argument_Handler_Files_TestObject',
                    ),
                    'type' => 'object',
                )
            ),
            array(
                reset($complexObject), array(
                    'value' => array(
                        'object' => 'Magento_Core_Model_Layout_Argument_Handler_Files_TestObject',
                    ),
                    'type' => 'object',
                    'updaters' => array('Magento_Test_Updater')
                )
            ),
        );
    }

    /**
     * @dataProvider processDataProvider
     * @param array $argument
     */
    public function testProcess($argument)
    {
        $objectMock = $this->getMock(
            'Magento_Core_Model_Layout_Argument_Handler_Files_TestObject', array(), array(), '', false, false
        );
        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Magento_Core_Model_Layout_Argument_Handler_Files_TestObject')
            ->will($this->returnValue($objectMock));

        $this->assertSame($this->_model->process($argument), $objectMock);
    }

    /**
     * @return array
     */
    public function processDataProvider()
    {
        return array(
            array(
                array(
                    'value' => array(
                        'object' => 'Magento_Core_Model_Layout_Argument_Handler_Files_TestObject',
                    ),
                    'type' => 'object',
                )
            ),
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
            array(array('value' => array()), 'Passed value has incorrect format'),
            array(array('value' => array('object' => 'Test_Model')), 'Incorrect data source model'),
        );
    }
}
