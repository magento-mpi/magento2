<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Enterprise_Queue_Model_TaskRepository
{
    /**
     * @var Enterprise_Queue_Model_Resource_Task_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var Enterprise_Queue_Model_TaskFactory
     */
    protected $_taskFactory;

    /**
     * @param Enterprise_Queue_Model_Resource_Task_CollectionFactory $collectionFactory
     * @param Enterprise_Queue_Model_TaskFactory $taskFactory
     */
    public function __construct(
        Enterprise_Queue_Model_Resource_Task_CollectionFactory $collectionFactory,
        Enterprise_Queue_Model_TaskFactory $taskFactory
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_taskFactory = $taskFactory;
    }

    /**
     * Find task by task id
     *
     * @param string $taskId
     * @return Enterprise_Queue_Model_Task
     */
    public function find($taskId)
    {
        $task = $this->_taskFactory->create();
        $task->load($taskId);
        return $task;
    }

    /**
     * Find pending tasks by name
     *
     * @param string $taskName
     * @return Enterprise_Queue_Model_Resource_Task_Collection
     */
    public function findPendingByName($taskName)
    {
        $collection = $this->_collectionFactory->create();
        $collection->addFieldToFilter('task_name', array('eq' => $taskName))
            ->addFieldToFilter('status', array('eq' => Enterprise_Queue_Model_Task::STATUS_PENDING));
        return $collection;
    }

    /**
     * Find running tasks by name
     *
     * @param string $taskName
     * @return Enterprise_Queue_Model_Resource_Task_Collection
     */
    public function findRunningByName($taskName)
    {
        $collection = $this->_collectionFactory->create();
        $collection->addFieldToFilter('task_name', array('eq' => $taskName))
            ->addFieldToFilter('status', array('in' => array(
                Enterprise_Queue_Model_Task::STATUS_PENDING,
                Enterprise_Queue_Model_Task::STATUS_IN_PROGRESS,
            )));
        return $collection;
    }

    /**
     * Create new task
     *
     * @param string $taskId
     * @param string $handle
     * @return Enterprise_Queue_Model_Task
     */
    public function create($taskId, $handle)
    {
        $task = $this->_taskFactory->create();
        $task->setUniqueKey($taskId)
            ->setHandle($handle)
            ->save();
        return $task;
    }
}
