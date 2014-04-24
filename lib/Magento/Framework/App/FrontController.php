<?php
/**
 * Front controller responsible for dispatching application requests
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App;

class FrontController implements FrontControllerInterface
{
    /**
     * @var RouterList
     */
    protected $_routerList;

    /**
     * @param RouterList $routerList
     */
    public function __construct(RouterList $routerList)
    {
        $this->_routerList = $routerList;
    }

    /**
     * Perform action and generate response
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws \LogicException
     */
    public function dispatch(RequestInterface $request)
    {
        \Magento\Framework\Profiler::start('routers_match');
        $routingCycleCounter = 0;
        $response = null;
        while (!$request->isDispatched() && $routingCycleCounter++ < 100) {
            foreach ($this->_routerList as $router) {
                try {
                    $actionInstance = $router->match($request);
                    if ($actionInstance) {
                        $request->setDispatched(true);
                        $actionInstance->getResponse()->setNoCacheHeaders();
                        $response = $actionInstance->dispatch($request);
                        break;
                    }
                } catch (Action\NotFoundException $e) {
                    $request->initForward();
                    $request->setActionName('noroute');
                    $request->setDispatched(false);
                    break;
                }
            }
        }
        \Magento\Framework\Profiler::stop('routers_match');
        if ($routingCycleCounter > 100) {
            throw new \LogicException('Front controller reached 100 router match iterations');
        }
        return $response;
    }
}
