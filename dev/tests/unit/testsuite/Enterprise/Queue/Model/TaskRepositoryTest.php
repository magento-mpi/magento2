<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Enterprise_Queue_Model_TaskRepositoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_Queue_Model_TaskRepository
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_taskFactoryMock;

    protected function setUp()
    {
        $this->_taskFactoryMock = $this->getMock(
            'Enterprise_Queue_Model_TaskFactory', array('create'), array(), '', false
        );
        $this->_model = new Enterprise_Queue_Model_TaskRepository($this->_taskFactoryMock);
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_taskFactoryMock);
    }

    public function testGetCreatesNewTask()
    {
        $taskName = 'taskName';
        $params = array('param' => 'val');
        $taskMock = $this->getMock('Enterprise_Queue_Model_Task', array(), array(), '', false);
        $taskMock->expects($this->once())->method('setId');
        $this->_taskFactoryMock->expects($this->once())->method('create')->will($this->returnValue($taskMock));
        $this->assertEquals($taskMock, $this->_model->get($taskName, $params));
    }
}
