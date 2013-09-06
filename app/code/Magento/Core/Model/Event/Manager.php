<?php
/**
 * Event manager
 * Used to dispatch global events
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Event_Manager
{
    /**
     * Events cache
     *
     * @var array
     */
    protected $_events = array();

    /**
     * Event invoker
     *
     * @var Magento_Core_Model_Event_InvokerInterface
     */
    protected $_invoker;

    /**
     * Event config
     *
     * @var Magento_Core_Model_Event_ConfigInterface
     */
    protected $_eventConfig;

    /**
     * Magento event factory
     *
     * @var Magento_EventFactory
     */
    protected $_eventFactory;

    /**
     * Magento event observer factory
     *
     * @var Magento_Event_ObserverFactory
     */
    protected $_eventObserverFactory;

    /**
     * @param Magento_Core_Model_Event_InvokerInterface $invoker
     * @param Magento_Core_Model_Event_ConfigInterface $eventConfig
     * @param Magento_EventFactory $eventFactory
     * @param Magento_Event_ObserverFactory $eventObserverFactory
     */
    public function __construct(
        Magento_Core_Model_Event_InvokerInterface $invoker,
        Magento_Core_Model_Event_ConfigInterface $eventConfig,
        Magento_EventFactory $eventFactory,
        Magento_Event_ObserverFactory $eventObserverFactory
    ) {
        $this->_invoker = $invoker;
        $this->_eventConfig = $eventConfig;
        $this->_eventFactory = $eventFactory;
        $this->_eventObserverFactory = $eventObserverFactory;
    }

    /**
     * Dispatch event
     *
     * Calls all observer callbacks registered for this event
     * and multiple observers matching event name pattern
     *
     * @param string $eventName
     * @param array $data
     */
    public function dispatch($eventName, array $data = array())
    {
        Magento_Profiler::start('EVENT:' . $eventName, array('group' => 'EVENT', 'name' => $eventName));
        foreach ($this->_eventConfig->getObservers($eventName) as $observerConfig) {
            /** @var $event Magento_Event */
            $event = $this->_eventFactory->create(array('data' => $data));
            $event->setName($eventName);

            /** @var $observer Magento_Event_Observer */
            $observer = $this->_eventObserverFactory->create();
            $observer->setData(array_merge(array('event' => $event), $data));

            Magento_Profiler::start('OBSERVER:' . $observerConfig['name']);
            $this->_invoker->dispatch($observerConfig, $observer);
            Magento_Profiler::stop('OBSERVER:' .  $observerConfig['name']);
        }
        Magento_Profiler::stop('EVENT:' . $eventName);
    }
}
