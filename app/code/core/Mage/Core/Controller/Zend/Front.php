<?php

class Mage_Core_Controller_Zend_Front extends Zend_Controller_Front
{    
    /**
     * Singleton instance
     * 
     * to call overloaded methods...
     * 
     * @return Mage_Core_Controller_Zend_Front
     */
    public static function getInstance()
    {
        if (null === $this->_instance) {
            $this->_instance = new self();
        }

        return $this->_instance;
    }
    
    public function init()
    {
        $this->throwExceptions(true);
        $this->setParam('useDefaultControllerAlways', true);
        $this->setRequest(new Zend_Controller_Request_Http());
        $this->registerPlugin(new Varien_Controller_Plugin_NotFound());
        
        $defaultModule = (string)Mage::getConfig()->getNode('front/default/router');
        $this->setDefaultModule($defaultModule);
        
        $dispatcher = new Mage_Core_Controller_Zend_Dispatcher();
        $dispatcher->setFrontController($this)
            ->setDefaultModule($defaultRoute)
            ->setDefaultControllerName('index')
            ->setDefaultAction('index');
        $this->setDispatcher($dispatcher);
            
        $routers = Mage::getConfig()->getNode('front/routers')->children();
        foreach ($routers as $routerName=>$routerConfig) {
            $use = (string)$routerConfig->use;
            
            if (true || $use==='default') {
                $module = (string)$routerConfig->args->module;
                $frontName = (string)$routerConfig->args->frontName;
                $dispatcher->addModule($module, $frontName);
            }
        }
        
        $defaults = array();
        $route = new Zend_Controller_Router_Route_Module($defaults, $dispatcher);
        $this->getRouter()->addRoute('default', $route);
        
        return $this;
    }
}