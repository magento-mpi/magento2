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
 * Custom \Zend_Controller_Request_Http class (formally)
 *
 * Allows dispatching before and after events for each controller action
 */
namespace Magento\Core\Controller\Request;

class Http extends \Zend_Controller_Request_Http
{
    const DEFAULT_HTTP_PORT = 80;
    const DEFAULT_HTTPS_PORT = 443;

    /**
     * ORIGINAL_PATH_INFO
     * @var string
     */
    protected $_originalPathInfo= '';
    protected $_requestString   = '';

    /**
     * Path info array used before applying rewrite from config
     *
     * @var null || array
     */
    protected $_rewritedPathInfo= null;
    protected $_requestedRouteName = null;
    protected $_routingInfo = array();

    protected $_route;

    protected $_directFrontNames = null;
    protected $_controllerModule = null;

    /**
     * Streight request flag.
     * If flag is determined no additional logic is applicable
     *
     * @var $_isStraight bool
     */
    protected $_isStraight = false;

    /**
     * Request's original information before forward.
     *
     * @var array
     */
    protected $_beforeForwardInfo = array();

    /**
     * @var \Magento\Backend\Helper\DataProxy
     */
    protected $_helper;

    /**
     * @param \Magento\Backend\Helper\DataProxy $helper
     * @param null $uri
     */
    public function __construct(\Magento\Backend\Helper\DataProxy $helper, $uri = null)
    {
        $this->_helper = $helper;
        parent::__construct($uri);
    }

    /**
     * Returns ORIGINAL_PATH_INFO.
     * This value is calculated instead of reading PATH_INFO
     * directly from $_SERVER due to cross-platform differences.
     *
     * @return string
     */
    public function getOriginalPathInfo()
    {
        if (empty($this->_originalPathInfo)) {
            $this->setPathInfo();
        }
        return $this->_originalPathInfo;
    }

    /**
     * Set the PATH_INFO string
     * Set the ORIGINAL_PATH_INFO string
     *
     * @param string|null $pathInfo
     * @return \Zend_Controller_Request_Http
     */
    public function setPathInfo($pathInfo = null)
    {
        if ($pathInfo === null) {
            $requestUri = $this->getRequestUri();
            if (null === $requestUri) {
                return $this;
            }

            // Remove the query string from REQUEST_URI
            $pos = strpos($requestUri, '?');
            if ($pos) {
                $requestUri = substr($requestUri, 0, $pos);
            }

            $baseUrl = $this->getBaseUrl();
            $pathInfo = substr($requestUri, strlen($baseUrl));
            if ((null !== $baseUrl) && (false === $pathInfo)) {
                $pathInfo = '';
            } elseif (null === $baseUrl) {
                $pathInfo = $requestUri;
            }

            $pathParts = explode('/', ltrim($pathInfo, '/'), 2);
            $storeCode = $pathParts[0];

            if ($this->_isFrontArea($storeCode)) {
                $stores = \Mage::app()->getStores(true, true);
                if (isset($stores[$storeCode]) && $stores[$storeCode]->isUseStoreInUrl()) {
                    if (!$this->isDirectAccessFrontendName($storeCode)) {
                        \Mage::app()->setCurrentStore($storeCode);
                        $pathInfo = '/'.(isset($pathParts[1]) ? $pathParts[1] : '');
                    } elseif (!empty($storeCode)) {
                        $this->setActionName('noRoute');
                    }
                }
            }

            $this->_originalPathInfo = (string)$pathInfo;

            $this->_requestString = $pathInfo . ($pos !== false ? substr($requestUri, $pos) : '');
        }

        $this->_pathInfo = (string)$pathInfo;
        return $this;
    }

    /**
     * Check area by store code
     *
     * @param string $storeCode
     * @return boolean
     */
    protected function _isFrontArea($storeCode)
    {
        return $storeCode != $this->_helper->getAreaFrontName();
    }

    /**
     * Specify new path info
     * It happen when occur rewrite based on configuration
     *
     * @param   string $pathInfo
     * @return  \Magento\Core\Controller\Request\Http
     */
    public function rewritePathInfo($pathInfo)
    {
        if (($pathInfo != $this->getPathInfo()) && ($this->_rewritedPathInfo === null)) {
            $this->_rewritedPathInfo = explode('/', trim($this->getPathInfo(), '/'));
        }
        $this->setPathInfo($pathInfo);
        return $this;
    }

