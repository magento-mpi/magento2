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
 * Webservice api2 dispatcher model
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Dispatcher
{
    /**
     * Template for retrieve resource class name
     */
    const RESOURCE_CLASS_TEMPLATE = ':resource_:api_:user_V:version';

    /**
     * API User object
     *
     * @var Mage_Api2_Model_Auth_User_Abstract
     */
    protected $_apiUser;

    /**
     * Dispatcher constructor
     *
     * @param Mage_Api2_Model_Auth_User_Abstract $apiUser
     */
    public function __construct(Mage_Api2_Model_Auth_User_Abstract $apiUser)
    {
        $this->setApiUser($apiUser);
    }

    /**
     * Instantiate resource class, set parameters to the instance, run resource internal dispatch method
     *
     * @param Mage_Api2_Model_Request $request
     * @param Mage_Api2_Model_Response $response
     * @return Mage_Api2_Model_Dispatcher
     * @throws Mage_Api2_Exception
     */
    public function dispatch(Mage_Api2_Model_Request $request, Mage_Api2_Model_Response $response)
    {
        $replace = array(
            ':resource' => $request->getParam('model'), // Set in Mage_Api2_Model_Router::_setRequestParams()
            ':api'      => ucfirst($request->getApiType()),
            ':user'     => ucfirst($this->_apiUser->getRole()),
            ':version'  => (int)$request->getVersion(),
        );
        $class = strtr(self::RESOURCE_CLASS_TEMPLATE, $replace);

        try {
            if (class_exists($class)) {
                /** @var $model Mage_Api2_Model_Resource */
                $model = new $class($request, $response);
                $model->dispatch();
            }
        } catch (Exception $e) {
            throw new Mage_Api2_Exception(sprintf('Invalid resource class "%s"', $class),
                Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        }

        return $this;
    }

    /**
     * Set API user object
     *
     * @param Mage_Api2_Model_Auth_User_Abstract $apiUser
     * @return Mage_Api2_Model_Dispatcher
     */
    public function setApiUser(Mage_Api2_Model_Auth_User_Abstract $apiUser)
    {
        $this->_apiUser = $apiUser;

        return $this;
    }
}
