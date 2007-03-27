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

        $this->_dispatcher = new Varien_Controller_Dispatcher_Standard();
        $this->_front->setDispatcher($this->_dispatcher);
       // Zend_Registry::set('view', new Zend_View());
    }

    public function loadModule($modInfo)
    {
        if (is_string($modInfo)) {
            $modInfo = Mage::getConfig()->getModuleInfo($modInfo);
        }
        if (!$modInfo instanceof Varien_Simplexml_Object) {
            Mage::exception('Argument suppose to be module name or module info object');
        }
        if ('true'!==(string)$modInfo->active 
            || empty($modInfo->load->admin->controller->active) 
            || 'true'!==(string)$modInfo->load->admin->controller->active) {
            return false;
        }

        $name = $modInfo->getName();
        $dir = Mage::getBaseDir('controllers', $name).DS.'Admin';
        if (is_dir($dir)) {
            $this->_front->addControllerDirectory($dir, strtolower($name));
        }
        
        if (!empty($modInfo->load->admin->controller->default) 
            && 'true'===(string)$modInfo->load->admin->controller->default) {
            $this->_defaultModule = strtolower($name);
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
        $default = Mage::getBaseDir('controllers', 'Mage_Core').DS.'Admin';
        $this->_front->addControllerDirectory($default, 'default');

        foreach (Mage::getConfig()->getXml()->modules->children() as $module) {
            $this->loadModule($module);
        }

        if (!empty($this->_defaultModule)) {
            #$this->_dispatcher->setDefaultModuleName($this->_defaultModule);
        }

        $this->_front->dispatch($this->_request);
    }
}