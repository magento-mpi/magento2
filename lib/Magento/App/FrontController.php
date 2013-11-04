<?php
/**
 * Front controller responsible for dispatcing application requests
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

class FrontController implements FrontControllerInterface
{
    /**
     * @var array
     */
    protected $_defaults = array();

    /**
     * @var \Magento\App\RouterInterface[]
     */
    protected $_routerList;

    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var ActionInterface
     */
    protected $_action;

    /**
     * @param \Magento\App\ResponseInterface $response
     * @param RouterList $routerList
     * @param array $data
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        \Magento\App\ResponseInterface $response,
        RouterList $routerList,
        array $data = array()
    ) {
        $this->_routerList = $routerList;
        $this->_response = $response;
    }

    /**
     * Retrieve request object
     *
     * @return \Magento\App\RequestInterface
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Retrieve response object
     *
     * @return \Magento\App\ResponseInterface
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Seta application action
     *
     * @param ActionInterface $action
     */
    public function setAction(ActionInterface $action)
    {
        $this->_action = $action;
    }

    /**
     * @return ActionInterface
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws \LogicException
     */
    public function dispatch(RequestInterface $request)
    {
        $this->_request = $request;
        \Magento\Profiler::start('routers_match');
        $routingCycleCounter = 0;
        while (!$request->isDispatched() && $routingCycleCounter++ < 100) {
            foreach ($this->_routerList as $router) {
                $controllerInstance = $router->match($this->getRequest());
                if ($controllerInstance) {
                    $controllerInstance->dispatch($request->getActionName());
                    break;
                }
            }
        }
        \Magento\Profiler::stop('routers_match');
        if ($routingCycleCounter > 100) {
            throw new \LogicException('Front controller reached 100 router match iterations');
        }
        return $this->getResponse();
    }
}
