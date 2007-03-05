<?php

/**
 * Enter description here...
 *
 */
class Ecom_Core_Event_Observer
{
    protected $_callback = null;
    protected $_arguments = null;
	/**
	 * Enter description here...
	 *
	 * @param array $callback
	 * @param array $arguments
	 */
	public function __construct($callback, array $arguments = array()) 
	{
		$this->_callback = $callback;
		$this->_arguments = $arguments;
	}
	
	public function getCallback()
	{
	    return $this->_callback;
	}

	public function getArguments()
	{
	    return $this->_arguments;
	}
}
