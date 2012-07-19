<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Webservice api2 router model
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Router
{
    /**
     * Routes which are stored in module config files api2.xml
     *
     * @var array
     */
    protected $_routes = array();

    /**
     * Set routes
     *
     * @param array $routes
     * @return Mage_Api2_Model_Router
     */
    public function setRoutes(array $routes)
    {
        $this->_routes = $routes;

        return $this;
    }

    /**
     * Get routes
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->_routes;
    }

    /**
     * Route the Request, the only responsibility of the class
     * Find route that match current URL, set parameters of the route to Request object
     *
     * @param Mage_Api2_Model_Request $request
     * @throws Mage_Api2_Exception
     * @return Mage_Api2_Model_Route_Rest
     */
    public function match(Mage_Api2_Model_Request $request)
    {
        /** @var $route Mage_Api2_Model_Route_Rest */
        foreach ($this->getRoutes() as $route) {
            if ($params = $route->match($request)) {
                // TODO: Remove params set to $request
                $request->setParams($params);
                return $route;
            }
        }
        throw new Mage_Api2_Exception('Request does not match any route.', Mage_Api2_Model_Server::HTTP_NOT_FOUND);
    }

    /**
     * Set API type to request as a result of one pass route
     *
     * @param Mage_Api2_Model_Request $request
     * @param boolean $trimApiTypePath OPTIONAL If TRUE - /api/:api_type part of request path info will be trimmed
     * @return Mage_Api2_Model_Router
     * @throws Mage_Api2_Exception
     */
    public function routeApiType(Mage_Api2_Model_Request $request, $trimApiTypePath = true)
    {
        /** @var $apiTypeRoute Mage_Api2_Model_Route_ApiType */
        $apiTypeRoute = Mage::getModel('Mage_Api2_Model_Route_ApiType');

        if (!($apiTypeMatch = $apiTypeRoute->match($request, true))) {
            throw new Mage_Api2_Exception('Request does not match type route.', Mage_Api2_Model_Server::HTTP_NOT_FOUND);
        }
        // Trim matched URI path for next routes
        if ($trimApiTypePath) {
            $matchedPathLength = strlen('/' . ltrim($apiTypeRoute->getMatchedPath(), '/'));

            $request->setPathInfo(substr($request->getPathInfo(), $matchedPathLength));
        }
        $request->setParam('api_type', $apiTypeMatch['api_type']);

        return $this;
    }
}
