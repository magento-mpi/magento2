<?php

/**
 * Zend Controller
 *
 * @author Andrey Korolyov <andrey@varien.com>
 *
 */
class Mage_Core_Controller_Zend_Admin {
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

        $this->_front  = Zend_Controller_Front::getInstance();
        $this->_front->throwExceptions(true);

        //$this->_front->setParam('useDefaultControllerAlways', true);
        //$this->_front->registerPlugin(new Varien_Controller_Plugin_NotFound());
        //$this->_view = new Mage_Core_View_Zend();
        //$this->_request = new Mage_Core_Controller_Zend_Request();
        $this->_request = new Zend_Controller_Request_Http();

//        $this->_dispatcher = new Zend_Controller_Dispatcher_Standard();
//        $this->_front->setDispatcher($this->_dispatcher);
        Zend::register('view', new Zend_View());
    }

    public function loadModule($modInfo)
    {
        if (is_string($modInfo)) {
            $modInfo = Mage::getModuleInfo($modInfo);
        }
        if (!$modInfo instanceof Mage_Core_Module_Info) {
            Mage::exception('Argument suppose to be module name or module info object');
        }
        if (!$modInfo->isFront()) {
            return false;
        }

        $name = $modInfo->getName();
        if (is_dir($modInfo->getRoot('controllers').DS.'Admin')) {
            $this->_front->addControllerDirectory($modInfo->getRoot('controllers').DS.'Admin', strtolower($name));
        }
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
        $default = Mage::getModuleInfo('Mage_Core')->getRoot('controllers').DS.'Admin';
        $this->_front->addControllerDirectory($default, 'default');

        $this->_front->dispatch($this->_request);
    }
}