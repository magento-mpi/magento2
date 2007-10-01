<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * URL
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Moshe Gurvich <moshe@varien.com>
 */
class Mage_Core_Model_Url extends Varien_Object
{
    const TYPE_LINK = 'link';
    const TYPE_SKIN = 'skin';
    const TYPE_JS = 'js';

    const SCHEME_UNSECURE = 'http';
    const SCHEME_SECURE = 'https';

    const PORT_UNSECURE = 80;
    const PORT_SECURE = 443;

    static protected $_configDataCache;
    static protected $_baseUrlCache;

    /**
     * Controller request object
     *
     * @var Zend_Controller_Request_Http
     */
    protected $_request;

    /**
     * Properties:
     *
     * - request
     *
     * - relative_url: true, false
     * - type: 'link', 'skin'
     * - store: instanceof Mage_Core_Model_Store
     * - secure: true, false
     *
     * - scheme: 'http', 'https'
     * - user: 'user'
     * - password: 'password'
     * - host: 'localhost'
     * - port: 80, 443
     * - base_path: '/dev/magento'
     *
     * - route_path: '/module/controller/action/param1/value1/param2/value2'
     * - route_name: 'module'
     * - controller_name: 'controller'
     * - action_name: 'action'
     * - route_params: array('param1'=>'value1', 'param2'=>'value2')
     *
     * - query: (?)'param1=value1&param2=value2'
     * - query_array: array('param1'=>'value1', 'param2'=>'value2')
     * - fragment: (#)'fragment-anchor'
     */

    protected function _construct()
    {
        $this->setStore(null);
    }

    public function parseUrl($url)
    {
        $a = parse_url($url);
        $this->setScheme($a['scheme']);
        $this->setHost($a['host']);
        $this->setPort($a['port']);
        $this->setUser($a['user']);
        $this->setPassword($a['pass']);
        $this->setPath($a['path']);
        $this->setQuery($a['query']);
        $this->setFragment($a['fragment']);
        return $this;
    }

    public function getDefaultControllerName()
    {
        return 'index';
    }

    public function getDefaultActionName()
    {
        return 'index';
    }

    public function getConfigData($key)
    {
        $secure = $this->getSecure();
        if (!isset(self::$_configDataCache[$secure][$key])) {
            $path = 'web/'.($secure ? 'secure' : 'unsecure').'/'.$key;
            self::$_configDataCache[$secure][$key] = $this->getStore()->getConfig($path);
        }
        return self::$_configDataCache[$secure][$key];
    }

    public function isCurrentlySecure()
    {
        return (bool)$_SERVER['HTTPS'];
    }

