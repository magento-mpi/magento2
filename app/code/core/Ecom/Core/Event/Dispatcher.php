<?php

class Ecom_Core_Event_Dispatcher
{
    protected $_observers = array();
    protected $_data = null;
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $eventName
	 */
	public function __construct(array $data=array())
	{
		$this->_data = $data;
	}
	
	public function getName() 
	{
	    return $this->_data['name'];
	}
	
	/**
	 * Enter description here...
	 *
	 * @param Varien_Event_Observer_Abstract $observer
	 * @param string $observerName
	 */
	public function addObserver(Ecom_Core_Event_Observer $observer, $observerName='')
	{
	    if (!empty($observerName)) {
		    $this->_observers[$observerName] = $observer;
	    } else {
            $this->_observers[] = $observer;
	    }
	    return $this;
	}
	
	public function removeObserver($observerName)
	{
	    if (!empty($this->_observers[$observerName])) {
	        unset($this->_observers[$observerName]);
	    }
	    return $this;
	}
	
	public function getObservers()
	{
	    return $this->_observers;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param array $arguments
	 */
	public function dispatch(array $arguments=array())
	{
		foreach ($this->_observers as $observer) {
		    if ($observer->getCallback()) {
		      call_user_func_array($observer->getCallback(), $arguments);
		    }
		}
	}
}


