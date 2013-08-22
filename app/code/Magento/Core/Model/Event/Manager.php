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
     * @var Magento_Core_Model_Event_Config
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
     * @param Magento_Core_Model_Event_Config $eventConfig
     * @param Magento_EventFactory $eventFactory
     * @param Magento_Event_ObserverFactory $eventObserverFactory
     */
    public function __construct(
        Magento_Core_Model_Event_InvokerInterface $invoker,
        Magento_Core_Model_Event_Config $eventConfig,
        Magento_EventFactory $eventFactory,
        Magento_Event_ObserverFactory $eventObserverFactory
    ) {
        $this->_invoker = $invoker;
        $this->_eventConfig = $eventConfig;
        $this->_eventFactory = $eventFactory;
        $this->_eventObserverFactory = $eventObserverFactory;
        $this->addEventArea(Magento_Core_Model_App_Area::AREA_GLOBAL);
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
        foreach ($this->_events as $areaEvents) {
            if (!isset($areaEvents[$eventName])) {
                continue;
            }

            /** @var $event Magento_Event */
            $event = $this->_eventFactory->create(array('data' => $data));
            $event->setName($eventName);
            /** @var $observer Magento_Event_Observer */
            $observer = $this->_eventObserverFactory->create();

            foreach ($areaEvents[$eventName] as $obsName => $obsConfiguration) {
                $observer->setData(array_merge(array('event' => $event), $data));

                Magento_Profiler::start('OBSERVER:' . $obsName);
                $this->_invoker->dispatch($obsConfiguration, $observer);
                Magento_Profiler::stop('OBSERVER:' . $obsName);
            }
        }
        Magento_Profiler::stop('EVENT:' . $eventName);
    }

    /**
     * Add event area
     *
     * @param string $area
     * @return Magento_Core_Model_Event_Manager
     */
    public function addEventArea($area)
    {
        if (!isset($this->_events[$area])) {
            Magento_Profiler::start('config_event_' . $area);
            $this->_events[$area] = array();
            $this->_eventConfig->populate($this, $area);
            Magento_Profiler::stop('config_event_' . $area);
        }
        return $this;
    }

    /**
     * Add event observer
     *
     * @param string $area
     * @param string $eventName
     * @param array $observers example array('observerName' => array('type' => ..., 'model' => ..., 'method' => ... ),)
     */
    public function addObservers($area, $eventName, array $observers)
    {
        if (!isset($this->_events[$area])) {
             $this->addEventArea($area);
        }

        $existingObservers = array();
        if (isset($this->_events[$area][$eventName])) {
            $existingObservers = $this->_events[$area][$eventName];
        }
        $this->_events[$area][$eventName] = array_merge($existingObservers, $observers);
    }
}
