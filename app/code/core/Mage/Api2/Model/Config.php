<?php

class Mage_Api2_Model_Config extends Varien_Simplexml_Config    //extends Mage_Api_Model_Config
{
    const CACHE_TAG = 'config_api2';
    const CACHE_ID  = 'config_api2';

    /**
     * Constructor
     * Initializes XML for this configuration
     *
     * @param string|Varien_Simplexml_Element $sourceData
     */
    public function __construct($sourceData=null)
    {
        $this->setCacheId(self::CACHE_ID);
        $this->setCacheTags(array(self::CACHE_TAG));

        parent::__construct($sourceData);
        $this->_construct();
    }

    /**
     * Fetch all routes of the given api type from config files api2.xml
     *
     * @param string $apiType
     * @return array
     * @throws Mage_Api2_Exception
     */
    public function getRoutes($apiType)
    {
        switch ($apiType)
        {
            case Mage_Api2_Model_Server::API_TYPE_REST:
                $routes = $this->getRoutesRest();
                break;

            case Mage_Api2_Model_Server::API_TYPE_SOAP:
                $routes = $this->getRoutesSoap();
                break;

            default: throw new Mage_Api2_Exception(sprintf('Invalid API type "%s".', $apiType), 400);
        }

        return $routes;
    }

    /**
     * Init configuration for WS API
     *
     * @return Mage_Api2_Model_Config
     */
    protected function _construct()
    {
        if (Mage::app()->useCache(self::CACHE_ID)) {
            if ($this->loadCache()) {
                return $this;
            }
        }

        $config = Mage::getConfig()->loadModulesConfiguration('api2.xml');
        $this->setXml($config->getNode('api2'));

        if (Mage::app()->useCache(self::CACHE_ID)) {
            $this->saveCache();
        }
        return $this;
    }

    /**
     * Retrieve all resources from config files api2.xml
     *
     * @return Varien_Simplexml_Element
     */
    protected function getResources()
    {
        return $this->getNode('resources')->children();
    }

    /**
     * Fetch all routes for REST API
     *
     * @return array
     */
    protected function getRoutesRest()
    {
        $routes = array();
        foreach ($this->getResources() as $ns=>$resource) {
            if (!$resource->routes) {
                continue;
            }

            foreach ($resource->routes->children() as $route) {
                $mask = (string)$route->mask;
                $defaults = array(
                    'model' => (string)$resource->model,
                    'type'  => (string)$resource->type,     //TODO $ns can be used instead?
                );

                $reqs = array();
                $routes[] = new Mage_Api2_Model_Route_Rest($mask, $defaults, $reqs);
                //$routes[] = new Mage_Api2_Model_Route($mask, $defaults, $reqs);
                //$routes[] = new Zend_Controller_Router_Route($mask, $defaults, $reqs);
                //$routes[] = new Zend_Controller_Router_Route_Regex($mask, $defaults, $map, $reverse);
            }
        }

        return $routes;
    }

    /**
     * Fetch all routes for SOAP API
     *
     * @return array
     */
    protected function getRoutesSoap()
    {
        $routes = array();

        $resourcesAlias = $this->getResourcesAlias();
        $resources      = $this->getResources();

        return $routes;
    }
}
