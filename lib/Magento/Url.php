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
 * URL
 *
 * Properties:
 *
 * - request
 *
 * - relative_url: true, false
 * - type: 'link', 'skin', 'js', 'media'
 * - scope: instanceof \Magento\Url\ScopeInterface
 * - secure: true, false
 *
 * - scheme: 'http', 'https'
 * - user: 'user'
 * - password: 'password'
 * - host: 'localhost'
 * - port: 80, 443
 * - base_path: '/dev/magento/'
 * - base_script: 'index.php'
 *
 * - scopeview_path: 'scopeview/'
 * - route_path: 'module/controller/action/param1/value1/param2/value2'
 * - route_name: 'module'
 * - controller_name: 'controller'
 * - action_name: 'action'
 * - route_params: array('param1'=>'value1', 'param2'=>'value2')
 *
 * - query: (?)'param1=value1&param2=value2'
 * - query_array: array('param1'=>'value1', 'param2'=>'value2')
 * - fragment: (#)'fragment-anchor'
 *
 * URL structure:
 *
 * https://user:password@host:443/base_path/[base_script][scopeview_path]route_name/controller_name/action_name/param1/value1?query_param=query_value#fragment
 *       \__________A___________/\____________________________________B_____________________________________/
 * \__________________C___________________/              \__________________D_________________/ \_____E_____/
 * \_____________F______________/                        \___________________________G______________________/
 * \___________________________________________________H____________________________________________________/
 *
 * - A: authority
 * - B: path
 * - C: absolute_base_url
 * - D: action_path
 * - E: route_params
 * - F: host_url
 * - G: route_path
 * - H: route_url
 *
 * @category   Magento
 * @package    Magento_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento;

class Url extends \Magento\Object implements \Magento\UrlInterface
{
    /**
     * Configuration data cache
     *
     * @var array
     */
    static protected $_configDataCache;

    /**
     * Encrypted session identifier
     *
     * @var string|null
     */
    static protected $_encryptedSessionId;

    /**
     * Reserved Route parameter keys
     *
     * @var array
     */
    protected $_reservedRouteParams = array(
        '_scope', '_type', '_secure', '_forced_secure', '_use_rewrite', '_nosid',
        '_absolute', '_current', '_direct', '_fragment', '_escape', '_query',
        '_scope_to_url'
    );

    /**
     * Request instance
     *
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * Use Session ID for generate URL
     *
     * @var bool
     */
    protected $_useSession;

    /**
     * Url security info list
     *
     * @var \Magento\Url\SecurityInfoInterface
     */
    protected $_urlSecurityInfo;

    /**
     * @var \Magento\AppInterface
     */
    protected $_app;

    /**
     * @var \Magento\Core\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Session\SidResolverInterface
     */
    protected $_sidResolver;

    /**
     * Constructor
     *
     * @var \Magento\App\Route\ConfigInterface
     */
    protected $_routeConfig;

    /**
     * @var \Magento\Url\RouteParamsResolverInterface
     */
    protected $_routeParamsResolver;

    /**
     * @var \Magento\Url\ScopeResolverInterface
     */
    protected $_scopeResolver;

    /**
     * @param \Magento\App\Route\ConfigInterface $routeConfig
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Url\SecurityInfoInterface $urlSecurityInfo
     * @param \Magento\AppInterface $app
     * @param \Magento\Url\ScopeResolverInterface $scopeResolver
     * @param \Magento\Session\Generic $session
     * @param \Magento\Session\SidResolverInterface $sidResolver
     * @param \Magento\Url\RouteParamsResolverFactory $routeParamsResolver
     * @param array $data
     */
    public function __construct(
        \Magento\App\Route\ConfigInterface $routeConfig,
        \Magento\App\RequestInterface $request,
        \Magento\Url\SecurityInfoInterface $urlSecurityInfo,
        \Magento\AppInterface $app,
        \Magento\Url\ScopeResolverInterface $scopeResolver,
        \Magento\Session\Generic $session,
        \Magento\Session\SidResolverInterface $sidResolver,
        \Magento\Url\RouteParamsResolverFactory $routeParamsResolver,
        array $data = array()
    ) {
        $this->_request = $request;
        $this->_routeConfig = $routeConfig;
        $this->_urlSecurityInfo = $urlSecurityInfo;
        $this->_app = $app;
        $this->_scopeResolver = $scopeResolver;
        $this->_session = $session;
        $this->_sidResolver = $sidResolver;
        $this->_routeParamsResolver = $routeParamsResolver->create();
        parent::__construct($data);
    }

    /**
     * Get default url type
     *
     * @return string
     */
    protected function _getDefaultUrlType()
    {
        return \Magento\UrlInterface::URL_TYPE_LINK;
    }

    /**
     * Initialize object data from retrieved url
     *
     * @param   string $url
     * @return  \Magento\Core\Model\Url
     */
    public function parseUrl($url)
    {
        $data = parse_url($url);
        $parts = array(
            'scheme'   => 'setScheme',
            'host'     => 'setHost',
            'port'     => 'setPort',
            'user'     => 'setUser',
            'pass'     => 'setPassword',
            'path'     => 'setPath',
            'query'    => 'setQuery',
            'fragment' => 'setFragment');

        foreach ($parts as $component => $method) {
            if (isset($data[$component])) {
                $this->$method($data[$component]);
            }
        }
        return $this;
    }

    /**
     * Retrieve default controller name
     *
     * @return string
     */
    public function getDefaultControllerName()
    {
        return self::DEFAULT_CONTROLLER_NAME;
    }

    /**
     * Set use session rule
     *
     * @param bool $useSession
     * @return \Magento\Core\Model\Url
     */
    public function setUseSession($useSession)
    {
        $this->_useSession = (bool) $useSession;
        return $this;
    }

    /**
     * Set route front name
     *
     * @param string $name
     * @return \Magento\Core\Model\Url
     */
    public function setRouteFrontName($name)
    {
        $this->setData('route_front_name', $name);
        return $this;
    }

    /**
     * Retrieve use session rule
     *
     * @return bool
     */
    public function getUseSession()
    {
        if (is_null($this->_useSession)) {
            $this->_useSession = $this->_app->getUseSessionInUrl();
        }
        return $this->_useSession;
    }

    /**
     * Retrieve default action name
     *
     * @return string
     */
    public function getDefaultActionName()
    {
        return self::DEFAULT_ACTION_NAME;
    }

    /**
     * Retrieve configuration data
     *
     * @param string $key
     * @param string|null $prefix
     * @return string
     */
    public function getConfigData($key, $prefix = null)
    {
        if (is_null($prefix)) {
            $prefix = 'web/' . ($this->isSecure() ? 'secure' : 'unsecure').'/';
        }
        $path = $prefix . $key;

        $cacheId = $this->_getConfigCacheId($path);
        if (!isset(self::$_configDataCache[$cacheId])) {
            $data = $this->_getConfig($path);
            self::$_configDataCache[$cacheId] = $data;
        }

        return self::$_configDataCache[$cacheId];
    }

    /**
     * Get cache id for config path
     *
     * @param string $path
     * @return string
     */
    protected function _getConfigCacheId($path)
    {
        return $this->getScope()->getCode() . '/' . $path;
    }

    /**
     * Get config data by path
     *
     * @param string $path
     * @return null|string
     */
    protected function _getConfig($path)
    {
        return $this->getScope()->getConfig($path);
    }

    /**
     * Set request
     *
     * @param \Magento\App\RequestInterface $request
     * @return \Magento\Core\Model\Url
     */
    public function setRequest(\Magento\App\RequestInterface $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * Zend request object
     *
     * @return \Magento\App\RequestInterface
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Retrieve URL type
     *
     * @return string
     */
    public function getType()
    {
        if (!$this->_routeParamsResolver->hasData('type')) {
            $this->_routeParamsResolver->setData('type', $this->_getDefaultUrlType());
        }
        return $this->_routeParamsResolver->getType();
    }

    /**
     * @param string $type
     * @return mixed
     */
    public function setType($type)
    {
        $this->_routeParamsResolver->setType($type);
        return $this;
    }

    /**
     * Retrieve is secure mode URL
     *
     * @return bool
     */
    public function isSecure()
    {
        if ($this->_routeParamsResolver->hasData('secure_is_forced')) {
            return (bool)$this->_routeParamsResolver->getData('secure');
        }

        if (!$this->getScope()->isUrlSecure()) {
            return false;
        }

        if (!$this->_routeParamsResolver->hasData('secure')) {
            if ($this->getType() == \Magento\UrlInterface::URL_TYPE_LINK) {
                $pathSecure = $this->_urlSecurityInfo->isSecure('/' . $this->getActionPath());
                $this->_routeParamsResolver->setData('secure', $pathSecure);
            } else {
                $this->_routeParamsResolver->setData('secure', true);
            }
        }

        return $this->_routeParamsResolver->getData('secure');
    }

    /**
     * Set scope entity
     *
     * @param mixed $params
     * @return \Magento\Core\Model\Url
     */
    public function setScope($params)
    {
        $this->setData('scope', $this->_scopeResolver->getScope($params));
        $this->_routeParamsResolver->setScope($this->_scopeResolver->getScope($params));
        return $this;
    }

    /**
     * Get current scope for the url instance
     *
     * @return \Magento\Url\ScopeInterface
     */
    public function getScope()
    {
        if (!$this->hasData('scope')) {
            $this->setScope(null);
        }
        return $this->_getData('scope');
    }

    /**
     * Retrieve Base URL
     *
     * @param array $params
     * @return string
     */
    public function getBaseUrl($params = array())
    {
        if (isset($params['_scope'])) {
            $this->setScope($params['_scope']);
        }
        if (isset($params['_type'])) {
            $this->_routeParamsResolver->setType($params['_type']);
        }

        if (isset($params['_secure'])) {
            $this->_routeParamsResolver->setSecure($params['_secure']);
        }

        /**
         * Add availability support urls without scope code
         */
        if ($this->getType() == \Magento\UrlInterface::URL_TYPE_LINK
            && $this->getRequest()->isDirectAccessFrontendName($this->getRouteFrontName())) {
            $this->_routeParamsResolver->setType(\Magento\UrlInterface::URL_TYPE_DIRECT_LINK);
        }

        $result =  $this->getScope()->getBaseUrl($this->getType(), $this->isSecure());
        $this->_routeParamsResolver->setType($this->_getDefaultUrlType());
        return $result;
    }

    /**
     * Set Route Parameters
     *
     * @param string $data
     * @return \Magento\Core\Model\Url
     */
    public function setRoutePath($data)
    {
        if ($this->_getData('route_path') == $data) {
            return $this;
        }

        $this->unsetData('route_path');
        $routePieces = explode('/', $data);

        $route = array_shift($routePieces);
        if ('*' === $route) {
            $route = $this->getRequest()->getRequestedRouteName();
        }
        $this->setRouteName($route);

        $controller = '';
        if (!empty($routePieces)) {
            $controller = array_shift($routePieces);
            if ('*' === $controller) {
                $controller = $this->getRequest()->getRequestedControllerName();
            }
        }
        $this->setControllerName($controller);

        $action = '';
        if (!empty($routePieces)) {
            $action = array_shift($routePieces);
            if ('*' === $action) {
                $action = $this->getRequest()->getRequestedActionName();
            }
        }
        $this->setActionName($action);

        if (!empty($routePieces)) {
            while (!empty($routePieces)) {
                $key = array_shift($routePieces);
                if (!empty($routePieces)) {
                    $value = array_shift($routePieces);
                    $this->_routeParamsResolver->setRouteParam($key, $value);
                }
            }
        }

        return $this;
    }

    /**
     * Retrieve action path
     *
     * @return string
     */
    public function getActionPath()
    {
        if (!$this->getRouteName()) {
            return '';
        }

        $hasParams = (bool) $this->getRouteParams();
        $path = $this->getRouteFrontName() . '/';

        if ($this->getControllerName()) {
            $path .= $this->getControllerName() . '/';
        } elseif ($hasParams) {
            $path .= $this->getDefaultControllerName() . '/';
        }
        if ($this->getActionName()) {
            $path .= $this->getActionName() . '/';
        } elseif ($hasParams) {
            $path .= $this->getDefaultActionName() . '/';
        }

        return $path;
    }

    /**
     * Retrieve route path
     *
     * @param array $routeParams
     * @return string
     */
    public function getRoutePath($routeParams = array())
    {
        if (!$this->hasData('route_path')) {
            $routePath = $this->getRequest()->getAlias(self::REWRITE_REQUEST_PATH_ALIAS);
            if (!empty($routeParams['_use_rewrite']) && ($routePath !== null)) {
                $this->setData('route_path', $routePath);
                return $routePath;
            }
            $routePath = $this->getActionPath();
            if ($this->getRouteParams()) {
                foreach ($this->getRouteParams() as $key=>$value) {
                    if (is_null($value) || false === $value || '' === $value || !is_scalar($value)) {
                        continue;
                    }
                    $routePath .= $key . '/' . $value . '/';
                }
            }
            if ($routePath != '' && substr($routePath, -1, 1) !== '/') {
                $routePath .= '/';
            }
            $this->setData('route_path', $routePath);
        }
        return $this->_getData('route_path');
    }

    /**
     * Set route name
     *
     * @param string $data
     * @return \Magento\Core\Model\Url
     */
    public function setRouteName($data)
    {
        if ($this->_getData('route_name') == $data) {
            return $this;
        }
        $this->unsetData('route_front_name')
            ->unsetData('route_path')
            ->unsetData('controller_name')
            ->unsetData('action_name')
            ->unsetData('secure');
        return $this->setData('route_name', $data);
    }

    /**
     * Retrieve route front name
     *
     * @return string
     */
    public function getRouteFrontName()
    {
        if (!$this->hasData('route_front_name')) {
            $frontName = $this->_routeConfig->getRouteFrontName(
                $this->getRouteName(),
                $this->_scopeResolver->getAreaCode()
            );
            $this->setRouteFrontName($frontName);
        }

        return $this->_getData('route_front_name');
    }

    /**
     * Retrieve route name
     *
     * @param mixed $default
     * @return string|null
     */
    public function getRouteName($default = null)
    {
        return $this->_getData('route_name') ? $this->_getData('route_name') : $default;
    }

    /**
     * Set Controller Name
     *
     * Reset action name and route path if has change
     *
     * @param string $data
     * @return \Magento\Core\Model\Url
     */
    public function setControllerName($data)
    {
        if ($this->_getData('controller_name') == $data) {
            return $this;
        }
        $this->unsetData('route_path')->unsetData('action_name')->unsetData('secure');
        return $this->setData('controller_name', $data);
    }

    /**
     * Retrieve controller name
     *
     * @param mixed $default
     * @return string|null
     */
    public function getControllerName($default = null)
    {
        return $this->_getData('controller_name') ? $this->_getData('controller_name') : null;
    }

    /**
     * Set Action name
     * Reseted route path if action name has change
     *
     * @param string $data
     * @return \Magento\Core\Model\Url
     */
    public function setActionName($data)
    {
        if ($this->_getData('action_name') == $data) {
            return $this;
        }
        $this->unsetData('route_path');
        return $this->setData('action_name', $data)->unsetData('secure');
    }

    /**
     * Retrieve action name
     *
     * @param mixed $default
     * @return string|null
     */
    public function getActionName($default = null)
    {
        return $this->_getData('action_name') ? $this->_getData('action_name') : $default;
    }

    /**
     * Set route params
     *
     * @param array $data
     * @param boolean $unsetOldParams
     * @return \Magento\Core\Model\Url
     */
    public function setRouteParams(array $data, $unsetOldParams = true)
    {
        $this->_routeParamsResolver->setRouteParams($data, $unsetOldParams);
        return $this;
    }

    /**
     * Retrieve route params
     *
     * @return array
     */
    public function getRouteParams()
    {
        return $this->_routeParamsResolver->getRouteParams();
    }

    /**
     * Set route param
     *
     * @param string $key
     * @param mixed $data
     * @return \Magento\Core\Model\Url
     */
    public function setRouteParam($key, $data)
    {
        return $this->_routeParamsResolver->setRouteParam($key, $data);
    }

    /**
     * Retrieve route params
     *
     * @param string $key
     * @return mixed
     */
    public function getRouteParam($key)
    {
        return $this->_routeParamsResolver->getRouteParam($key);
    }

    /**
     * Retrieve route URL
     *
     * @param string $routePath
     * @param array $routeParams
     * @return string
     */
    public function getRouteUrl($routePath = null, $routeParams = null)
    {
        if (filter_var($routePath, FILTER_VALIDATE_URL)) {
            return $routePath;
        }

        $this->_routeParamsResolver->unsetData('route_params');

        if (isset($routeParams['_direct'])) {
            if (is_array($routeParams)) {
                $this->setRouteParams($routeParams, false);
            }
            return $this->getBaseUrl() . $routeParams['_direct'];
        }

        $this->setRoutePath($routePath);
        if (is_array($routeParams)) {
            $this->setRouteParams($routeParams, false);
        }

        return $this->getBaseUrl() . $this->getRoutePath($routeParams);
    }

    /**
     * If the host was switched but session cookie won't recognize it - add session id to query
     *
     * @return \Magento\Core\Model\Url
     */
    public function checkCookieDomains()
    {
        $hostArr = explode(':', $this->getRequest()->getServer('HTTP_HOST'));
        if ($hostArr[0] !== $this->getHost()) {
            if (!$this->_session->isValidForHost($this->getHost())) {
                if (!self::$_encryptedSessionId) {
                    self::$_encryptedSessionId = $this->_session->getSessionId();
                }
                $this->setQueryParam(
                    $this->_sidResolver->getSessionIdQueryParam($this->_session),
                    self::$_encryptedSessionId
                );
            }
        }
        return $this;
    }

    /**
     * Add session param
     *
     * @return \Magento\Core\Model\Url
     */
    public function addSessionParam()
    {
        if (!self::$_encryptedSessionId) {
            self::$_encryptedSessionId = $this->_session->getSessionId();
        }
        $this->setQueryParam($this->_sidResolver->getSessionIdQueryParam($this->_session), self::$_encryptedSessionId);
        return $this;
    }

    /**
     * Set URL query param(s)
     *
     * @param mixed $data
     * @return \Magento\Core\Model\Url
     */
    public function setQuery($data)
    {
        if ($this->_getData('query') == $data) {
            return $this;
        }
        $this->unsetData('query_params');
        return $this->setData('query', $data);
    }

    /**
     * Get query params part of url
     *
     * @param bool $escape "&" escape flag
     * @return string
     */
    public function getQuery($escape = false)
    {
        if (!$this->hasData('query')) {
            $query = '';
            $params = $this->getQueryParams();
            if (is_array($params)) {
                ksort($params);
                $query = http_build_query($params, '', $escape ? '&amp;' : '&');
            }
            $this->setData('query', $query);
        }
        return $this->_getData('query');
    }

    /**
     * Set query Params as array
     *
     * @param array $data
     * @return \Magento\Core\Model\Url
     */
    public function setQueryParams(array $data)
    {
        $this->unsetData('query');

        if ($this->_getData('query_params') == $data) {
            return $this;
        }

        $params = $this->_getData('query_params');
        if (!is_array($params)) {
            $params = array();
        }
        foreach ($data as $param => $value) {
            $params[$param] = $value;
        }
        $this->setData('query_params', $params);

        return $this;
    }

    /**
     * Purge Query params array
     *
     * @return \Magento\Core\Model\Url
     */
    public function purgeQueryParams()
    {
        $this->setData('query_params', array());
        return $this;
    }

    /**
     * Return Query Params
     *
     * @return array
     */
    public function getQueryParams()
    {
        if (!$this->hasData('query_params')) {
            $params = array();
            if ($this->_getData('query')) {
                foreach (explode('&', $this->_getData('query')) as $param) {
                    $paramArr = explode('=', $param);
                    $params[$paramArr[0]] = urldecode($paramArr[1]);
                }
            }
            $this->setData('query_params', $params);
        }
        return $this->_getData('query_params');
    }

    /**
     * Set query param
     *
     * @param string $key
     * @param mixed $data
     * @return \Magento\Core\Model\Url
     */
    public function setQueryParam($key, $data)
    {
        $params = $this->getQueryParams();
        if (isset($params[$key]) && $params[$key] == $data) {
            return $this;
        }
        $params[$key] = $data;
        $this->unsetData('query');
        return $this->setData('query_params', $params);
    }

    /**
     * Retrieve query param
     *
     * @param string $key
     * @return mixed
     */
    public function getQueryParam($key)
    {
        if (!$this->hasData('query_params')) {
            $this->getQueryParams();
        }
        return $this->getData('query_params', $key);
    }

    /**
     * Set fragment to URL
     *
     * @param string $data
     * @return \Magento\Core\Model\Url
     */
    public function setFragment($data)
    {
        return $this->setData('fragment', $data);
    }

    /**
     * Retrieve URL fragment
     *
     * @return string|null
     */
    public function getFragment()
    {
        return $this->_getData('fragment');
    }

    /**
     * Build url by requested path and parameters
     *
     * @param   string|null $routePath
     * @param   array|null $routeParams
     * @return  string
     */
    public function getUrl($routePath = null, $routeParams = null)
    {
        if (filter_var($routePath, FILTER_VALIDATE_URL)) {
            return $routePath;
        }

        $escapeQuery = false;

        /**
         * All system params should be unset before we call getRouteUrl
         * this method has condition for adding default controller and action names
         * in case when we have params
         */
        $fragment = null;
        if (isset($routeParams['_fragment'])) {
            $fragment = $routeParams['_fragment'];
            unset($routeParams['_fragment']);
        }

        if (isset($routeParams['_escape'])) {
            $escapeQuery = $routeParams['_escape'];
            unset($routeParams['_escape']);
        }

        $query = null;
        if (isset($routeParams['_query'])) {
            $this->purgeQueryParams();
            $query = $routeParams['_query'];
            unset($routeParams['_query']);
        }

        $noSid = null;
        if (isset($routeParams['_nosid'])) {
            $noSid = (bool)$routeParams['_nosid'];
            unset($routeParams['_nosid']);
        }
        $url = $this->getRouteUrl($routePath, $routeParams);
        /**
         * Apply query params, need call after getRouteUrl for rewrite _current values
         */
        if ($query !== null) {
            if (is_string($query)) {
                $this->setQuery($query);
            } elseif (is_array($query)) {
                $this->setQueryParams($query, !empty($routeParams['_current']));
            }
            if ($query === false) {
                $this->setQueryParams(array());
            }
        }

        if ($noSid !== true) {
            $this->_prepareSessionUrl($url);
        }

        $query = $this->getQuery($escapeQuery);
        if ($query) {
            $mark = (strpos($url, '?') === false) ? '?' : ($escapeQuery ? '&amp;' : '&');
            $url .= $mark . $query;
            $this->unsetData('query');
            $this->unsetData('query_params');
        }

        if (!is_null($fragment)) {
            $url .= '#' . $fragment;
        }

        return $this->escape($url);
    }

    /**
     * Check and add session id to URL
     *
     * @param string $url
     *
     * @return \Magento\Core\Model\Url
     */
    protected function _prepareSessionUrl($url)
    {
        return $this->_prepareSessionUrlWithParams($url, array());
    }

    /**
     * Check and add session id to URL, session is obtained with parameters
     *
     * @param string $url
     * @param array $params
     *
     * @return \Magento\Core\Model\Url
     */
    protected function _prepareSessionUrlWithParams($url, array $params)
    {
        if (!$this->getUseSession()) {
            return $this;
        }
        $sessionId = $this->_session->getSessionIdForHost($url);
        if ($this->_app->getUseSessionVar() && !$sessionId) {
            $this->setQueryParam('___SID', $this->isSecure() ? 'S' : 'U'); // Secure/Unsecure
        } else if ($sessionId) {
            $this->setQueryParam($this->_sidResolver->getSessionIdQueryParam($this->_session), $sessionId);
        }
        return $this;
    }

    /**
     * Rebuild URL to handle the case when session ID was changed
     *
     * @param string $url
     * @return string
     */
    public function getRebuiltUrl($url)
    {
        $this->parseUrl($url);
        $port = $this->getPort();
        if ($port) {
            $port = ':' . $port;
        } else {
            $port = '';
        }
        $url = $this->getScheme() . '://' . $this->getHost() . $port . $this->getPath();

        $this->_prepareSessionUrl($url);

        $query = $this->getQuery();
        if ($query) {
            $url .= '?' . $query;
        }

        $fragment = $this->getFragment();
        if ($fragment) {
            $url .= '#' . $fragment;
        }

        return $this->escape($url);
    }

    /**
     * Escape (enclosure) URL string
     *
     * @param string $value
     * @return string
     */
    public function escape($value)
    {
        $value = str_replace('"', '%22', $value);
        $value = str_replace("'", '%27', $value);
        $value = str_replace('>', '%3E', $value);
        $value = str_replace('<', '%3C', $value);
        return $value;
    }

    /**
     * Build url by direct url and parameters
     *
     * @param string $url
     * @param array $params
     * @return string
     */
    public function getDirectUrl($url, $params = array())
    {
        $params['_direct'] = $url;
        return $this->getUrl('', $params);
    }

    /**
     * Replace Session ID value in URL
     *
     * @param string $html
     * @return string
     */
    public function sessionUrlVar($html)
    {
        return preg_replace_callback('#(\?|&amp;|&)___SID=([SU])(&amp;|&)?#',
            array($this, "sessionVarCallback"), $html);
    }

    /**
     * Check and return use SID for URL
     *
     * @param bool $secure
     * @return bool
     */
    public function useSessionIdForUrl($secure = false)
    {
        $key = 'use_session_id_for_url_' . (int) $secure;
        if (is_null($this->getData($key))) {
            $httpHost = $this->_request->getHttpHost();
            $urlHost = parse_url($this->getScope()->getBaseUrl(\Magento\UrlInterface::URL_TYPE_LINK, $secure),
                PHP_URL_HOST);

            if ($httpHost != $urlHost) {
                $this->setData($key, true);
            } else {
                $this->setData($key, false);
            }
        }
        return $this->getData($key);
    }

    /**
     * Callback function for session replace
     *
     * @param array $match
     * @return string
     */
    public function sessionVarCallback($match)
    {
        if ($this->useSessionIdForUrl($match[2] == 'S' ? true : false)) {
            return $match[1]
                . $this->_sidResolver->getSessionIdQueryParam($this->_session)
                . '=' . $this->_session->getSessionId()
                . (isset($match[3]) ? $match[3] : '');
        } else {
            if ($match[1] == '?' && isset($match[3])) {
                return '?';
            } elseif ($match[1] == '?' && !isset($match[3])) {
                return '';
            } elseif (($match[1] == '&amp;' || $match[1] == '&') && !isset($match[3])) {
                return '';
            } elseif (($match[1] == '&amp;' || $match[1] == '&') && isset($match[3])) {
                return $match[3];
            }
        }
        return '';
    }

    /**
     * Check if users originated URL is one of the domain URLs assigned to scopes
     *
     * @return boolean
     */
    public function isOwnOriginUrl()
    {
        $scopeDomains = array();
        $referer = parse_url($this->_app->getRequest()->getServer('HTTP_REFERER'), PHP_URL_HOST);
        foreach ($this->_scopeResolver->getScopes() as $scope) {
            $scopeDomains[] = parse_url($scope->getBaseUrl(), PHP_URL_HOST);
            $scopeDomains[] = parse_url($scope->getBaseUrl(
                \Magento\UrlInterface::URL_TYPE_LINK, true), PHP_URL_HOST
            );
        }
        $scopeDomains = array_unique($scopeDomains);
        if (empty($referer) || in_array($referer, $scopeDomains)) {
            return true;
        }
        return false;
    }

    /**
     * Return frontend redirect URL with SID and other session parameters if any
     *
     * @param string $url
     *
     * @return string
     */
    public function getRedirectUrl($url)
    {
        $this->_prepareSessionUrlWithParams($url, array(
            'name' => self::SESSION_NAMESPACE
        ));

        $query = $this->getQuery(false);
        if ($query) {
            $url .= (strpos($url, '?') === false ? '?' : '&') . $query;
        }

        return $url;
    }

    /**
     * Retrieve current url
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        $port = $this->_request->getServer('SERVER_PORT');
        if ($port) {
            $defaultPorts = array(
                \Magento\App\Request\Http::DEFAULT_HTTP_PORT,
                \Magento\App\Request\Http::DEFAULT_HTTPS_PORT
            );
            $port = (in_array($port, $defaultPorts)) ? '' : ':' . $port;
        }
        $requestUri = $this->_request->getServer('REQUEST_URI');
        $url = $this->_request->getScheme() . '://' . $this->_request->getHttpHost()
            . $port . $requestUri;
        return $url;
    }
}
