<?php

class Varien_Event extends Varien_Object
{
    protected $_observers;
    
    public function __construct(array $data=array())
    {
        $this->_observers = new Varien_Event_Observer_Collection();
        parent::__construct($data);
    }
    
    public function getObservers()
    {
        return $this->_observers;
    }
    
    public function addObserver(Varien_Event_Observer $observer)
    {
        $this->getObservers()->addObserver($observer);
        return $this;
    }
    
    public function removeObserverByName($observerName)
    {
        $this->getObservers()->removeObserverByName($observerName);
        return $this;
    }
    
    public function dispatch()
    {
        $this->getObservers()->dispatch($this);
        return $this;
    }
}