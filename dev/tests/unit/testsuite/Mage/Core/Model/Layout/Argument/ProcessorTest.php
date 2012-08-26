<?php

class Mage_Core_Model_Layout_Argument_ProcessorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Layout_Argument_Processor
     */
    protected $_model;

    protected $_objectFactoryMock;

    protected $_processorConfig;

    protected $_dummyArgumentTypeMock;

    public function setUp()
    {
        $this->_objectFactoryMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_processorConfig = $this->getMock(
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
            'processorConfig' => $this->_processorConfig
        ));
    }

    /**
     * @param $arguments
     * @dataProvider argumentsDataProvider
     */
    public function testProcess($arguments)
    {
        $this->_processorConfig->expects($this->once())->method('getArgumentHandlerByType')
            ->with($this->equalTo('dummy'))
            ->will($this->returnValue('Mage_Core_Model_Layout_Argument_Processor_DummyType'));

        $this->_objectFactoryMock->expects($this->once())->method('getModelInstance')
            ->with(
            $this->equalTo('Mage_Core_Model_Layout_Argument_Processor_DummyType'),
            $this->equalTo(array('objectFactory' => $this->_objectFactoryMock))
        )->will($this->returnValue($this->_dummyArgumentTypeMock));

        $this->_objectFactoryMock->expects($this->any())->method('getModelInstance')
            ->with($this->equalTo('Dummy_Argument_Value_Class_Name'))
            ->will($this->returnValue($this->getMock('Dummy_Argument_Value_Class_Name', array(), array(), '', false)));

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

        $this->_processorConfig->expects($this->once())->method('getArgumentHandlerByType')
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

    public function argumentsDataProvider()
    {
        return array(array(array(
            'argKeyOne' => array('value' => 'argValue'),
            'argKeyTwo' => array('type' => 'dummy', 'value' => 'Dummy_Argument_Value_Class_Name'),
            'argKeyCorrupted' => array('no_value' => false)
        )));
    }
}
