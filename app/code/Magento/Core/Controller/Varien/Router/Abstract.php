<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract router class
 */
abstract class Magento_Core_Controller_Varien_Router_Abstract
{
    /**
     * @var Magento_Core_Controller_Varien_Front
     */
    protected $_front;

    /**
     * @var Magento_Core_Controller_Varien_Action_Factory
     */
    protected $_controllerFactory;

    /**
     * @param Magento_Core_Controller_Varien_Action_Factory $controllerFactory
     */
    public function __construct(Magento_Core_Controller_Varien_Action_Factory $controllerFactory)
    {
        $this->_controllerFactory = $controllerFactory;
    }

    /**
     * Assign front controller instance
     *
     * @param $front Magento_Core_Controller_Varien_Front
     * @return Magento_Core_Controller_Varien_Router_Abstract
     */
    public function setFront(Magento_Core_Controller_Varien_Front $front)
    {
        $this->_front = $front;
        return $this;
    }

    /**
     * Retrieve front controller instance
     *
     * @return Magento_Core_Controller_Varien_Front
     */
    public function getFront()
    {
        return $this->_front;
    }

    /**
     * Retrieve front name by route
     *
     * @param string $routeId
     * @return string
     */
    public function getFrontNameByRoute($routeId)
    {
        return $routeId;
    }

    /**
     * Retrieve route by module front name
     *
     * @param string $frontName
     * @return string
     */
    public function getRouteByFrontName($frontName)
    {
        return $frontName;
    }

    /**
     * Match controller by request
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return Mage_Core_Controller_Varien_Action
     */
    abstract public function match(Mage_Core_Controller_Request_Http $request);
}