    /**
     * Check if code declared as direct access frontend name
     * this mean what this url can be used without store code
     *
     * @param   string $code
     * @return  bool
     */
    public function isDirectAccessFrontendName($code)
    {
        $names = $this->getDirectFrontNames();
        return isset($names[$code]);
    }

    /**
     * Get list of front names available with access without store code
     *
     * @return array
     */
    public function getDirectFrontNames()
    {
        return $this->_directFrontNames;
    }

    /**
     * Get request string
     *
     * @return string
     */
    public function getRequestString()
    {
        return $this->_requestString;
    }

    /**
     * Get base path
     *
     * @return string
     */
    public function getBasePath()
    {
        $path = parent::getBasePath();
        if (empty($path)) {
            $path = '/';
        } else {
            $path = str_replace('\\', '/', $path);
        }
        return $path;
    }

    /**
     * Get base url
     *
     * @return string
     */
    public function getBaseUrl()
    {
        $url = parent::getBaseUrl();
        $url = str_replace('\\', '/', $url);
        return $url;
    }

    /**
     * Set route name
     *
     * @param string $route
     * @return $this
     */
    public function setRouteName($route)
    {
        $this->_route = $route;
        $router = \Mage::app()->getFrontController()->getRouterList()->getRouterByRoute($route);
        if (!$router) {
            return $this;
        }
        $module = $router->getFrontNameByRoute($route);
        if ($module) {
            $this->setModuleName($module);
        }
        return $this;
    }

    public function getRouteName()
    {
        return $this->_route;
    }

    /**
     * Retrieve HTTP HOST
     *
     * @param bool $trimPort
     * @return string
     */
    public function getHttpHost($trimPort = true)
    {
        if (!isset($_SERVER['HTTP_HOST'])) {
            return false;
        }
        if ($trimPort) {
            $host = explode(':', $_SERVER['HTTP_HOST']);
            return $host[0];
        }
        return $_SERVER['HTTP_HOST'];
    }

    /**
     * Set a member of the $_POST superglobal
     *
     * @param string|array $key
     * @param mixed $value
     *
     * @return \Magento\Core\Controller\Request\Http
     */
    public function setPost($key, $value = null)
    {
        if (is_array($key)) {
            $_POST = $key;
        } else {
            $_POST[$key] = $value;
        }
        return $this;
    }

    /**
     * Specify module name where was found currently used controller
     *
     * @param   string $module
     * @return  \Magento\Core\Controller\Request\Http
     */
    public function setControllerModule($module)
    {
        $this->_controllerModule = $module;
        return $this;
    }

    /**
     * Get module name of currently used controller
     *
     * @return  string
     */
    public function getControllerModule()
    {
        return $this->_controllerModule;
    }

    /**
     * Retrieve the module name
     *
     * @return string
     */
    public function getModuleName()
    {
        return $this->_module;
    }

    /**
     * Retrieve the controller name
     *
     * @return string
     */
    public function getControllerName()
    {
        return $this->_controller;
    }

    /**
     * Retrieve the action name
     *
     * @return string
     */
    public function getActionName()
    {
        return $this->_action;
    }

    /**
     * Retrieve an alias
     *
     * Retrieve the actual key represented by the alias $name.
     *
     * @param string $name
     * @return string|null Returns null when no alias exists
     */
    public function getAlias($name)
    {
        $aliases = $this->getAliases();
        if (isset($aliases[$name])) {
            return $aliases[$name];
        }
        return null;
    }

    /**
     * Retrieve the list of all aliases
     *
     * @return array
     */
    public function getAliases()
    {
        if (isset($this->_routingInfo['aliases'])) {
            return $this->_routingInfo['aliases'];
        }
        return parent::getAliases();
    }

    /**
     * Get route name used in request (ignore rewrite)
     *
     * @return string
     */
    public function getRequestedRouteName()
    {
        if (isset($this->_routingInfo['requested_route'])) {
            return $this->_routingInfo['requested_route'];
        }
        if ($this->_requestedRouteName === null) {
            if ($this->_rewritedPathInfo !== null && isset($this->_rewritedPathInfo[0])) {
                $frontName = $this->_rewritedPathInfo[0];
                $router = \Mage::app()->getFrontController()->getRouterList()->getRouterByFrontName($frontName);
                $this->_requestedRouteName = $router->getRouteByFrontName($frontName);
            } else {
                // no rewritten path found, use default route name
                return $this->getRouteName();
            }
        }
        return $this->_requestedRouteName;
    }

