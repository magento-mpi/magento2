<?php
/**
 * Asynchronous event invoker
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Queue_Model_Event_Invoker_Asynchronous implements Magento_Core_Model_Event_InvokerInterface
{
    /**#@+
     * Configuration parameters
     */
    const CONFIG_PARAMETER_ASYNCHRONOUS = 'asynchronous';
    const CONFIG_PARAMETER_PRIORITY = 'priority';
    /**#@-*/

    /**
     * Queue event handler
     *
     * @var Enterprise_Queue_Model_Event_HandlerInterface
     */
    protected $_queueHandler;

    /**
     * Event invoker
     *
     * @var Magento_Core_Model_Event_Invoker_InvokerDefault
     */
    protected $_invokerDefault;

    /**
     * @param Enterprise_Queue_Model_Event_HandlerInterface $queueHandler
     * @param Magento_Core_Model_Event_Invoker_InvokerDefault $invokerDefault
     */
    public function __construct(
        Enterprise_Queue_Model_Event_HandlerInterface $queueHandler,
        Magento_Core_Model_Event_Invoker_InvokerDefault $invokerDefault
    ) {
        $this->_queueHandler = $queueHandler;
        $this->_invokerDefault = $invokerDefault;
    }

    /**
     * Dispatch event
     *
     * @param array $configuration
     * @param Magento_Event_Observer $observer
     */
    public function dispatch(array $configuration, Magento_Event_Observer $observer)
    {
        if (isset($configuration['config'][self::CONFIG_PARAMETER_ASYNCHRONOUS])
            && $configuration['config'][self::CONFIG_PARAMETER_ASYNCHRONOUS]
        ) {
            $this->_addTaskToAsynchronousProcessing($configuration, $observer);
        } else {
            $this->_invokerDefault->dispatch($configuration, $observer);
        }
    }

    /**
     * Add a task to asynchronous processing
     *
     * @param array $configuration
     * @param Magento_Event_Observer $observer
     */
    protected function _addTaskToAsynchronousProcessing($configuration, $observer)
    {
        $eventName = $observer->getEvent()->getName();
        $data = array(
            'observer' => $observer->toArray(),
            'configuration' => $configuration,
        );
        $priority = isset($configuration['config'][self::CONFIG_PARAMETER_PRIORITY])
            ? $configuration['config'][self::CONFIG_PARAMETER_PRIORITY] : null;

        $this->_queueHandler->addTask($eventName, $data, $priority);
    }
}
