<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Queue_Model_Queue_HandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_queueMock;

    /**
     * @var Enterprise_Queue_Model_Event_Handler
     */
    protected $_model;

    protected function setUp()
    {
        $this->_queueMock = $this->getMock('Enterprise_Queue_Model_QueueInterface');
        $this->_model = new Enterprise_Queue_Model_Event_Handler($this->_queueMock);
    }

    public function testAddTaskTest()
    {
        $this->_queueMock->expects($this->once())->method('addTask')->with('some_event', array('123'), 7)
            ->will($this->returnSelf());

        $this->assertEquals($this->_model, $this->_model->addTask('some_event', array('123'), 7));
    }
}
