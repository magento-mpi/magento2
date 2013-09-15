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
 * Test class for \Magento\Core\Model\Layout\Argument\Handler\Options
 */
class Magento_Core_Model_Layout_Argument_Handler_OptionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Layout\Argument\Handler\Options
     */
    protected $_model;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManagerMock;

    protected function setUp()
    {
        include_once(__DIR__ . DIRECTORY_SEPARATOR . 'TestOptions.php');

        $helperObjectManager = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_objectManagerMock = $this->getMock('Magento\ObjectManager');
        $this->_model = $helperObjectManager->getObject(
            'Magento_Core_Model_Layout_Argument_Handler_Options',
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
        $optionsArguments = $layout->xpath('//argument[@name="testOptions"]');
        return array(
            array(
                reset($optionsArguments),
                array(
                    'type' => 'options',
                    'value' => array(
                        'model' => 'Magento_Core_Model_Layout_Argument_Handler_TestOptions',
                    )
                )
            ),
        );
    }

    /**
     * @dataProvider processDataProvider
     * @param array $argument
     * @param boolean $expectedResult
     */
    public function testProcess($argument, $expectedResult)
    {
        $optionsMock = $this->getMock(
            'Magento_Core_Model_Layout_Argument_Handler_TestOptions', array(), array(), '', false, false
        );
        $optionsMock->expects($this->once())
            ->method('toOptionArray')
            ->will($this->returnValue(array('value' => 'label')));

        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Magento_Core_Model_Layout_Argument_Handler_TestOptions')
            ->will($this->returnValue($optionsMock));

        $this->assertEquals($this->_model->process($argument), $expectedResult);
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
                        'model' => 'Magento_Core_Model_Layout_Argument_Handler_TestOptions',
                    )
                ),
                array(
                    array(
                        'value' => 'value',
                        'label' => 'label',
                    )
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
            array(array(), 'Value is required for argument'),
            array(array('value' => array()), 'Passed value has incorrect format'),
            array(array('value' => array('model' => 'Magento_Dummy_Model')), 'Incorrect options model'),
        );
    }
}
