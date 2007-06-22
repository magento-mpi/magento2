<?php

/**
 * Zend Controller
 *
 * @author Andrey Korolyov <andrey@varien.com>
 *
 */
class Mage_Core_Controller_Zend_Front {
    /**
     * Enter description here...
     *
     * @var Zend_Controller_Front
     */
    private $_front;


    /**
     * Enter description here...
     *
     * @var Zend_Controller_Dispatcher_Standard
     */
    private $_dispatcher;
    
    /**
     * Request object
     *
     * @var Zend_Controller_Request_Http
     */
    private $_request;

    /**
     * Enter description here...
     *
     * @var Zend_Controller_Router_Rewrite
     */
    private $_router;
    
    private $_defaultModule;

    /**
     * Controller constructor
     *
     */
    public function __construct() 
    {
        Varien_Profiler::start('ctrl/init');
        
        $this->_front  = Zend_Controller_Front::getInstance();
        $this->_front->throwExceptions(true);
        $this->_front->setParam('useDefaultControllerAlways', true);
        $this->_front->registerPlugin(new Varien_Controller_Plugin_NotFound());
        //$this->_view = new Mage_Core_View_Zend();
        $this->_request = new Zend_Controller_Request_Http();
        $this->_dispatcher = new Varien_Controller_Dispatcher_Standard();
        $this->_front->setDispatcher($this->_dispatcher);
    }
    
    public function getRequest()
    {
        return $this->_request;
    }
    
    public function getFront()
    {
        return $this->_front;
    }

    /**
     * Run controller
     *
     */
    public function run() 
    {
        $defaultModule = 'Mage_Core';
        
        $routers = Mage::getConfig()->getNode('front/routers')->children();
        foreach ($routers as $routerName=>$routerConfig) {
            $router = Mage::getConfig()->getRouterInstance($routerName);
            if (empty($router)) {
                continue;
            }
            $moduleName = (string)$routerConfig->args->module;
            $this->_front->addControllerDirectory(Mage::getModuleDir('controllers', $moduleName), $moduleName);
            $router->addRoutes($this->_front->getRouter());
            if ($routerConfig->is('default')) {
                $defaultModule = $moduleName;
            }
        }

        if (!empty($defaultModule)) {
            $this->_dispatcher->setDefaultModuleName($defaultModule);
        }
        Varien_Profiler::stop('ctrl/init');
        
        Varien_Profiler::start('ctrl/dispatch');
        $this->_front->dispatch($this->_request);
        Varien_Profiler::stop('ctrl/dispatch');
    }
}