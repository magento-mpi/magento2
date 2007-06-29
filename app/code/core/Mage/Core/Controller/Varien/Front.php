<?php

class Mage_Core_Controller_Varien_Front
{
    protected $_request;
    
    protected $_response;
    
    protected $_defaults = array();
    
    protected $_routers = array();
    
    public function setDefault($key, $value=null)
    {
        if (is_array($key)) {
            $this->_defaults = $key;
        } else {
            $this->_defaults[$key] = $value;
        }
        return $this;
    }
    
    public function getDefault($key=null)
    {
        if (is_null($key)) {
            return $this->_defaults;
        } elseif (isset($this->_defaults[$key])) {
            return $this->_defaults[$key];
        }
        return false;
    }
    
    public function getRequest()
    {
        if (empty($this->_request)) {
            $this->_request = new Zend_Controller_Request_Http();
        }
        return $this->_request;
    }
    
    public function getResponse()
    {
        if (empty($this->_response)) {
            $this->_response = new Zend_Controller_Response_Http();
        }
        return $this->_response;
    }
    
    public function addRouter($name, Mage_Core_Controller_Varien_Router_Abstract $router)
    {
        $router->setFront($this);
        $this->_routers[$name] = $router;
        return $this;
    }
    
    public function getRouter($name)
    {
        if (isset($this->_routers[$name])) {
            return $this->_routers[$name];
        }
        return false;
    }
    
    public function init()
    {
        // set defaults
        $defaultModule = (string)Mage::getSingleton('core/store')->getConfig('core/defaultFrontName');
        $this->setDefault(array('module'=>$defaultModule, 'controller'=>'index', 'action'=>'index'));

        // init standard modules router
        $standard = new Mage_Core_Controller_Varien_Router_Standard();
        $this->addRouter('standard', $standard);
        
        // init modules
        $routers = Mage::getConfig()->getNode('front/routers')->children();
        foreach ($routers as $routerName=>$routerConfig) {
            $use = (string)$routerConfig->use;
            
            if (true || $use==='standard') {
                $module = (string)$routerConfig->args->module;
                $frontName = (string)$routerConfig->args->frontName;
                $standard->addModule($frontName, $module);
            }
        }
        
        // init custom routers
        Mage::dispatchEvent('initControllerRouters', array('front'=>$this));

        // init default router (articles and 404)
        $default = new Mage_Core_Controller_Varien_Router_Default();
        $this->addRouter('default', $default);
    }
    
    public function dispatch()
    {
        $request = $this->getRequest();
        $request->setPathInfo()->setDispatched(false);

        $i = 0;
        while (!$request->isDispatched() && $i++<100) {
            foreach ($this->_routers as $router) {
                if ($router->match($this->getRequest())) {
                    break;
                }
            }
        }
        
        $this->getResponse()->sendResponse();

        return $this;
    }
}