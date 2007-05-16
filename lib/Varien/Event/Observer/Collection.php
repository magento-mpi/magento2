<?php

class Varien_Event_Observer_Collection
{
    protected $_observers;
    
    public function __construct()
    {
        $this->_observers = array();
    }
    
    public function getAllObservers()
    {
        return $this->_observers;
    }
    
    public function getObserverByName($observerName)
    {
        return $this->_observers[$observerName];
    }
    
    public function addObserver(Varien_Event_Observer $observer)
    {
        $this->_observers[$observer->getName()] = $observer;
        return $this;
    }
    
    public function removeObserverByName($observerName)
    {
        unset($this->_observers[$observerName]);
        return $this;
    }
    
    public function dispatch(Varien_Event $event)
    {
        foreach ($this->_observers as $observer) {
            $observer->dispatch($event);
        }
        return $this;
    }
}