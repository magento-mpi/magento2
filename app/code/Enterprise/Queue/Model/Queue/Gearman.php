<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Enterprise_Queue_Model_Queue_Gearman implements Enterprise_Queue_Model_QueueInterface
{
    /**
     * @var Enterprise_Queue_Model_Resource_Task_Collection
     */
    protected $_taskCollection;

    /**
     * @param Enterprise_Queue_Model_Resource_Task_Collection $taskCollection
     */
    public function __construct(Enterprise_Queue_Model_Resource_Task_Collection $taskCollection)
    {
        $this->_taskCollection = $taskCollection;
    }

    /**
     * Add task to queue
     *
     * @param string $taskName
     * @param array $params
     * @param string $priority
     */
    public function addTask($taskName, $params, $priority)
    {
        $taskId = md5($taskName . json_encode($params));
        $handle = $this->_client->addTask($taskName, serialize($params), $priority, $taskId);
        $task = $this->_taskFactory->create();
        $task->setParams($params)
            ->setName($taskName)
            ->setHandle($handle)
            ->save();
    }

    /**
     * Stop task
     *
     * @param string $taskName
     * @return bool
     */
    public function stopTask($taskName)
    {
        $tasks = $this->_taskCollection->getTasksByName($taskName, self::TASK_STATUS_IS_PENDING);
        if (!$tasks) {
            return false;
        }
        foreach ($tasks as $task) {
            $task->setStatus(self::TASK_STATUS_SKIPPED)
                ->save();
        }
        return true;
    }

    /**
     * Retrieve task status
     *
     * @param string $taskName
     */
    public function getStatus($taskName)
    {
        $tasks = $this->_taskCollection->getTasksByName($taskName, self::TASK_STATUS_IS_PENDING);
    }
}
