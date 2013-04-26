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
     * Queue client
     *
     * @var Magento_Queue_ClientInterface
     */
    protected $_client;

    /**
     * @param Magento_Queue_ClientInterface $client
     */
    public function __construct(Magento_Queue_ClientInterface $client)
    {
        $this->_client = $client;
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
        $this->_client->addTask($eventName, $data, $priority);
        return $this;
    }
}