    /**
     * Get controller name used in request (ignore rewrite)
     *
     * @return string
     */
    public function getRequestedControllerName()
    {
        if (isset($this->_routingInfo['requested_controller'])) {
            return $this->_routingInfo['requested_controller'];
        }
        if (($this->_rewritedPathInfo !== null) && isset($this->_rewritedPathInfo[1])) {
            return $this->_rewritedPathInfo[1];
        }
        return $this->getControllerName();
    }

    /**
     * Get action name used in request (ignore rewrite)
     *
     * @return string
     */
    public function getRequestedActionName()
    {
        if (isset($this->_routingInfo['requested_action'])) {
            return $this->_routingInfo['requested_action'];
        }
        if (($this->_rewritedPathInfo !== null) && isset($this->_rewritedPathInfo[2])) {
            return $this->_rewritedPathInfo[2];
        }
        return $this->getActionName();
    }

    /**
     * Set routing info data
     *
     * @param array $data
     * @return \Magento\Core\Controller\Request\Http
     */
    public function setRoutingInfo($data)
    {
        if (is_array($data)) {
            $this->_routingInfo = $data;
        }
        return $this;
    }

    /**
     * Collect properties changed by _forward in protected storage
     * before _forward was called first time.
     *
     * @return \Magento\Core\Controller\Varien\Action
     */
    public function initForward()
    {
        if (empty($this->_beforeForwardInfo)) {
            $this->_beforeForwardInfo = array(
                'params' => $this->getParams(),
                'action_name' => $this->getActionName(),
                'controller_name' => $this->getControllerName(),
                'module_name' => $this->getModuleName(),
                'route_name' => $this->getRouteName(),
            );
        }

        return $this;
    }

    /**
     * Retrieve property's value which was before _forward call.
     * If property was not changed during _forward call null will be returned.
     * If passed name will be null whole state array will be returned.
     *
     * @param string $name
     * @return array|string|null
     */
    public function getBeforeForwardInfo($name = null)
    {
        if (is_null($name)) {
            return $this->_beforeForwardInfo;
        } elseif (isset($this->_beforeForwardInfo[$name])) {
            return $this->_beforeForwardInfo[$name];
        }

        return null;
    }

    /**
     * Specify/get _isStraight flag value
     *
     * @param bool $flag
     * @return bool
     */
    public function isStraight($flag = null)
    {
        if ($flag !== null) {
            $this->_isStraight = $flag;
        }
        return $this->_isStraight;
    }

    /**
     * Check is Request from AJAX
     *
     * @return boolean
     */
    public function isAjax()
    {
        if ($this->isXmlHttpRequest()) {
            return true;
        }
        if ($this->getParam('ajax') || $this->getParam('isAjax')) {
            return true;
        }
        return false;
    }

    /**
     * Retrieve a member of the $_FILES super global
     *
     * If no $key is passed, returns the entire $_FILES array.
     *
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed
     */
    public function getFiles($key = null, $default = null)
    {
        if (null === $key) {
            return $_FILES;
        }

        return (isset($_FILES[$key])) ? $_FILES[$key] : $default;
    }

    /**
     * Get website instance base url
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getDistroBaseUrl()
    {
        if (isset($_SERVER['SCRIPT_NAME']) && isset($_SERVER['HTTP_HOST'])) {
            $secure = (!empty($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] != 'off'))
                || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443');
            $scheme = ($secure ? 'https' : 'http') . '://' ;

            $hostArr = explode(':', $_SERVER['HTTP_HOST']);
            $host = $hostArr[0];
            $port = isset($hostArr[1]) && (!$secure && $hostArr[1] != 80 || $secure && $hostArr[1] != 443)
                ? ':'. $hostArr[1]
                : '';
            $path = $this->getBasePath();

            return $scheme . $host . $port . rtrim($path, '/') . '/';
        }
        return 'http://localhost/';
    }
}
