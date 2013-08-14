<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

class Enterprise_Queue_Model_QueueTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_Queue_Model_Queue
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_taskRepositoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_clientMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_taskMock;

    /**
     * @var string
     */
    protected $_taskName = 'taskName';

    /**
     * @var string
     */
    protected $_taskNamePrefix = 'taskNamePrefix';

    /**
     * @var array
     */
    protected $_params = array('param1' => 'val1');

    /**
     * @var array
     */
    protected $_mergedParams = array('param1' => 'val1', 'defaultParam' => 'defaultVal');

    protected function setUp()
    {
        $this->_configMock = $this->getMock('Enterprise_Queue_Model_Config', array(), array(), '', false);
        $this->_taskRepositoryMock = $this->getMock(
            'Enterprise_Queue_Model_TaskRepository', array(), array(), '', false
        );
        $this->_taskMock = $this->getMock(
            'Magento_Core_Model_Task',
            array('getHandle', 'setStatus', 'setHandle', 'isEnqueued', 'getId', 'save'),
            array(),
            '',
            false
        );
        $this->_taskRepositoryMock->expects($this->any())
            ->method('get')
            ->with($this->_taskNamePrefix . $this->_taskName, $this->_mergedParams)
            ->will($this->returnValue($this->_taskMock));

        $this->_clientMock = $this->getMock('Magento_JobQueue_ClientInterface');

        $this->_clientMock->expects($this->any())->method('getStatus')->with('task_handle')
            ->will($this->returnValue('running'));

        $this->_configMock->expects($this->any())->method('getTaskParams')
            ->will($this->returnValue(array('defaultParam' => 'defaultVal')));

        $this->_model = new Enterprise_Queue_Model_Queue(
            $this->_configMock, $this->_taskRepositoryMock, $this->_clientMock, $this->_taskNamePrefix
        );
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_clientMock);
        unset($this->_taskRepositoryMock);
        unset($this->_configMock);
    }

    public function testAddTaskWithNewTaskAddsTaskToQueue()
    {
        $this->_taskMock->expects($this->once())->method('isEnqueued')->will($this->returnValue(false));
        $this->_taskMock->expects($this->once())->method('getId')->will($this->returnValue('taskId'));
        $this->_taskMock->expects($this->once())->method('setHandle')->with('newHandle');
        $this->_clientMock->expects($this->once())->method('addBackgroundTask')
            ->with($this->_taskNamePrefix . $this->_taskName, $this->_mergedParams, 'high', 'taskId')
            ->will($this->returnValue('newHandle'));
        $this->_model->addTask($this->_taskName, $this->_params, 'high');
    }

    public function testAddTaskWithRunningTaskDoesNothing()
    {
        $this->_taskMock->expects($this->once())->method('isEnqueued')->will($this->returnValue(true));
        $this->_clientMock->expects($this->never())->method("addBackgroundTask");
        $this->_model->addTask($this->_taskName, $this->_params, 'high');
    }

    /**
     * @expectedException Enterprise_Queue_Model_AddException
     */
    public function testAddTaskRethrowsProperException()
    {
        $this->_taskMock->expects($this->once())->method('isEnqueued')->will(
            $this->throwException(new Enterprise_Queue_Model_AddException())
        );
        $this->_model->addTask($this->_taskName, $this->_params, 'high');
    }

    public function testGetTaskInitializesTaskStatus()
    {
        $this->_taskMock->expects($this->any())->method('getHandle')->will($this->returnValue('task_handle'));
        $this->_taskMock->expects($this->once())->method('setStatus')->with('running');

        $this->assertEquals($this->_taskMock, $this->_model->getTask($this->_taskName, $this->_params));
    }
}
