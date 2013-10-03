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
namespace Magento\Core\Controller\Varien\Router;

abstract class AbstractRouter
{
    /**
     * @var \Magento\Core\Controller\Varien\Front
     */
    protected $_front;

    /**
     * @var \Magento\Core\Controller\Varien\Action\Factory
     */
    protected $_controllerFactory;

    /**
     * @param \Magento\Core\Controller\Varien\Action\Factory $controllerFactory
     */
    public function __construct(\Magento\Core\Controller\Varien\Action\Factory $controllerFactory)
    {
        $this->_controllerFactory = $controllerFactory;
    }

    /**
     * Assign front controller instance
     *
     * @param $front \Magento\Core\Controller\Varien\Front
     * @return \Magento\Core\Controller\Varien\Router\AbstractRouter
     */
    public function setFront(\Magento\Core\Controller\Varien\Front $front)
    {
        $this->_front = $front;
        return $this;
    }

    /**
     * Retrieve front controller instance
     *
     * @return \Magento\Core\Controller\Varien\Front
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
     * @param \Magento\Core\Controller\Request\Http $request
     * @return \Magento\Core\Controller\Varien\Action
     */
    abstract public function match(\Magento\Core\Controller\Request\Http $request);
}
