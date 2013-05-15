<?php
/**
 * Queue Event handler.
 *
 * In this class in future adapters can be created according to a type that has been provided in configuration.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Queue_Model_Event_Handler implements Enterprise_Queue_Model_Event_HandlerInterface
{
    /**
     * Queue
     *
     * @var Enterprise_Queue_Model_QueueInterface
     */
    protected $_queue;

    /**
     * Task name prefix
     * @var string
     */
    protected $_taskNamePrefix = '';

    /**
     * @param Enterprise_Queue_Model_QueueInterface $queue
     */
    public function __construct(Enterprise_Queue_Model_QueueInterface $queue)
    {
        $this->_queue = $queue;
    }

    /**
     * Add task to queue
     *
     * @param string $eventName
     * @param array $data
     * @param string|null $priority
     * @throws InvalidArgumentException
     * @return Saas_Queue_Model_Event_Handler
     */
    public function addTask($eventName, $data, $priority = null)
    {
        $eventData = $data['observer']['event']->getData();
        if (!is_array($eventData)) {
            $eventData = array();
        }
        unset($eventData['name']);
        if (
            !array_key_exists('params', $data['configuration']['config'])
            || !is_array($data['configuration']['config']['params'])
            || empty($data['configuration']['config']['params']['task_name'])
        ) {
            //Backward compatibility
            $taskName = $eventName;
            $params = $eventData;
        } else {
            $taskName = $data['configuration']['config']['params']['task_name'];
            $params = array(
                'event_name' => $eventName,
                'event_data' => $eventData,
            );
            if (array_key_exists('event_area', $data['configuration']['config']['params'])) {
                $params['event_area'] = $data['configuration']['config']['params']['event_area'];
            }
        }
        $taskData = array('task_name' => $taskName, 'params' => $params);
        $this->_queue->addTask($taskName, $taskData, $priority);
        return $this;
    }
}
