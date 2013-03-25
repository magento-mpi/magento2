<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Queue
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Queue handler.
 *
 * In this class in future adapters can be created according to a type that has been provided in configuration.
 */
class Saas_Queue_Model_Queue_DefaultHandler implements Enterprise_Queue_Model_Queue_HandlerInterface
{
    /**
     * @var Enterprise_Queue_Model_Queue_AdapterInterface
     */
    protected $_adapter;

    /**
     * @var Saas_Queue_Helper_Gearman
     */
    protected $_helper;

    /**
     * @param Enterprise_Queue_Model_Queue_AdapterInterface $adapter
     * @param Saas_Queue_Helper_Gearman $helper
     */
    public function __construct(
        Enterprise_Queue_Model_Queue_AdapterInterface $adapter,
        Saas_Queue_Helper_Gearman $helper
    ) {
        $this->_adapter = $adapter;
        $this->_helper = $helper;
    }

    /**
     * Add task to queue
     *
     * @param string $eventName
     * @param array $data
     * @param string|null $priority
     * @throws InvalidArgumentException
     * @return Saas_Queue_Model_Queue_DefaultHandler
     */
    public function addTask($eventName, $data, $priority = null)
    {
        $eventData = $data['observer']['event']->getData();
        if (!is_array($eventData)) {
            $eventData = array();
        }
        unset($eventData['name']);
        $taskData = $this->_helper->getTaskParams();
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
        $taskData['task_name'] = $taskName;
        $taskData['params'] = $params;
        $this->_adapter->addTask($taskName, $taskData, $priority);
        return $this;
    }
}
