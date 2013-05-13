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
     * @var Enterprise_Queue_Model_TaskFactory
     */
    protected $_taskFactory;

    /**
     * @param Enterprise_Queue_Model_TaskFactory $taskFactory
     */
    public function __construct(Enterprise_Queue_Model_TaskFactory $taskFactory)
    {
        $this->_taskFactory = $taskFactory;
    }

    /**
     * Find task by task name and params
     *
     * @param string $taskName
     * @param array $params
     * @return Enterprise_Queue_Model_Task
     */
    public function get($taskName, array $params = array())
    {
        $task = $this->_taskFactory->create();
        $taskId = md5($taskName . json_encode($params));
        $task->load($taskId);
        if (!$task->getId()) {
            $task->setId($taskId);
        }
        return $task;
    }
}
