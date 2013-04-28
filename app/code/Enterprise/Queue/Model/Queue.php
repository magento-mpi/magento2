<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Enterprise_Queue_Model_Queue implements Enterprise_Queue_Model_QueueInterface
{
    /**
     * @var Enterprise_Queue_Model_Resource_Task_Collection
     */
    protected $_taskRepository;

    /**
     * @var Enterprise_Queue_Model_Config
     */
    protected $_queueConfig;

    /**
     * @var Magento_Queue_ClientInterface
     */
    protected $_client;

    /**
     * @param Enterprise_Queue_Model_Config $queueConfig
     * @param Enterprise_Queue_Model_TaskRepository $taskRepository
     * @param Magento_Queue_ClientInterface $client
     */
    public function __construct(
        Enterprise_Queue_Model_Config $queueConfig,
        Enterprise_Queue_Model_TaskRepository $taskRepository,
        Magento_Queue_ClientInterface $client
    ) {
        $this->_taskRepository = $taskRepository;
        $this->_queueConfig = $queueConfig;
        $this->_client = $client;
    }

    /**
     * @param string $taskName
     * @param array $params
     * @param string $priority
     * @return $this
     * @throws Enterprise_Queue_Model_AddException
     */
    public function addTask($taskName, array $params, $priority)
    {
        $taskWorkload = Zend_Json::encode($params);
        $taskId       = md5($taskName . $taskWorkload);
        $task = $this->_taskRepository->find($taskId);
        if ($task) {
            $taskStatus = $task->getStatus();
            if ($taskStatus == Enterprise_Queue_Model_Task::STATUS_PENDING) {
                return $this;
            } elseif ($taskStatus == Enterprise_Queue_Model_Task::STATUS_SKIPPED
                || $taskStatus == Enterprise_Queue_Model_Task::STATUS_COMPLETE
            ) {
                $taskId = md5($taskName . $taskWorkload . microtime());
            }
        }

        try {
            $handle = $this->_client->addBackgroundTask($taskName, $params, $priority, $taskId);
            $this->_taskRepository->create($taskId, $handle);
        } catch (Exception $e) {
            throw new Enterprise_Queue_Model_AddException(
                'Unable to add task [' . $taskName . "] to task pool.\n"
                    . $e->getMessage() . "\n"
                    . $e->getTraceAsString()
            );
        }

        return $this;
    }

    /**
     * Stop task by name
     *
     * @param string $taskName
     * @return bool
     */
    public function stopTask($taskName)
    {
        $tasks = $this->_taskRepository->findPendingByName($taskName);
        if (count($tasks)) {
            foreach($tasks as $task) {
                $task->stop()
                    ->save();
            }
            return true;
        }
        return false;
    }

    /**
     * Is task running
     *
     * @param string $taskName
     * @return bool
     */
    public function isRunning($taskName)
    {
        return !!count($this->_taskRepository->findRunningByName($taskName));
    }
}