    public function setRequest(Zend_Controller_Request_Http $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * Zend request object
     *
     * @return Zend_Controller_Request_Http
     */
    public function getRequest()
    {
        if (!$this->_request) {
            $this->_request = Mage::app()->getFrontController()->getRequest();
        }
        return $this->_request;
    }

    public function setType($data)
    {
        $this->setData('type', $data);
        $this->unsetData('secure');
        $this->unsetData('query')->unsetData('query_params');
        $this->unsetData('fragment');
        $this->getSecure();
        return $this;
    }

    public function getType()
    {
        if (!$this->hasData('type')) {
            $this->setData('type', self::TYPE_LINK);
        }
        return $this->getData('type');
    }

    public function refreshAbsoluteBase()
    {
        $this->unsetData('scheme');#->getScheme();
        $this->unsetData('host');#->getHost();
        $this->unsetData('port');#->getPort();
        $this->unsetData('base_path');#->getBasePath();
        $this->unsetData('authority');
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param boolean $flag
     * @return Mage_Core_Model_Url
     */
    public function setSecure($flag)
    {
        $this->setData('secure', $flag)->refreshAbsoluteBase();
        return $this;
    }

    public function getSecure()
    {
        if (!$this->hasData('secure')) {
            if ($this->getType()===self::TYPE_SKIN || $this->getType()===self::TYPE_JS) {
                $flag = $this->isCurrentlySecure();
            } else {
                $flag = Mage::getConfig()->isUrlSecure($this->getActionPath());
            }
        }
        return $this->getData('secure');
    }

    public function setStore($data)
    {
        $defStore = Mage::app()->getStore();

        if (is_null($data)) {
            $store = $defStore;
        } elseif (is_numeric($data)) {
            if ($defStore->getId()==$data) {
                $store = $defStore;
            } else {
                $store = Mage::getModel('core/store')->load($data);
            }
        } elseif (is_string($data)) {
            if ($defStore->getCode()==$data) {
                $store = $defStore;
            } else {
                $store = Mage::getModel('core/store')->load($data);
            }
        } elseif ($store instanceof Mage_Core_Model_Store) {
            $store = $data;
        } else {
            throw Mage::exception('Mage_Core', 'Wrong store argument specified');
        }

        $this->setData('store', $store)->refreshAbsoluteBase();
        return $this;
    }

    public function getStore()
    {
        if (!$this->hasData('store')) {
            $this->setStore(null);
        }
        return $this->getData('store');
    }

    public function setScheme($data)
    {
        if ($data!==SCHEME_SECURE && $data!==SCHEME_UNSECURE) {
            throw Mage::exception('Mage_Core', 'Invalid scheme specified');
        }

        $this->setData('secure', $data===SCHEME_SECURE);

        return $this->setData('scheme', $data);
    }

    public function getScheme()
    {
        if (!$this->hasData('scheme')) {
            $this->setData('scheme', $this->getConfigData('protocol'));
        }
        return $this->getData('scheme');
    }

    public function setHost($data)
    {
        $this->setData('host', $data)->unsetData('authority');
        return $this;
    }

    public function getHost()
    {
        if (!$this->hasData('host')) {
            $this->setData('host', $this->getConfigData('host'));
        }
        return $this->getData('host');
    }

    public function setPort($data)
    {
        $this->setData('port', $data)->unsetData('authority');
        return $this;
    }

    public function getPort()
    {
        if (!$this->hasData('port')) {
            $this->setData('port', $this->getConfigData('port'));
        }
        return $this->getData('port');
    }

    public function setBasePath($data)
    {
        $this->setData('base_path', $data)->unsetData('authority');
        return $this;
    }

    public function getBasePath()
    {
        if (!$this->hasData('base_path')) {
            $this->setData('base_path', $this->getConfigData('base_path'));
        }
        return $this->getData('base_path');
    }

    public function setPath($data)
    {
        if (strpos($data, $this->getBasePath())===0) {
            $this->setRoutePath(substr($data, strlen($this->getBasePath())));
        } else {
            $this->setBasePath('');
            $this->setRoutePath($data);
        }
        return $this;
    }

    public function getPath()
    {
        return $this->getBasePath().$this->getRoutePath();
    }

    public function getAuthority()
    {
        if (!$this->hasData('authority')) {
            $authority = '//'.$this->getHost();

            if ($this->getUser()) {
                $authority .= $this->getUser();
                if ($this->getPassword()) {
                    $authority .= ':'.$this->getPassword();
                }
                $authority .= '@';
            }

            $secure = $this->getSecure();
            $port = $this->getPort();
            if (!$secure && $port!=self::PORT_UNSECURE
                || $secure && $port!=self::PORT_SECURE) {
                $authority .= ':'.$port;
            }
            $this->setData('authority', $authority);
        }
        return $this->getData('authority');
    }

    public function getAbsoluteBaseUrl()
    {
        $url = $this->getScheme().':'.$this->getAuthority().$this->getBasePath();
        return $url;
    }

    public function setRoutePath($data)
    {
        $a = explode('/', $data);

        $route = array_shift($a);
        if ('*'===$route) {
            $route = $this->getRequest()->getModuleName();
        }
        $this->setRouteName($route);

        if (!empty($a)) {
            $controller = array_shift($a);
            if ('*'===$controller) {
                $controller = $this->getRequest()->getControllerName();
            }
            $this->setControllerName($controller);
        }

        if (!empty($a)) {
            $action = array_shift($a);
            if ('*'===$action) {
                $action = $this->getRequest()->getActionName();
            }
            $this->setActionName($action);
        }

        if (!empty($a)) {
            $this->unsetData('route_params');
            while (!empty($a)) {
                $key = array_shift($a);
                if (!empty($a)) {
                    $value = array_shift($a);
                    $this->setRouteParam($key, $value);
                }
            }
        }

        return $this->setData('route_path', $data);
    }

    public function getRoutePath()
    {
        if (!$this->hasData('route_path')) {
            /*
            $params = $this->getRouteParams();
            $params['controller'] = $this->getControllerName();
            $params['action'] = $this->getActionName();
            $router = Mage::app()->getFrontController()->getRouterByRoute($this->getRouteName());
            $routePath = $router->getUrl($this->getRouteName(), $params);
            */
            $routePath = $this->getRouteName();
            if ($this->getControllerName()) {
                $routePath .= '/'.$this->getControllerName();
            } elseif ($this->getRouteParams()) {
                $routePath .= '/'.$this->getDefaultControllerName();
            }
            if ($this->getActionName()) {
                $routePath .= '/'.$this->getActionName();
            } elseif ($this->getRouteParams()) {
                $routePath .= '/'.$this->getDefaultActionName();
            }
            if ($this->getRouteParams()) {
                foreach ($this->getRouteParams() as $key=>$value) {
                    $routePath .='/'.$key.'/'.$value;
                }
            }

            $this->setData('route_path', $routePath);
        }
        return $this->getData('route_path');
    }

    public function setRouteName($data)
    {
        $this->unsetData('route_path');
        return $this->setData('route_name', $data);
    }

    public function getRouteName()
    {
        return $this->getData('route_name');
    }

    public function setControllerName($data)
    {
        return $this->setData('controller_name', $data);
    }

    public function getControllerName()
    {
        return $this->getData('controller_name');
    }

    public function setActionName($data)
    {
        return $this->setData('action_name', $data);
    }

    public function getActionName()
    {
        return $this->getData('action_name');
    }

    public function setRouteParams(array $data)
    {
        if (!empty($data['_type'])) {
            if (self::TYPE_JS===$type || self::TYPE_SKIN===$type) {
                $this->setType($type);
            } else {
                $this->setType(self::TYPE_LINK);
            }
            unset($data['_type']);
        } else {
            $this->setType(self::TYPE_LINK);
        }

        if (!empty($data['_secure'])) {
            $this->setSecure(true);
            unset($data['_secure']);
        }

        if (!empty($data['_current'])) {
            if (is_array($data['_current'])) {
                foreach ($data['_current'] as $key) {
                    if (!$this->getRequest()->getUserParam($key)) {
                        continue;
                    }
                    $data[$key] = $this->getRequest()->getUserParam($key);
                }
            } else {
                foreach ($this->getRequest()->getUserParams() as $key=>$value) {
                    $data[$key] = $value;
                }
                foreach ($this->getRequest()->getQuery() as $key=>$value) {
                    $this->setQueryParam($key, $value);
                }
            }
            unset($data['_current']);
            $this->setUseUrlCache(false);
        } else {
            $this->setUseUrlCache(true);
        }

        $this->unsetData('route_path');
        return $this->setData('route_params', $data);
    }

    public function getRouteParams()
    {
        return $this->getData('route_params');
    }

    public function setRouteParam($key, $data)
    {
        $this->unsetData('route_path');
        $params = $this->getData('route_params');
        $params[$key] = $data;
        return $this->setData('route_params', $params);
    }

    public function getRouteParam($key)
    {
        return $this->getData('route_params', $key);
    }

    public function getRouteUrl($routePath=null, $routeParams=null)
    {
        #$this->unsetData('route_path')->unsetData('route_params');

        if (!is_null($routePath)) {
            $this->setRoutePath($routePath);
        }
        if (!is_null($routeParams)) {
            $this->setRouteParams($routeParams);
        }

        $cacheId = $this->getBaseUrlCacheId();
        if (isset(self::$_baseUrlCache[$cacheId])) {
            $url = self::$_baseUrlCache[$cacheId];
        } else {
            if ($this->getRelativeUrl()) {
                $url = $this->getBasePath();
            } else {
                $url = $this->getAbsoluteBaseUrl();
            }
            self::$_baseUrlCache[$cacheId] = $url;

            $this->checkCookieDomains();
        }

        $url .= $this->getRoutePath();

        return $url;
    }

    public function getBaseUrlCacheId()
    {
        $id = $this->getStore()->getId()
            .'-'.($this->getSecure() ? 1 : 0)
        ;
        return $id;
    }

    /**
     * If the host was switched but session cookie won't recognize it - add session id to query
     *
     * @return unknown
     */
    public function checkCookieDomains()
    {
        $hostArr = explode(':', $this->getRequest()->getServer('HTTP_HOST'));
        if ($hostArr[0]!=$this->getHost()) {
            $session = Mage::getSingleton('core/session');
            if (!$session->getCookieDomains($this->getHost())) {
                $this->setQueryParam(
                    Mage_Core_Model_Session_Abstract::SESSION_ID_QUERY_PARAM,
                    Mage::encrypt($session->getSessionId())
                );
            }
        }
        return $this;
    }

    public function setQuery($data)
    {
        $this->unsetData('query_params');
        return $this->setData('query', $data);
    }

    public function getQuery()
    {
        if (!$this->hasData('query')) {
            $query = '';
            if (is_array($this->getQueryParams())) {
                $query = http_build_query($this->getQueryParams());
            }
            $this->setData('query', $query);
        }
        return $this->getData('query');
    }

    public function setQueryParams(array $data)
    {
        $this->unsetData('query');
        return $this->setData('query_params', $data);
    }

    public function getQueryParams()
    {
        if (!$this->hasData('query_params')) {
            $params = array();
            if ($this->getData('query')) {
                foreach (explode('&', $this->getData('query')) as $param) {
                    $paramArr = explode('=', $param);
                    $params[$paramArr[0]] = urldecode($paramArr[1]);
                }
            }
            $this->setData('query_params', $params);
        }
        return $this->getData('query_params');
    }

    public function setQueryParam($key, $data)
    {
        $params = $this->getQueryParams();
        $params[$key] = $data;
        $this->unsetData('query');
        return $this->setData('query_params', $params);
    }

    public function getQueryParam($key)
    {
        if (!$this->hasData('query_params')) {
            $this->getQueryParams();
        }
        return $this->getData('query_params', $key);
    }

    public function setFragment($data)
    {
        return $this->setData('fragment', $data);
    }

    public function getFragment()
    {
        return $this->getData('fragment');
    }

    public function getUrl($routePath=null, $routeParams=null)
    {
        $url = $this->getRouteUrl($routePath, $routeParams);

        if ($this->getQuery()) {
            $url .= '?'.$this->getQuery();
        }

        if ($this->getFragment()) {
            $url .= '#'.$this->getFragment();
        }

        return $url;
    }
}