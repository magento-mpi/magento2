<?php

class Varien_Helper_LoadComponent {

    /**
     * Enter description here...
     *
     * @var Zend_Controller_Front
     */
    protected $front;

    /**
     * Enter description here...
     *
     * @var Zend_Controller_Request_Http
     */
    protected $request;

    /**
     * Enter description here...
     *
     * @var Zend_Controller_Response_Http
     */
    protected $response;

    /**
     * Enter description here...
     *
     * @var Zend_Controller_Dispatcher_Standard
     */
    protected $dispatcher;



    public function loadComponent($module, $controller, $action, $params = array())
    {
        $this->front = Zend_Controller_Front::getInstance();
        $this->request = $this->front->getRequest();

        $this->request->setModuleName($module);
        $this->request->setControllerName($controller);
        $this->request->setActionName($action);
        $this->request->setDispatched(true);

        $this->response = new Zend_Controller_Response_Http();

        $this->dispatcher = new Zend_Controller_Dispatcher_Standard();
        $this->dispatcher->setControllerDirectory($this->front->getControllerDirectory());
        $this->dispatcher->dispatch($this->request, $this->response);

        echo $this->dispatcher->getResponse();
    }
}
