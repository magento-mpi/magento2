<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Core_Model_Layout_Argument_Processor
 */
class Mage_Core_Model_Layout_Argument_ProcessorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Layout_Argument_Processor
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_processorConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dummyArgumentTypeMock;

    protected function setUp()
    {
        $this->_objectFactoryMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_processorConfigMock = $this->getMock(
            'Mage_Core_Model_Layout_Argument_ProcessorConfig',
            array(),
            array(),
            '',
            false);
        $this->_dummyArgumentTypeMock = $this->getMock(
            'Mage_Core_Model_Layout_Argument_Processor_TypeInterface',
            array(),
            array(),
            '',
            false);

        $this->_model = new Mage_Core_Model_Layout_Argument_Processor(array(
            'objectFactory' => $this->_objectFactoryMock,
            'processorConfig' => $this->_processorConfigMock
        ));
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_dummyArgumentTypeMock);
        unset($this->_objectFactoryMock);
        unset($this->_processorConfigMock);
    }

    /**
     * @param $arguments
     * @dataProvider argumentsDataProvider
     */
    public function testProcess($arguments)
    {
        $this->_processorConfigMock->expects($this->once())->method('getArgumentHandlerByType')
            ->with($this->equalTo('dummy'))
            ->will($this->returnValue('Mage_Core_Model_Layout_Argument_Processor_DummyType'));

        $map = array(
            array(
                'Mage_Core_Model_Layout_Argument_Processor_DummyType',
                array('objectFactory' => $this->_objectFactoryMock),
                $this->_dummyArgumentTypeMock
            ),
            array(
                'Dummy_Argument_Value_Class_Name',
                array('objectFactory' => $this->_objectFactoryMock),
                $this->getMock('Dummy_Argument_Value_Class_Name', array(), array(), '', false)
            )
        );
        $this->_objectFactoryMock->expects($this->any())
            ->method('getModelInstance')
            ->will($this->returnValueMap($map));

        $processedArguments = $this->_model->process($arguments);

        $this->assertArrayHasKey('argKeyOne', $processedArguments);
        $this->assertArrayHasKey('argKeyTwo', $processedArguments);
        $this->assertArrayHasKey('argKeyCorrupted', $processedArguments);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testProcessWhenArgumentTypeIsIncorrect()
    {
        $incorrectArgumentType = $this->getMock('Incorrect_Handler', array(), array(), '', false);

        $this->_processorConfigMock->expects($this->once())->method('getArgumentHandlerByType')
            ->with($this->equalTo('incorrect'))->will($this->returnValue('Incorrect_Handler'));

        $this->_objectFactoryMock->expects($this->any())->method('getModelInstance')
            ->with(
            $this->equalTo('Incorrect_Handler'),
            $this->equalTo(array('objectFactory' => $this->_objectFactoryMock))
        )->will($this->returnValue($incorrectArgumentType));

        $this->_model->process(array(
            'argKey' => array('type' => 'incorrect', 'value' => 'Incorrect_Argument_Value_Class_Name')
        ));
    }

    /**
     * @expectedException InvalidArgumentException
     * @dataProvider processWhitEmptyArgumentValueAndSpecifiedTypeDataProvider
     */
    public function testProcessWhitEmptyArgumentValueAndSpecifiedType($arguments)
    {
        $this->_model->process($arguments);
    }

    public function processWhitEmptyArgumentValueAndSpecifiedTypeDataProvider()
    {
        return array(
            array('argKey' => array('type' => 'Dummy_Type')),
            array('argKey' => array('value' => 0, 'type' => 'Dummy_Type')),
            array('argKey' => array('value' => '', 'type' => 'Dummy_Type')),
            array('argKey' => array('value' => null, 'type' => 'Dummy_Type')),
            array('argKey' => array('value' => false, 'type' => 'Dummy_Type')),
        );
    }

    public function argumentsDataProvider()
    {
        return array(
            array(
                array(
                    'argKeyOne' => array('value' => 'argValue'),
                    'argKeyTwo' => array('type' => 'dummy', 'value' => 'Dummy_Argument_Value_Class_Name'),
                    'argKeyCorrupted' => array('no_value' => false)
                )
            )
        );
    }

    public function testProcessWithArgumentUpdaters()
    {
        $arguments = array(
            'one' => array(
                'value' => 1,
                'updater' => array('Dummy_Updater_1', 'Dummy_Updater_2')
            )
        );

        $argumentUpdaterMock = $this->getMock('Mage_Core_Model_Layout_Argument_Updater', array(), array(), '', false);
        $argumentUpdaterMock->expects($this->once())->method('applyUpdaters')->will($this->returnValue(1));

        $this->_objectFactoryMock
            ->expects($this->once())
            ->method('getModelInstance')
            ->with('Mage_Core_Model_Layout_Argument_Updater')
            ->will($this->returnValue($argumentUpdaterMock));

        $expected = array(
            'one' => 1,
        );
        $this->assertEquals($expected, $this->_model->process($arguments));
    }
}
