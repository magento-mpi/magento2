<?php

class Mage_Core_Model_Session_Abstract_Varien extends Varien_Object
{
    public function start()
    {
    	if (isset($_SESSION)) {
    		return $this;
    	}
    	
        Varien_Profiler::start(__METHOD__.'/setOptions');
        session_save_path(Mage::getBaseDir('session'));
        Varien_Profiler::stop(__METHOD__.'/setOptions');
/*
        $sessionResource = Mage::getResourceSingleton('core/session');
        $sessionResource->setSaveHandler();
*/      
        
		if (!$this->hasCookieLifetime()) {
			$this->setCookieLifetime(0);
		}
		if (!$this->hasCookiePath()) {
			$this->setCookiePath('/');
		}
		if (!$this->hasCookieDomain()) {
			$this->setCookieDomain($_SERVER['HTTP_HOST']);
		}

        Varien_Profiler::start(__METHOD__.'/start');
        session_set_cookie_params(
        	$this->getCookieLifetime(), 
        	$this->getCookiePath(),
        	$this->getCookieDomain()
        );
        
        session_start();
        Varien_Profiler::stop(__METHOD__.'/start');

        return $this;
    }
    
    public function init($namespace)
    {
    	if (!isset($_SESSION)) {
    		$this->start();
    	}
        if (!isset($_SESSION[$namespace])) {
        	$_SESSION[$namespace] = array();
        }
        $this->_data = &$_SESSION[$namespace];

        return $this;
    }
    
    public function getData($key='', $clear=false)
    {
        $data = parent::getData($key);
        if ($clear && isset($this->_data[$key])) {
            unset($this->_data[$key]);
        }
        return $data;
    }

    public function getSessionId()
    {
        return session_id();
    }
    
    public function unsetAll()
    {
    	$this->unsetData();
    	return $this;
    }
}