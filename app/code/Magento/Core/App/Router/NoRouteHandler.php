<?php
/**
 * Default no route handler
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\App\Router;

class NoRouteHandler implements \Magento\App\Router\NoRouteHandlerInterface
{
    /**
     * @var \Magento\App\ConfigInterface
     */
    protected $_config;

    /**
     * @param \Magento\App\ConfigInterface $config
     */
    public function __construct(\Magento\App\ConfigInterface $config)
    {
        $this->_config = $config;
    }

    /**
     * Check and process no route request
     *
     * @param \Magento\App\RequestInterface $request
     * @return bool
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function process(\Magento\App\RequestInterface $request)
    {
        $noRoutePath = $this->_config->getValue('web/default/no_route', 'default');

        if ($noRoutePath) {
            $noRoute = explode('/', $noRoutePath);
        } else {
            $noRoute = array();
        }

        $moduleName     = isset($noRoute[0]) ? $noRoute[0] : 'core';
        $controllerName = isset($noRoute[1]) ? $noRoute[1] : 'index';
        $actionName     = isset($noRoute[2]) ? $noRoute[2] : 'index';

        $request->setModuleName($moduleName)
            ->setControllerName($controllerName)
            ->setActionName($actionName);

        return true;
    }
}
