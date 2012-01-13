<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Webservice api2 router model
 *
 * @method array getRoutes()
 * @method Mage_Api2_Model_Router setRoutes() setRoutes(array $routes)
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Router extends Varien_Object //Zend_Controller_Router_Abstract
{
    /**
     * Route the Request, the only responsibility of the class
     * Find route that match current URL, set parameters of the route to Request object
     *
     * @param Mage_Api2_Model_Request $request
     * @return Mage_Api2_Model_Request
     * @throws Mage_Api2_Exception
     */
    public function route(Mage_Api2_Model_Request $request)
    {
        $isMatched = false;
        /** @var $route Mage_Api2_Model_Route_Interface */
        foreach ($this->getRoutes() as $route) { //set in Mage_Api2_Model_Server::_route()
            if ($params = $route->match($request)) {
                $this->_setRequestParams($request, $params);
                $isMatched = true;
                break;
            }
        }

        if (!$isMatched) {
            throw new Mage_Api2_Exception(sprintf('Request not matched any route.'),
                Mage_Api2_Model_Server::HTTP_NOT_FOUND);
        }

        return $request;
    }

    /**
     * Set parameters of matched route to Request object
     *
     * @param Mage_Api2_Model_Request $request
     * @param array $params
     * @return Mage_Api2_Model_Router
     * @throws Mage_Api2_Exception
     */
    protected function _setRequestParams(Mage_Api2_Model_Request $request, $params)
    {
        if (!isset($params['type']) || !isset($params['model'])) {
            throw new Mage_Api2_Exception('Matched resource is not properly set.',
                Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        }

        foreach ($params as $param => $value) {
            $request->setParam($param, $value);
        }

        return $this;
    }
}
