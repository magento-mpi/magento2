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
class Enterprise_Queue_Model_Queue_DefaultHandler implements Enterprise_Queue_Model_Queue_HandlerInterface
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_adapter;

    /**
     * @param Enterprise_Queue_Model_Queue_AdapterInterface $adapter
     */
    public function __construct(Enterprise_Queue_Model_Queue_AdapterInterface $adapter)
    {
        $this->_adapter = $adapter;
    }

    /**
     * Add task to queue
     *
     * @param string $eventName
     * @param array $data
     * @param string|null $priority
     * @return Enterprise_Queue_Model_Queue_DefaultHandler
     */
    public function addTask($eventName, $data, $priority = null)
    {
        $this->_adapter->addTask($eventName, $data, $priority);

        return $this;
    }
}
