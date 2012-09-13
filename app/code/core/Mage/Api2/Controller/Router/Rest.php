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
class Mage_Api2_Controller_Router_Rest
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
     * @return Mage_Api2_Controller_Router_Rest
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
     * @return Mage_Api2_Controller_Router_Route_Rest
     */
    public function match(Mage_Api2_Model_Request $request)
    {
        /** @var $route Mage_Api2_Controller_Router_Route_Rest */
        foreach ($this->getRoutes() as $route) {
            $params = $route->match($request);
            if ($params !== false) {
                // TODO: Try to remove params set to $request
                $request->setParams($params);
                return $route;
            }
        }
        throw new Mage_Api2_Exception('Request does not match any route.', Mage_Api2_Controller_Front_Rest::HTTP_NOT_FOUND);
    }
}
