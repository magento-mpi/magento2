<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Default event invoker.
 */
class Enterprise_Queue_Model_Core_Event_InvokerAsynchronous implements Mage_Core_Model_Event_InvokerInterface
{
    /**#@+
     * Configuration parameters
     */
    const CONFIG_PARAMETER_ASYNCHRONOUS = 'asynchronous';
    const CONFIG_PARAMETER_PRIORITY = 'priority';
    /**#@-*/

    /**
     * @var Enterprise_Queue_Model_Queue_HandlerInterface
     */
    protected $_queueHandler;

    /**
     * @var Mage_Core_Model_Event_InvokerDefault
     */
    protected $_invokerDefault;

    /**
     * @param Enterprise_Queue_Model_Queue_HandlerInterface $queueHandler
     * @param Mage_Core_Model_Event_InvokerDefault $invokerDefault
     */
    public function __construct(
        Enterprise_Queue_Model_Queue_HandlerInterface $queueHandler,
        Mage_Core_Model_Event_InvokerDefault $invokerDefault
    ) {
        $this->_queueHandler = $queueHandler;
        $this->_invokerDefault = $invokerDefault;
    }

    /**
     * Dispatch event
     *
     * @param array $configuration
     * @param Varien_Event_Observer $observer
     */
    public function dispatch(array $configuration, Varien_Event_Observer $observer)
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
     * @param Varien_Event_Observer $observer
     */
    protected function _addTaskToAsynchronousProcessing($configuration, $observer)
    {
        $eventName = $observer->getEvent()->getName();
        $data = array_merge($observer->toArray());
        $priority = isset($configuration['config'][self::CONFIG_PARAMETER_PRIORITY])
            ? $configuration['config'][self::CONFIG_PARAMETER_PRIORITY] : null;

        $this->_queueHandler->addTask($eventName, $data, $priority);
    }
}
