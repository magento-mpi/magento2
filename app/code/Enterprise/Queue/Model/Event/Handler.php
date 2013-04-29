<?php
/**
 * Queue default handler
 *
 * In this class in future adapters can be created according to a type that has been provided in configuration
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Queue_Model_Event_Handler implements Enterprise_Queue_Model_Event_HandlerInterface
{
    /**
     * Queue interface
     *
     * @var Enterprise_Queue_Model_QueueInterface
     */
    protected $_queue;

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
     * @return Enterprise_Queue_Model_Event_Handler
     */
    public function addTask($eventName, $data, $priority = null)
    {
        $this->_queue->addTask($eventName, $data, $priority);
        return $this;
    }
}
