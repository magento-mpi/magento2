<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento;

interface UrlInterface
{
    /**
     * Possible URL types
     */
    const URL_TYPE_LINK                   = 'link';
    const URL_TYPE_DIRECT_LINK            = 'direct_link';
    const URL_TYPE_WEB                    = 'web';
    const URL_TYPE_LIB                    = 'lib';
    const URL_TYPE_MEDIA                  = 'media';
    const URL_TYPE_STATIC                 = 'static';
    const URL_TYPE_CACHE                  = 'cache';
    const URL_TYPE_JS                     = 'js';

    /**
     * Default controller name
     */
    const DEFAULT_CONTROLLER_NAME   = 'index';

    /**
     * Default action name
     */
    const DEFAULT_ACTION_NAME       = 'index';

    /**
     * Configuration paths
     */
    const XML_PATH_UNSECURE_URL     = 'web/unsecure/base_url';
    const XML_PATH_SECURE_URL       = 'web/secure/base_url';
    const XML_PATH_SECURE_IN_ADMIN  = 'web/secure/use_in_adminhtml';
    const XML_PATH_SECURE_IN_FRONT  = 'web/secure/use_in_frontend';

    /**
     * Initialize object data from retrieved url
     *
     * @param   string $url
     * @return  \Magento\UrlInterface
     */
    public function parseUrl($url);

    /**
     * Retrieve default controller name
     *
     * @return string
     */
    public function getDefaultControllerName();

    /**
     * Set use session rule
     *
     * @param bool $useSession
     * @return \Magento\UrlInterface
     */
    public function setUseSession($useSession);

    /**
     * Set route front name
     *
     * @param string $name
     * @return \Magento\UrlInterface
     */
    public function setRouteFrontName($name);

    /**
     * Retrieve use session rule
     *
     * @return bool
     */
    public function getUseSession();

    /**
     * Retrieve default action name
     *
     * @return string
     */
    public function getDefaultActionName();

    /**
     * Retrieve configuration data
     *
     * @param string $key
     * @param string|null $prefix
     * @return string
     */
    public function getConfigData($key, $prefix = null);

    /**
     * Set request
     *
     * @param \Magento\App\RequestInterface $request
     * @return \Magento\UrlInterface
     */
    public function setRequest(\Magento\App\RequestInterface $request);

    /**
     * Zend request object
     *
     * @return \Magento\App\RequestInterface
     */
    public function getRequest();

    /**
     * Retrieve URL type
     *
     * @return string
     */
    public function getType();

    /**
     * Retrieve is secure mode URL
     *
     * @return bool
     */
    public function isSecure();

    /**
     * Retrieve Base URL
     *
     * @param array $params
     * @return string
     */
    public function getBaseUrl($params = array());

    /**
     * Retrieve current url with port number (if not default used)
     *
     * @return string
     */
    public function getCurrentUrl();

    /**
     * Set Route Parameters
     *
     * @param array $data
     * @return \Magento\UrlInterface
     */
    public function setRoutePath($data);

    /**
     * Retrieve action path
     *
     * @return string
     */
    public function getActionPath();

    /**
     * Retrieve route path
     *
     * @param array $routeParams
     * @return string
     */
    public function getRoutePath($routeParams = array());

    /**
     * Set route name
     *
     * @param string $data
     * @return \Magento\UrlInterface
     */
    public function setRouteName($data);

    /**
     * Retrieve route front name
     *
     * @return string
     */
    public function getRouteFrontName();

    /**
     * Retrieve route name
     *
     * @param mixed $default
     * @return string|null
     */
    public function getRouteName($default = null);

    /**
     * Set Controller Name
     *
     * Reset action name and route path if has change
     *
     * @param string $data
     * @return \Magento\UrlInterface
     */
    public function setControllerName($data);

    /**
     * Retrieve controller name
     *
     * @param mixed $default
     * @return string|null
     */
    public function getControllerName($default = null);

    /**
     * Set Action name
     * Reseted route path if action name has change
     *
     * @param string $data
     * @return \Magento\UrlInterface
     */
    public function setActionName($data);

