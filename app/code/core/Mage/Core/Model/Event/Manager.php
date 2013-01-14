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
 * Event manager. Used to dispatch global events
 */
class Mage_Core_Model_Event_Manager
{
    /**
     * Events cache
     *
     * @var array
     */
    protected $_events = array();

    /**
     * Observer model factory
     *
     * @var Mage_Core_Model_ObserverFactory
     */
    protected $_observerFactory;

    /**
     * Observer model factory
     *
     * @var Mage_Core_Model_Event_Config
     */
    protected $_eventConfig;

    /**
     * @param Mage_Core_Model_ObserverFactory $observerFactory
     * @param Mage_Core_Model_Event_Config $eventConfig
     */
    public function __construct(
        Mage_Core_Model_ObserverFactory $observerFactory,
        Mage_Core_Model_Event_Config $eventConfig
    ) {
        $this->_observerFactory = $observerFactory;
        $this->_eventConfig = $eventConfig;
        $this->addEventArea(Mage_Core_Model_App_Area::AREA_GLOBAL);
    }

    /**
     * Dispatch event
     *
     * @param string $eventName
     * @param array $args
     */
    protected function _dispatchEvent($eventName, $args)
    {
        foreach ($this->_events as $area => $events) {
            if (false == isset($events[$eventName])) {
                continue;
            }

            $event = new Varien_Event($args);
            $event->setName($eventName);
            $observer = new Varien_Event_Observer();

            foreach ($this->_events[$area][$eventName] as $obsName => $obsConfiguration) {
                $observer->setData(array('event' => $event));
                Magento_Profiler::start('OBSERVER:' . $obsName);
                switch ($obsConfiguration['type']) {
                    case 'disabled':
                        break;
                    case 'object':
                    case 'model':
                        $method = $obsConfiguration['method'];
                        $observer->addData($args);
                        $object = $this->_observerFactory->create($obsConfiguration['model']);
                        $this->_callObserverMethod($object, $method, $observer);
                        break;
                    default:
                        $method = $obsConfiguration['method'];
                        $observer->addData($args);
                        $object = $this->_observerFactory->get($obsConfiguration['model']);
                        $this->_callObserverMethod($object, $method, $observer);
                        break;
                }
                Magento_Profiler::stop('OBSERVER:' . $obsName);
            }
        }
    }

    /**
     * Performs non-existent observer method calls protection
     *
     * @param object $object
     * @param string $method
     * @param Varien_Event_Observer $observer
     * @return Mage_Core_Model_App
     * @throws Mage_Core_Exception
     */
    protected function _callObserverMethod($object, $method, $observer)
    {
        if (method_exists($object, $method)) {
            $object->$method($observer);
        } elseif (Mage::getIsDeveloperMode()) {
            Mage::throwException('Method "' . $method . '" is not defined in "' . get_class($object) . '"');
        }
        return $this;
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
        Magento_Profiler::start('EVENT:' . $eventName);
        $this->_dispatchEvent($eventName, $data);
        Magento_Profiler::stop('EVENT:' . $eventName);
    }

    /**
     * Add event area
     *
     * @param string $area
     * @return Mage_Core_Model_Event_Manager
     */
    public function addEventArea($area)
    {
        if (!isset($this->_events[$area])) {
            $this->_events[$area] = array();
            $this->_eventConfig->populate($this, $area);
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
