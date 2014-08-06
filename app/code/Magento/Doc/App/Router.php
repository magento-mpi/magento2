<?php
/**
 * Doc Area router
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */
namespace Magento\Doc\App;

use Magento\Core\App\Router\Base;

/**
 * Class Router
 * @package Magento\Doc\App
 */
class Router extends Base
{
    /**
     * List of required request parameters in doc area
     * Order sensitive
     * @var string[]
     */
    protected $_requiredParams = ['moduleFrontName'];

    /**
     * Parse request URL params
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return array
     */
    protected function parseRequest(\Magento\Framework\App\RequestInterface $request)
    {
        $output = [];

        $path = trim($request->getPathInfo(), '/');

        $params = explode('/', $path ? $path : $this->_getDefaultPath());
        foreach ($this->_requiredParams as $paramName) {
            $output[$paramName] = array_shift($params);
        }

        if ($params) {
            $schemeParts = [];
            $schemeParts[] = array_shift($params); // package
            $schemeParts[] = array_shift($params); // document name
            for ($i = 0,$l = sizeof($params); $i < $l; $i += 2) {
                $output['variables'][$params[$i]] = isset($params[$i + 1]) ? urldecode($params[$i + 1]) : '';
            }
            $output['variables']['doc_scheme'] = implode('/', $schemeParts);
        }

        return $output;
    }

    /**
     * Create matched controller instance
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @param array $params
     * @return \Magento\Framework\App\Action\Action|null
     */
    protected function matchAction(\Magento\Framework\App\RequestInterface $request, array $params)
    {
        $moduleFrontName = $this->matchModuleFrontName($request, $params['moduleFrontName']);
        if (empty($moduleFrontName)) {
            return null;
        }

        /**
         * Searching router args by module name from route using it as key
         */
        $modules = $this->_routeConfig->getModulesByFrontName($moduleFrontName);
        if (empty($modules) === true) {
            return null;
        }

        /**
         * Going through modules to find appropriate controller
         */
        $currentModuleName = null;
        $actionPath = null;
        $action = null;
        $actionInstance = null;

        $request->setRouteName($this->_routeConfig->getRouteByFrontName($moduleFrontName));
        $actionPath = 'index';
        $action = $request->isPost() ? 'write' : 'read';
        $this->_checkShouldBeSecure($request, '/' . $moduleFrontName . '/' . $actionPath . '/' . $action);

        $currentModuleName = 'Magento_Doc';

        $actionClassName = $this->actionList->get('Magento_Doc', $this->pathPrefix, $actionPath, $action);
        if (!$actionClassName || !is_subclass_of($actionClassName, $this->actionInterface)) {
            return null;
        }

        $actionInstance = $this->actionFactory->create($actionClassName, array('request' => $request));

        if (null == $actionInstance) {
            $actionInstance = $this->getNotFoundAction($currentModuleName, $request);
            if (is_null($actionInstance)) {
                return null;
            }
            $action = 'noroute';
        }

        // set values only after all the checks are done
        $request->setModuleName($moduleFrontName);
        $request->setControllerName($actionPath);
        $request->setActionName($action);
        $request->setControllerModule($currentModuleName);
        if (isset($params['variables'])) {
            $request->setParams($params['variables']);
        }
        return $actionInstance;
    }
}
