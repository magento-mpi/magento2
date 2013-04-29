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
     * @var Magento_JobQueue_ClientInterface
     */
    protected $_client;

    /**
     * @param Enterprise_Queue_Model_Config $queueConfig
     * @param Enterprise_Queue_Model_TaskRepository $taskRepository
     * @param Magento_JobQueue_ClientInterface $client
     */
    public function __construct(
        Enterprise_Queue_Model_Config $queueConfig,
        Enterprise_Queue_Model_TaskRepository $taskRepository,
        Magento_JobQueue_ClientInterface $client
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
        try {
            $params = array_merge_recursive($this->_queueConfig->getTaskParams(), $params);
            $task = $this->_taskRepository->get($taskName, $params);
            if ($task->getHandle()) {
                $task->setStatus($this->_client->getStatus($task->getHandle()));
            }
            if ($task->isEnqueued()) {
                return $this;
            }
            $task->setHandle($this->_client->addBackgroundTask($taskName, $params, $priority, $task->getId()));
            $task->save();
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
     * Retrieve
     *
     * @param string $taskName
     * @param array $params
     * @return Enterprise_Queue_Model_Task
     */
    public function getTask($taskName, array $params = array())
    {
        $task = $this->_taskRepository->get($taskName, $params);
        if ($task->getHandle()) {
            $task->setStatus($this->_client->getStatus($task->getHandle()));
        }
        return $task;
    }
}
