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
     * @var \Magento\App\RouterList
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
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var ActionInterface
     */
    protected $_action;

    /**
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\App\ResponseInterface $response
     * @param RouterList $routerList
     * @param array $data
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\App\ResponseInterface $response,
        RouterList $routerList,
        array $data = array()
    ) {
        $this->_eventManager = $eventManager;
        $this->_routerList = $routerList;
        $this->_response = $response;
    }

    /**
     * Set Default Value
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setDefault($key, $value = null)
    {
        if (is_array($key)) {
            $this->_defaults = $key;
        } else {
            $this->_defaults[$key] = $value;
        }
        return $this;
    }

    /**
     * Retrieve default value
     *
     * @param string $key
     * @return mixed
     */
    public function getDefault($key=null)
    {
        if (is_null($key)) {
            return $this->_defaults;
        } elseif (isset($this->_defaults[$key])) {
            return $this->_defaults[$key];
        }
        return false;
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
     * @throws \LogicException
     */
    public function dispatch(RequestInterface $request)
    {
        $this->_request = $request;
        \Magento\Profiler::start('routers_match');
        $routingCycleCounter = 0;
        while (!$request->isDispatched() && $routingCycleCounter++ < 100) {
            /** @var $router \Magento\App\Router\AbstractRouter */
            foreach ($this->_routerList->getRouters() as $router) {
                $router->setFront($this);

                /** @var $controllerInstance \Magento\App\ActionInterface */
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
        // This event gives possibility to launch something before sending output (allow cookie setting)
        $this->_eventManager->dispatch('controller_front_send_response_before', array('front' => $this));
        \Magento\Profiler::start('send_response');
        $this->_eventManager->dispatch('http_response_send_before', array('response' => $this));
        $this->getResponse()->sendResponse();
        \Magento\Profiler::stop('send_response');
        $this->_eventManager->dispatch('controller_front_send_response_after', array('front' => $this));
    }
}
