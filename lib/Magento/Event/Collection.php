<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Event
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Collection of events
 *
 * @category   Magento
 * @package    Magento_Event
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Event;

use \Magento\Event;

class Collection
{
    /**
     * Array of events in the collection
     *
     * @var array
     */
    protected $_events;
    
    /**
     * Global observers
     * 
     * For example regex observers will watch all events that 
     *
     * @var \Magento\Event\Observer\Collection
     */
    protected $_observers;
    
    /**
     * Initializes global observers collection
     * 
     */
    public function __construct()
    {
        $this->_events = array();
        $this->_globalObservers = new \Magento\Event\Observer\Collection();
    }
    
    /**
     * Returns all registered events in collection
     *
     * @return array
     */
    public function getAllEvents()
    {
        return $this->_events;
    }
    
    /**
     * Returns all registered global observers for the collection of events
     *
     * @return \Magento\Event\Observer\Collection
     */
    public function getGlobalObservers()
    {
        return $this->_globalObservers;
    }
    
    /**
     * Returns event by its name
     *
     * If event doesn't exist creates new one and returns it
     * 
     * @param string $eventName
     * @return \Magento\Event
     */
    public function getEventByName($eventName)
    {
        if (!isset($this->_events[$eventName])) {
            $this->addEvent(new \Magento\Event(array('name'=>$eventName)));
        }
        return $this->_events[$eventName];
    }
    
    /**
     * Register an event for this collection
     *
     * @param \Magento\Event $event
     * @return $this
     */
    public function addEvent(\Magento\Event $event)
    {
        $this->_events[$event->getName()] = $event;
        return $this;
    }
    
    /**
     * Register an observer
     * 
     * If observer has event_name property it will be registered for this specific event.
     * If not it will be registered as global observer
     *
     * @param \Magento\Event\Observer $observer
     * @return $this
     */
    public function addObserver(\Magento\Event\Observer $observer)
    {
        $eventName = $observer->getEventName();
        if ($eventName) {
            $this->getEventByName($eventName)->addObserver($observer);
        } else {
            $this->getGlobalObservers()->addObserver($observer);
        }
        return $this;
    }

    /**
     * Dispatch event name with optional data
     *
     * Will dispatch specific event and will try all global observers
     *
     * @param string $eventName
     * @param array $data
     * @return $this
     */
    public function dispatch($eventName, array $data=array())
    {
        $event = $this->getEventByName($eventName);
        $event->addData($data)->dispatch();
        $this->getGlobalObservers()->dispatch($event);
        return $this;
    }
}
