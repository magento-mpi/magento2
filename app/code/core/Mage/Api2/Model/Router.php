<?php

/**
 * @method array getRoutes()
 * @method setRoutes()
 * @throws Mage_Api2_Exception
 *
 */
class Mage_Api2_Model_Router extends Varien_Object //Zend_Controller_Router_Abstract
{
    /**
     * Route the Request, the only responsibility of the class
     * Find route that match current URL, set parameters of the route to Request
     *
     * @param Mage_Api2_Model_Request $request
     * @return Mage_Api2_Model_Request
     * @throws Mage_Api2_Exception
     */
    public function route(Mage_Api2_Model_Request $request)
    {
        $isMatched = false;
        /** @var $route Mage_Api2_Model_Route_Interface */
        foreach ($this->getRoutes() as $route) {        //set in Mage_Api2_Model_Server::dispatch()
            if ($params = $route->match($request)) {
                $this->setRequestParams($request, $params);
                $isMatched = true;
                break;
            }
        }

        if (!$isMatched) {
            throw new Mage_Api2_Exception(sprintf('Request not matched any route.'), 404);
        }
    
        return $request;
    }

    /**
     * @param Mage_Api2_Model_Request $request
     * @param $params
     * @throws Mage_Api2_Exception
     */
    protected function setRequestParams(Mage_Api2_Model_Request $request, $params)
    {
        if (!isset($params['type']) || !isset($params['model'])) {
            throw new Mage_Api2_Exception("Matched resource is not properly set.", 500);
        }

        foreach ($params as $param=>$value) {
            $request->setParam($param, $value);
        }
    }
}
