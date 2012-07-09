<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Controller_Varien_Router_Base extends Mage_Core_Controller_Varien_Router_Abstract
{
    protected $_modules = array();
    protected $_routes = array();
    protected $_dispatchData = array();

    /**
     * Current router belongs to the area
     *
     * @var string
     */
    protected $_area;

    /**
     * Base controller that belongs to area
     *
     * @var string
     */
    protected $_baseController = null;

    public function __construct(array $options = array())
    {
        $this->_area           = null;
        $this->_baseController = null;

        if (isset($options['area'])) {
            $this->_area = $options['area'];
        }

        if (isset($options['base_controller'])) {
            $this->_baseController = $options['base_controller'];
        }

        if (is_null($this->_area) || is_null($this->_baseController)) {
            Mage::throwException("Not enough options to initialize router.");
        }
    }

    public function collectRoutes($configArea, $useRouterName)
    {
        $routers = array();
        $routersConfigNode = Mage::getConfig()->getNode($configArea.'/routers');
        if($routersConfigNode) {
            $routers = $routersConfigNode->children();
        }
        foreach ($routers as $routerName=>$routerConfig) {
            $use = (string)$routerConfig->use;
            if ($use == $useRouterName) {
                $modules = array((string)$routerConfig->args->module);
                if ($routerConfig->args->modules) {
                    foreach ($routerConfig->args->modules->children() as $customModule) {
                        if ($customModule) {
                            if ($before = $customModule->getAttribute('before')) {
                                $position = array_search($before, $modules);
                                if ($position === false) {
                                    $position = 0;
                                }
                                array_splice($modules, $position, 0, (string)$customModule);
                            } elseif ($after = $customModule->getAttribute('after')) {
                                $position = array_search($after, $modules);
                                if ($position === false) {
                                    $position = count($modules);
                                }
                                array_splice($modules, $position+1, 0, (string)$customModule);
                            } else {
                                $modules[] = (string)$customModule;
                            }
                        }
                    }
                }

                $frontName = (string)$routerConfig->args->frontName;
                $this->addModule($frontName, $modules, $routerName);
            }
        }
    }

    public function fetchDefault()
    {
        $this->getFront()->setDefault(array(
            'module' => 'core',
            'controller' => 'index',
            'action' => 'index'
        ));
    }

    /**
     * checking if this admin if yes then we don't use this router
     *
     * @return bool
     */
    protected function _beforeModuleMatch()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return false;
        }
        return true;
    }

    /**
     * dummy call to pass through checking
     *
     * @return bool
     */
    protected function _afterModuleMatch()
    {
        return true;
    }

    public function match(Zend_Controller_Request_Http $request)
    {

        //checking before even try to find out that current module
        //should use this router
        if (!$this->_beforeModuleMatch()) {
            return false;
        }

        $params = $this->_parseRequest($request);
        return $this->_createControllerInstance($request, $params);
    }

    protected function _parseRequest(Zend_Controller_Request_Http $request)
    {
        $this->fetchDefault();

        $path = trim($request->getPathInfo(), '/');

        if ($path) {
            $params = explode('/', $path);
        } else {
            $params = explode('/', $this->_getDefaultPath());
        }
        return $params;
    }

    protected function _createControllerInstance(Zend_Controller_Request_Http $request, array $params)
    {
        $front = $this->getFront();

        // get module name
        if ($request->getModuleName()) {
            $moduleFrontName = $request->getModuleName();
        } else {
            if (!empty($params[0])) {
                $moduleFrontName = $params[0];
            } else {
                $moduleFrontName = $this->getFront()->getDefault('module');
                $request->setAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS, '');
            }
        }
        if (!$moduleFrontName) {
            return null;
        }

        /**
         * Searching router args by module name from route using it as key
         */
        $modules = $this->getModulesByFrontName($moduleFrontName);

        if (empty($modules) === true) {
            return null;
        }

        // checks after we found out that this router should be used for current module
        if (!$this->_afterModuleMatch()) {
            return null;
        }

        /**
         * Going through modules to find appropriate controller
         */
        $found = false;
        $currentModuleName = null;
        foreach ($modules as $moduleName) {
            $currentModuleName = $moduleName;
            $request->setRouteName($this->getRouteByFrontName($moduleFrontName));

            // get controller name
            if ($request->getControllerName()) {
                $controller = $request->getControllerName();
            } else {
                if (!empty($params[1])) {
                    $controller = $params[1];
                } else {
                    $controller = $front->getDefault('controller');
                    $request->setAlias(
                        Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                        ltrim($request->getOriginalPathInfo(), '/')
                    );
                }
            }

            // get action name
            if (empty($action)) {
                if ($request->getActionName()) {
                    $action = $request->getActionName();
                } else {
                    $action = !empty($params[2]) ? $params[2] : $front->getDefault('action');
                }
            }

            //checking if this place should be secure
            $this->_checkShouldBeSecure($request, '/'.$moduleFrontName.'/'.$controller.'/'.$action);

            $controllerClassName = $this->_validateControllerClassName($moduleName, $controller);
            if (!$controllerClassName) {
                continue;
            }

            // instantiate controller class
            $controllerInstance = Mage::getControllerInstance($controllerClassName, $request, $front->getResponse());

            // instantiated controller must be a child of $this->_baseController
            if (!($controllerInstance instanceof $this->_baseController)) {
                continue;
            }

            if (!$controllerInstance->hasAction($action)) {
                continue;
            }

            $found = true;
            break;
        }

        /**
         * if we did not found any suitable
         */
        if (!$found) {
            if ($this->_noRouteShouldBeApplied()) {
                $controller = 'index';
                $action = 'noroute';

                $controllerClassName = $this->_validateControllerClassName($currentModuleName, $controller);
                if (!$controllerClassName) {
                    return false;
                }

                // instantiate controller class
                $controllerInstance = Mage::getControllerInstance($controllerClassName, $request,
                    $front->getResponse());

                if (!$controllerInstance->hasAction($action)) {
                    return false;
                }
            } else {
                return false;
            }
        }

        // set values only after all the checks are done
        $request->setModuleName($moduleFrontName);
        $request->setControllerName($controller);
        $request->setActionName($action);
        $request->setControllerModule($currentModuleName);

        // set parameters from pathinfo
        for ($i = 3, $l = sizeof($params); $i < $l; $i += 2) {
            $request->setParam($params[$i], isset($params[$i+1]) ? urldecode($params[$i+1]) : '');
        }
    }

    /**
     * Get router default request path
     * @return string
     */
    protected function _getDefaultPath()
    {
        return Mage::getStoreConfig('web/default/front');
    }

    /**
     * Allow to control if we need to enable no route functionality in current router
     *
     * @return bool
     */
    protected function _noRouteShouldBeApplied()
    {
        return false;
    }

    /**
     * Generating and validating class file name,
     * class and if evrything ok do include if needed and return of class name
     *
     * @return mixed
     */
    protected function _validateControllerClassName($realModule, $controller)
    {
        $controllerFileName = $this->getControllerFileName($realModule, $controller);
        if (!$this->validateControllerFileName($controllerFileName)) {
            return false;
        }

        $controllerClassName = $this->getControllerClassName($realModule, $controller);
        if (!$controllerClassName) {
            return false;
        }

        // include controller file if needed
        if (!$this->_includeControllerClass($controllerFileName, $controllerClassName)) {
            return false;
        }

        return $controllerClassName;
    }

    /**
     * Include the file containing controller class if this class is not defined yet
     *
     * @param string $controllerFileName
     * @param string $controllerClassName
     * @return bool
     */
    protected function _includeControllerClass($controllerFileName, $controllerClassName)
    {
        if (!class_exists($controllerClassName, false)) {
            if (!file_exists($controllerFileName)) {
                return false;
            }
            include $controllerFileName;

            if (!class_exists($controllerClassName, false)) {
                throw Mage::exception('Mage_Core', Mage::helper('Mage_Core_Helper_Data')->__('Controller file was loaded but class does not exist'));
            }
        }
        return true;
    }

    public function addModule($frontName, $moduleName, $routeName)
    {
        $this->_modules[$frontName] = $moduleName;
        $this->_routes[$routeName] = $frontName;
        return $this;
    }

    /**
     * Retrieve list of modules subscribed to given frontName
     *
     * @param string $frontName
     * @return array
     */
    public function getModulesByFrontName($frontName)
    {
        $modules = array();
        if (isset($this->_modules[$frontName])) {
            if (false === is_array($this->_modules[$frontName])) {
                $modules = array($this->_modules[$frontName]);
            } else {
                $modules = $this->_modules[$frontName];
            }
        }
        return $modules;
    }

    public function getModuleByName($moduleName, $modules)
    {
        foreach ($modules as $module) {
            if ($moduleName === $module || (is_array($module)
                    && $this->getModuleByName($moduleName, $module))) {
                return true;
            }
        }
        return false;
    }

    public function getFrontNameByRoute($routeName)
    {
        if (isset($this->_routes[$routeName])) {
            return $this->_routes[$routeName];
        }
        return false;
    }

    public function getRouteByFrontName($frontName)
    {
        return array_search($frontName, $this->_routes);
    }

    public function getControllerFileName($realModule, $controller)
    {
        $parts = explode('_', $realModule);
        $realModule = implode('_', array_splice($parts, 0, 2));
        $file = Mage::getModuleDir('controllers', $realModule);
        if (count($parts)) {
            $file .= DS . implode(DS, $parts);
        }
        $file .= DS.uc_words($controller, DS).'Controller.php';
        return $file;
    }

    public function validateControllerFileName($fileName)
    {
        if ($fileName && is_readable($fileName) && false===strpos($fileName, '//')) {
            return true;
        }
        return false;
    }

    public function getControllerClassName($realModule, $controller)
    {
        $class = $realModule.'_'.uc_words($controller).'Controller';
        return $class;
    }

    public function rewrite(array $p)
    {
        $rewrite = Mage::getConfig()->getNode('global/rewrite');
        if ($module = $rewrite->{$p[0]}) {
            if (!$module->children()) {
                $p[0] = trim((string)$module);
            }
        }
        if (isset($p[1]) && ($controller = $rewrite->{$p[0]}->{$p[1]})) {
            if (!$controller->children()) {
                $p[1] = trim((string)$controller);
            }
        }
        if (isset($p[2]) && ($action = $rewrite->{$p[0]}->{$p[1]}->{$p[2]})) {
            if (!$action->children()) {
                $p[2] = trim((string)$action);
            }
        }

        return $p;
    }

    /**
     * Check that request uses https protocol if it should.
     * Function redirects user to correct URL if needed.
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @param string $path
     * @return void
     */
    protected function _checkShouldBeSecure($request, $path = '')
    {
        if (!Mage::isInstalled() || $request->getPost()) {
            return;
        }

        if ($this->_shouldBeSecure($path) && !$request->isSecure()) {
            $url = $this->_getCurrentSecureUrl($request);
            if ($request->getRouteName() != 'adminhtml' && Mage::app()->getUseSessionInUrl()) {
                $url = Mage::getSingleton('Mage_Core_Model_Url')->getRedirectUrl($url);
            }

            Mage::app()->getFrontController()->getResponse()
                ->setRedirect($url)
                ->sendResponse();
            exit;
        }
    }

    protected function _getCurrentSecureUrl($request)
    {
        if ($alias = $request->getAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS)) {
            return Mage::getBaseUrl('link', true).ltrim($alias, '/');
        }

        return Mage::getBaseUrl('link', true).ltrim($request->getPathInfo(), '/');
    }

    /**
     * Check whether URL for corresponding path should use https protocol
     *
     * @param string $path
     * @return bool
     */
    protected function _shouldBeSecure($path)
    {
        return substr(Mage::getStoreConfig('web/unsecure/base_url'), 0, 5) === 'https'
            || Mage::getStoreConfigFlag('web/secure/use_in_frontend')
                && substr(Mage::getStoreConfig('web/secure/base_url'), 0, 5) == 'https'
                && Mage::getConfig()->shouldUrlBeSecure($path);
    }
}
