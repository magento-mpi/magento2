<?php

class Mage_Core_Model_Session_Abstract extends Mage_Core_Model_Session_Zend
{
	public function init($namespace)
	{
		parent::init($namespace);
		$hostArr = explode(':', $_SERVER['HTTP_HOST']);
		$this->addHost($hostArr[0]);
		return $this;
	}
	
    public function isValidForHost($host)
    {
    	$hostArr = explode(':', $host);
    	$hosts = $this->getSessionHosts();
    	return (!empty($hosts[$host[0]]));
    	
    }
    
    public function addHost($host)
    {
    	$hostArr = explode(':', $host);
    	$hosts = $this->getSessionHosts();
    	$hosts[$hostArr[0]] = true;
    	$this->setSessionHosts($hosts);
    	return $this;
    }
}