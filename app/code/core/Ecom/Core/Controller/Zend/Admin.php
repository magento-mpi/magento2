<?php

#include_once 'Zend/Controller/Front.php';
#include_once 'Zend/Controller/Router/Rewrite.php';
#include_once 'Zend/Controller/Router/Route.php';
#include_once 'Zend/View.php';
//include_once 'Varien/Controller/Dispatcher/Standard.php';
#include_once 'Zend/Controller/Dispatcher/Standard.php';
#include_once 'Ecom/Core/View/Zend.php';
//include_once 'Varien/Controller/Plugin/NotFound.php';
#include_once 'Ecom/Core/Controller/Zend/Request.php';


/**
 * Zend Controller
 *
 * @author Andrey Korolyov <andrey@varien.com>
 *
 */
class Ecom_Core_Controller_Zend_Admin {
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
        //$this->_view = new Ecom_Core_View_Zend();
        //$this->_request = new Ecom_Core_Controller_Zend_Request();
        $this->_request = new Zend_Controller_Request_Http();

//        $this->_dispatcher = new Zend_Controller_Dispatcher_Standard();
//        $this->_front->setDispatcher($this->_dispatcher);
        Zend::register('view', new Zend_View());
    }

    public function loadModule($modInfo)
    {
        if (is_string($modInfo)) {
            $modInfo = Ecom::getModuleInfo($modInfo);
        }
        if (!$modInfo instanceof Ecom_Core_Module_Info) {
            Ecom::exception('Argument suppose to be module name or module info object');
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
        $default = Ecom::getModuleInfo('Ecom_Core')->getRoot('controllers').DS.'Admin';
        $this->_front->addControllerDirectory($default, 'default');

        $mod_name = Ecom::getModuleConfig('Ecom_Core', 'controller')->default;

        $view = Zend::registry('view');
        $view->setScriptPath(Ecom::getRoot('layout').DS.'Admin');
        $view->addHelperPath(Ecom::getModuleInfo('Ecom_Core')->getRoot().DS.'View'.DS.'Helper', 'Ecom_Core_View_Zend_Helper_');

        $view->assign('BASE_URL', Ecom::getBaseUrl());
        $view->assign('SKIN_URL',Ecom::getBaseUrl('skin'));

        $this->_front->dispatch($this->_request);
    }
}