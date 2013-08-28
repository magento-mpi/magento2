<?php
/**
 * Default no route handler
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Router_NoRouteHandler implements Mage_Core_Model_Router_NoRouteHandlerInterface
{
    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @param Mage_Core_Model_Config $config
     */
    public function __construct(Mage_Core_Model_Config $config)
    {
        $this->_config = $config;
    }

    /**
     * Check and process no route request
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return bool
     */
    public function process(Mage_Core_Controller_Request_Http $request)
    {
        $noRoute        = explode('/', $this->_config->getNode('web/default/no_route'));

        $moduleName     = isset($noRoute[0]) ? $noRoute[0] : 'core';
        $controllerName = isset($noRoute[1]) ? $noRoute[1] : 'index';
        $actionName     = isset($noRoute[2]) ? $noRoute[2] : 'index';

        $request->setModuleName($moduleName)
            ->setControllerName($controllerName)
            ->setActionName($actionName);

        return true;
    }
}
