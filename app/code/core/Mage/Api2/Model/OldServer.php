<?php

class Mage_Api2_Model_OldServer
{
    /**
     * Web service adapter
     *
     * @var Mage_Api_Model_Server_Adaper_Interface
     */
    protected $_adapter;

    public function __construct()
    {
        // query parameter "type" is set by .htaccess rewrite rule
        $adapterAlias = Mage::app()->getRequest()->getParam('type');
        $adapterCode = $this->getAdapterCodeByAlias($adapterAlias);

        $this->init($adapterCode);
    }

    public function run()
    {
        $this->getAdapter()->run();
        Mage::app()->getResponse()->sendResponse();
    }

    /**
     * Set up Adapter and Handler
     *
     * @param string $adapter
     * @param string $handler
     * @return SoapServer
     */
    protected function init($adapter='default', $handler='default')
    {
        $this->initAdapter($adapter);
        $this->initHandler($handler);

        return $this;
    }

    protected function initAdapter($adapter)
    {
        $adapters = $this->getConfig()->getActiveAdapters();
        if (!isset($adapters[$adapter])) {
            throw new Exception(sprintf('Invalid webservice adapter "%s" specified.'), $adapter);
        }

        /* @var $adapterModel Mage_Api_Model_Server_Adapter_Interface */
        $adapterModel = Mage::getModel((string) $adapters[$adapter]->model);
        if (!($adapterModel instanceof Mage_Api_Model_Server_Adapter_Interface)) {
            throw new Exception(sprintf('Invalid webservice adapter "%s" specified.'), $adapter);
        }

        $this->setAdapter($adapterModel);
    }

    protected function initHandler($handler)
    {
        $handlers = $this->getConfig()->getHandlers();
        if (!isset($handlers->$handler)) {
            throw new Exception(sprintf('Invalid webservice handler "%s" specified.', $handler));
        }

        $handlerClassName = Mage::getConfig()->getModelClassName((string) $handlers->$handler->model);
        $this->getAdapter()->setHandler($handlerClassName);
    }

    /**
     * Retrieve web service adapter
     *
     * @return Mage_Api_Model_Server_Adapter_Interface
     */
    protected function getAdapter()
    {
        return $this->_adapter;
    }

    /**
     * Set web service adapter
     *
     * @param Mage_Api_Model_Server_Adapter_Interface $adapter
     * @return Mage_Api_Model_Server_Adapter_Interface
     */
    protected function setAdapter(Mage_Api_Model_Server_Adapter_Interface $adapter)
    {
        return $this->_adapter = $adapter;
    }

    protected function getConfig()
    {
        $config = new Mage_Api_Model_Config;
        //$config = Mage::getSingleton('api/config');

        return $config;
    }

    /**
     * Complex retrieve adapter code by calling auxiliary model method
     *
     * @param string $alias Alias name
     * @return string|null Returns NULL if no alias found
     */
    protected function getAdapterCodeByAlias($alias)
    {
        $aliases = $this->getConfig()->getAdapterAliases();

        if (!isset($aliases[$alias])) {
            // if no adapters found in aliases - find it by default, by code
            return $alias;
        }
        $object = Mage::getModel($aliases[$alias][0]);
        $method = $aliases[$alias][1];

        if (!method_exists($object, $method)) {
            Mage::throwException(Mage::helper('api')->__('Can not find webservice adapter.'));
        }
        return $object->$method();
    }
}
