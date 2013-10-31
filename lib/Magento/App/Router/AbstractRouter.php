<?php
/**
 * Abstract application router
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Router;

use \Magento\App\FrontControllerInterface,
    \Magento\App\ActionFactory,
    \Magento\App\RequestInterface;

abstract class AbstractRouter
{
    /**
     * @var \Magento\App\FrontController
     */
    protected $_front;

    /**
     * @var \Magento\App\ActionFactory
     */
    protected $_controllerFactory;

    /**
     * @param \Magento\App\ActionFactory $controllerFactory
     */
    public function __construct(ActionFactory $controllerFactory)
    {
        $this->_controllerFactory = $controllerFactory;
    }

    /**
     * Assign front controller instance
     *
     * @param $front FrontControllerInterface
     * @return AbstractRouter
     */
    public function setFront(FrontControllerInterface $front)
    {
        $this->_front = $front;
        return $this;
    }

    /**
     * Retrieve front controller instance
     *
     * @return FrontControllerInterface
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
     * @param RequestInterface $request
     * @return \Magento\App\Action\AbstractAction
     */
    abstract public function match(RequestInterface $request);

    /**
     * Get area code, detected or used by router
     *
     * @return null|string
     */
    public function getAreaCode()
    {
        return null;
    }
}
