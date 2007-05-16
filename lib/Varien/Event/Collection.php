<?php

class Varien_Event_Collection
{
    protected $_events;
    
    /**
     * Global observers
     * 
     * For example regex observers will watch all events that 
     *
     * @var unknown_type
     */
    protected $_observers;
    
    public function __construct()
    {
        $this->_events = array();
        $this->_globalObservers = new Varien_Event_Observer_Collection();
    }
    
    public function getAllEvents()
    {
        return $this->_events;
    }
    
    public function getGlobalObservers()
    {
        return $this->_globalObservers;
    }
    
    public function getEventByName($eventName)
    {
        if (!isset($this->_events[$eventName])) {
            $this->addEvent(new Varien_Event(array('name'=>$eventName)));
        }
        return $this->_events[$eventName];
    }
    
    public function addEvent(Varien_Event $event)
    {
        $this->_events[$event->getName()] = $event;
        return $this;
    }
    
    public function addObserver(Varien_Event_Observer $observer)
    {
        $eventName = $observer->getEventName();
        if ($eventName) {
            $this->getEventByName($eventName)->addObserver($observer);
        } else {
            $this->getGlobalObservers()->addObserver($observer);
        }
        return $this;
    }
    
    public function dispatch($eventName, array $data=array())
    {
        $event = $this->getEventByName($eventName);
        $event->addData($data)->dispatch();
        $this->getGlobalObservers()->dispatch($event);
        return $this;
    }
}