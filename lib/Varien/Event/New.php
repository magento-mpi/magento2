<?php

class Varien_Event extends Varien_Object
{
    protected $_observers;
    
    public function __construct()
    {
        #$this->_observers = new Varien_Event_Observer_Collection();
        $this->_observers = array();
    }
    
    public function &getObservers()
    {
        return $this->_observers;
    }
    
    public function addObserver(Varien_Event_Observer $observer)
    {
        $observers = $this->getObservers();
        $observers[$observer->getId()] = $observer;
        return $this;
    }
    
    public function removeObserver($observerId)
    {
        $observers = $this->getObservers();
        foreach ($observers as $i=>$observer) {
            if ($observer->getId()==$observerId) {
                unset($observers[$i]);
            }
        }
        return $this;
    }
    
    public function dispatch()
    {
        $observers = $this->getObservers();
        foreach ($observers as $observer) {
            $observer->dispatch($this);
        }
        return $this;
    }
}