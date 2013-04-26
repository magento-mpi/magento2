<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Enterprise_Queue_Model_Queue implements Enterprise_Queue_Model_QueueInterface
{
    protected $_taskCollection;

    public function __construct(Enterprise_Queue_Model_Resource_Task_Collection $taskCollection)
    {
        $this->_taskCollection = $backend;
    }

    public function addTask($taskName, $params, $priority)
    {
        try {
            $task = $this->_taskCollection->get($taskName, $params);
            if ($task && $task->getStatus() == Task::STATUS_PENDING) {
                return $this;
            }
            $this->_taskCollection->addTask($taskName, $params, $priority);
        } catch (Exception $e) {
            throw new Enterprise_Queue_Model_AddException(
                'Unable to add task [' . $taskName . "] to task pool.\n"
                    . $e->getMessage() . "\n"
                    . $e->getTraceAsString()
            );
        }
        return $this;
    }

    public function stopTask($taskName)
    {
        $task = $this->_taskCollection->getTaskByName($taskName);
        if ($task->getStatus() == Enterprise_Queue_Model_Task::STATUS_PENDING) {
            $this->_taskCollection->stopTask($taskName);
        }
    }

    /**
     * @param strign $taskName
     * @return bool
     */
    public function isRunning($taskName)
    {
        return !!count($this->_taskCollection->getRunningTasksByName($taskName));
    }
}
