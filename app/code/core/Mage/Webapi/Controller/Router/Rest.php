<?php
/**
 * Router for Magento web API.
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_Router_Rest
{
    /** @var array */
    protected $_routes = array();

    /** @var Mage_Core_Helper_Abstract */
    protected $_helper;

    /** @var Mage_Core_Model_Factory_Helper */
    protected $_helperFactory;

    /**
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     */
    public function __construct(Mage_Core_Model_Factory_Helper $helperFactory)
    {
        $this->_helperFactory = $helperFactory;
        $this->_helper = $this->_helperFactory->get('Mage_Webapi_Helper_Data');
    }

    /**
     * Set routes.
     *
     * @param array $routes
     * @return Mage_Webapi_Controller_Router_Rest
     */
    public function setRoutes(array $routes)
    {
        $this->_routes = $routes;
        return $this;
    }

    /**
     * Get routes.
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->_routes;
    }

    /**
     * Route the Request, the only responsibility of the class.
     * Find route that matches current URL, set parameters of the route to Request object.
     *
     * @param Mage_Webapi_Controller_Request $request
     * @return Mage_Webapi_Controller_Router_Route_Rest
     * @throws Mage_Webapi_Exception
     */
    public function match(Mage_Webapi_Controller_Request $request)
    {
        /** @var Mage_Webapi_Controller_Router_Route_Rest $route */
        foreach ($this->getRoutes() as $route) {
            $params = $route->match($request);
            if ($params !== false) {
                $request->setParams($params);
                return $route;
            }
        }
        throw new Mage_Webapi_Exception($this->_helper->__('Request does not match any route.'),
            Mage_Webapi_Exception::HTTP_NOT_FOUND);
    }
}