    /**
     * Retrieve action name
     *
     * @param mixed $default
     * @return string|null
     */
    public function getActionName($default = null);

    /**
     * Set route params
     *
     * @param array $data
     * @param boolean $unsetOldParams
     * @return \Magento\UrlInterface
     */
    public function setRouteParams(array $data, $unsetOldParams = true);

    /**
     * Retrieve route params
     *
     * @return array
     */
    public function getRouteParams();

    /**
     * Set route param
     *
     * @param string $key
     * @param mixed $data
     * @return \Magento\UrlInterface
     */
    public function setRouteParam($key, $data);

    /**
     * Retrieve route params
     *
     * @param string $key
     * @return mixed
     */
    public function getRouteParam($key);

    /**
     * Retrieve route URL
     *
     * @param string $routePath
     * @param array $routeParams
     * @return string
     */
    public function getRouteUrl($routePath = null, $routeParams = null);

    /**
     * If the host was switched but session cookie won't recognize it - add session id to query
     *
     * @return \Magento\UrlInterface
     */
    public function checkCookieDomains();

    /**
     * Add session param
     *
     * @return \Magento\UrlInterface
     */
    public function addSessionParam();

    /**
     * Set URL query param(s)
     *
     * @param mixed $data
     * @return \Magento\UrlInterface
     */
    public function setQuery($data);

    /**
     * Get query params part of url
     *
     * @param bool $escape "&" escape flag
     * @return string
     */
    public function getQuery($escape = false);

    /**
     * Set query Params as array
     *
     * @param array $data
     * @return \Magento\UrlInterface
     */
    public function setQueryParams(array $data);

    /**
     * Purge Query params array
     *
     * @return \Magento\UrlInterface
     */
    public function purgeQueryParams();

    /**
     * Return Query Params
     *
     * @return array
     */
    public function getQueryParams();

    /**
     * Set query param
     *
     * @param string $key
     * @param mixed $data
     * @return \Magento\UrlInterface
     */
    public function setQueryParam($key, $data);

    /**
     * Retrieve query param
     *
     * @param string $key
     * @return mixed
     */
    public function getQueryParam($key);

    /**
     * Set fragment to URL
     *
     * @param string $data
     * @return \Magento\UrlInterface
     */
    public function setFragment($data);

    /**
     * Retrieve URL fragment
     *
     * @return string|null
     */
    public function getFragment();

    /**
     * Build url by requested path and parameters
     *
     * @param   string|null $routePath
     * @param   array|null $routeParams
     * @return  string
     */
    public function getUrl($routePath = null, $routeParams = null);

    /**
     * Rebuild URL to handle the case when session ID was changed
     *
     * @param string $url
     * @return string
     */
    public function getRebuiltUrl($url);

    /**
     * Escape (enclosure) URL string
     *
     * @param string $value
     * @return string
     */
    public function escape($value);

    /**
     * Build url by direct url and parameters
     *
     * @param string $url
     * @param array $params
     * @return string
     */
    public function getDirectUrl($url, $params = array());

    /**
     * Replace Session ID value in URL
     *
     * @param string $html
     * @return string
     */
    public function sessionUrlVar($html);

    /**
     * Check and return use SID for URL
     *
     * @param bool $secure
     * @return bool
     */
    public function useSessionIdForUrl($secure = false);

    /**
     * Callback function for session replace
     *
     * @param array $match
     * @return string
     */
    public function sessionVarCallback($match);

    /**
     * Check if users originated URL is one of the domain URLs assigned to stores
     *
     * @return boolean
     */
    public function isOwnOriginUrl();

    /**
     * Return frontend redirect URL with SID and other session parameters if any
     *
     * @param string $url
     *
     * @return string
     */
    public function getRedirectUrl($url);

    /**
     * Get current scope for the url instance
     *
     * @return \Magento\Url\ScopeInterface
     */
    public function getScope();

    /**
     * Set scope entity
     *
     * @param mixed $params
     * @return \Magento\Core\Model\Url
     */
    public function setScope($params);
}
