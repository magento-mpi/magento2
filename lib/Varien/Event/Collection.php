<?php

class Varien_Event_Collection
{
    protected $_events;
    
    public function __construct()
    {
        $this->_events = array();
    }
    
    public function &getEvents()
    {
        return $this->_events;
    }
    
    public function getEvent($eventId)
    {
        return $this->_events[$eventId];
    }
    
    public function append(Varien_Event $event)
    {
        $this->_events[$event->getId()] = $event;
        return $this;
    }

    public function dispatchRegex($regex)
    {
        
    }
}