<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Core_Controller_Varien_Front extends Magento_Object implements Mage_Core_Controller_FrontInterface
{
    const XML_STORE_ROUTERS_PATH = 'web/routers';

    /**
     * Prevent redirect to baseUrl for some areas (use for VDE into Magento Go)
     */
    const XML_FORBIDDEN_FOR_REDIRECT_AREAS = 'default/web/forbiddenForRedirectAreas';

    /**
     * @var Mage_Core_Controller_Varien_Router_Factory
     */
    protected $_routerFactory;

    /**
     * @var Mage_Core_Model_Url_RewriteFactory
     */
    protected $_rewriteFactory;

    /**
     * @var array
     */
    protected $_defaults = array();

    /**
     * Available router classes
     *
     * @var array
     */
    protected $_routerTypes = array();

    /**
     * Available routers array
     *
     * @var array
     */
    protected $_routers = array();

    /**
     * @param Mage_Core_Controller_Varien_Router_Factory $routerFactory
     * @param Mage_Core_Model_Url_RewriteFactory $rewriteFactory
     * @param array $routerTypes
     * @param array $data
     */
    public function __construct(
        Mage_Core_Controller_Varien_Router_Factory $routerFactory,
        Mage_Core_Model_Url_RewriteFactory $rewriteFactory,
        $routerTypes,
        array $data = array()
    ) {
        parent::__construct($data);

        $this->_routerFactory  = $routerFactory;
        $this->_rewriteFactory = $rewriteFactory;
        $this->_routersTypes = $routerTypes;
    }

    public function setDefault($key, $value=null)
    {
        if (is_array($key)) {
            $this->_defaults = $key;
        } else {
            $this->_defaults[$key] = $value;
        }
        return $this;
    }

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
     * @return Mage_Core_Controller_Request_Http
     */
    public function getRequest()
    {
        return Mage::app()->getRequest();
    }

    /**
     * Retrieve response object
     *
     * @return Mage_Core_Controller_Response_Http
     */
    public function getResponse()
    {
        return Mage::app()->getResponse();
    }

    /**
     * Adding new router
     *
     * @param   string $name
     * @param   Mage_Core_Controller_Varien_Router_Abstract $router
     * @return  Mage_Core_Controller_Varien_Front
     */
    public function addRouter($name, Mage_Core_Controller_Varien_Router_Abstract $router)
    {
        $router->setFront($this);
        $this->_routers[$name] = $router;
        return $this;
    }

    /**
     * Retrieve router by name
     *
     * @param   string $name
     * @return  Mage_Core_Controller_Varien_Router_Abstract
     */
    public function getRouter($name)
    {
        if (isset($this->_routers[$name])) {
            return $this->_routers[$name];
        }
        return false;
    }

    /**
     * Retrieve routers collection
     *
     * @return array
     */
    public function getRouters()
    {
        return $this->_routers;
    }

    /**
     * Init Front Controller
     *
     * @return Mage_Core_Controller_Varien_Front
     */
    public function init()
    {
        Mage::dispatchEvent('controller_front_init_before', array('front'=>$this));

        foreach ($this->_routersTypes as $routerCode => $routerClass) {
            $router = $this->_routerFactory->createRouter($routerClass);
            $this->addRouter($routerCode, $router);
        }

        Mage::dispatchEvent('controller_front_init_routers', array('front'=>$this));

        return $this;
    }

    /**
     * Dispatch user request
     *
     * @return Mage_Core_Controller_Varien_Front
     */
    public function dispatch()
    {
        $request = $this->getRequest();

        // If pre-configured, check equality of base URL and requested URL
        $this->_checkBaseUrl($request);

        Magento_Profiler::start('dispatch');

        $request->setPathInfo()->setDispatched(false);
        $this->applyRewrites($request);

        Magento_Profiler::stop('dispatch');

        Magento_Profiler::start('routers_match');
        $routingCycleCounter = 0;
        while (!$request->isDispatched() && $routingCycleCounter++ < 100) {
            /** @var $router Mage_Core_Controller_Varien_Router_Abstract */
            foreach ($this->_routers as $router) {
                /** @var $controllerInstance Mage_Core_Controller_Varien_Action */
                $controllerInstance = $router->match($this->getRequest());
                if ($controllerInstance) {
                    $controllerInstance->dispatch($request->getActionName());
                    break;
                }
            }
        }
        Magento_Profiler::stop('routers_match');
        if ($routingCycleCounter > 100) {
            Mage::throwException('Front controller reached 100 router match iterations');
        }
        // This event gives possibility to launch something before sending output (allow cookie setting)
        Mage::dispatchEvent('controller_front_send_response_before', array('front' => $this));
        Magento_Profiler::start('send_response');
        Mage::dispatchEvent('http_response_send_before', array('response' => $this));
        $this->getResponse()->sendResponse();
        Magento_Profiler::stop('send_response');
        Mage::dispatchEvent('controller_front_send_response_after', array('front' => $this));
        return $this;
    }

    /**
     * Apply rewrites to current request
     *
     * @param Mage_Core_Controller_Request_Http $request
     */
    public function applyRewrites(Mage_Core_Controller_Request_Http $request)
    {
        // URL rewrite
        if (!$request->isStraight()) {
            Magento_Profiler::start('db_url_rewrite');
            /** @var $urlRewrite Mage_Core_Model_Url_Rewrite */
            $urlRewrite = $this->_rewriteFactory->create();
            $urlRewrite->rewrite($request);
            Magento_Profiler::stop('db_url_rewrite');
        }

        // config rewrite
        Magento_Profiler::start('config_url_rewrite');
        $this->rewrite($request);
        Magento_Profiler::stop('config_url_rewrite');
    }

    public function getRouterByRoute($routeId)
    {
        // empty route supplied - return base url
        if (empty($routeName)) {
            $router = $this->getRouter('standard');
        } elseif ($this->getRouter('admin') && $this->getRouter('admin')->getFrontNameByRoute($routeId)) {
            // try standard router url assembly
            $router = $this->getRouter('admin');
        } elseif ($this->getRouter('standard')->getFrontNameByRoute($routeId)) {
            // try standard router url assembly
            $router = $this->getRouter('standard');
        } elseif ($router = $this->getRouter($routeId)) {
            // try custom router url assembly
        } else {
            // get default router url
            $router = $this->getRouter('default');
        }

        return $router;
    }

    /**
     * @param string $frontName
     * @return Mage_Core_Controller_Varien_Router_Abstract
     */
    public function getRouterByFrontName($frontName)
    {
        // empty route supplied - return base url
        if (empty($frontName)) {
            $router = $this->getRouter('standard');
        } elseif ($this->getRouter('admin')->getRouteByFrontName($frontName)) {
            // try standard router url assembly
            $router = $this->getRouter('admin');
        } elseif ($this->getRouter('standard')->getRouteByFrontName($frontName)) {
            // try standard router url assembly
            $router = $this->getRouter('standard');
        } elseif ($router = $this->getRouter($frontName)) {
            // try custom router url assembly
        } else {
            // get default router url
            $router = $this->getRouter('default');
        }

        return $router;
    }

    /**
     * Apply configuration rewrites to current url
     *
     * @param Mage_Core_Controller_Request_Http $request
     */
    public function rewrite(Mage_Core_Controller_Request_Http $request = null)
    {
        if (!$request) {
            $request = $this->getRequest();
        }

        $config = Mage::getConfig()->getNode('global/rewrite');
        if (!$config) {
            return;
        }
        foreach ($config->children() as $rewrite) {
            $from = (string)$rewrite->from;
            $to = (string)$rewrite->to;
            if (empty($from) || empty($to)) {
                continue;
            }
            $from = $this->_processRewriteUrl($from);
            $to   = $this->_processRewriteUrl($to);

            $pathInfo = preg_replace($from, $to, $request->getPathInfo());

            if (isset($rewrite->complete)) {
                $request->setPathInfo($pathInfo);
            } else {
                $request->rewritePathInfo($pathInfo);
            }
        }
    }

    /**
     * Replace route name placeholders in url to front name
     *
     * @param   string $url
     * @return  string
     */
    protected function _processRewriteUrl($url)
    {
        $startPos = strpos($url, '{');
        if ($startPos!==false) {
            $endPos = strpos($url, '}');
            $routeId = substr($url, $startPos+1, $endPos-$startPos-1);
            $router = $this->getRouterByRoute($routeId);
            if ($router) {
                $frontName = $router->getFrontNameByRoute($routeId);
                $url = str_replace('{'.$routeId.'}', $frontName, $url);
            }
        }
        return $url;
    }

    /**
     * Auto-redirect to base url (without SID) if the requested url doesn't match it.
     * By default this feature is enabled in configuration.
     *
     * @param Zend_Controller_Request_Http $request
     */
    protected function _checkBaseUrl($request)
    {
        if (!Mage::isInstalled() || $request->getPost() || strtolower($request->getMethod()) == 'post') {
            return;
        }

        $redirectCode = (int)Mage::getStoreConfig('web/url/redirect_to_base');
        if (!$redirectCode) {
            return;
        } elseif ($redirectCode != 301) {
            $redirectCode = 302;
        }

        if ($this->_isAdminFrontNameMatched($request)) {
            return;
        }

        if($this->_isForbiddenForRedirectArea($request)) {

            return;
        }

        $baseUrl = Mage::getBaseUrl(
            Mage_Core_Model_Store::URL_TYPE_WEB,
            Mage::app()->getStore()->isCurrentlySecure()
        );
        if (!$baseUrl) {
            return;
        }

        $uri = @parse_url($baseUrl);
        $requestUri = $request->getRequestUri() ? $request->getRequestUri() : '/';
        if (isset($uri['scheme']) && $uri['scheme'] != $request->getScheme()
            || isset($uri['host']) && $uri['host'] != $request->getHttpHost()
            || isset($uri['path']) && strpos($requestUri, $uri['path']) === false
        ) {
            $redirectUrl = Mage::getSingleton('Mage_Core_Model_Url')->getRedirectUrl(
                Mage::getUrl(ltrim($request->getPathInfo(), '/'), array('_nosid' => true))
            );

            Mage::app()->getFrontController()->getResponse()
                ->setRedirect($redirectUrl, $redirectCode)
                ->sendResponse();
            exit;
        }
    }

    /**
     * Check if requested path starts with one of the admin front names
     *
     * @param Zend_Controller_Request_Http $request
     * @return boolean
     */
    protected function _isAdminFrontNameMatched($request)
    {
        $pathPrefix = $this->_extractPathPrefixFromUrl($request);
        return $pathPrefix == Mage::helper('Mage_Backend_Helper_Data')->getAreaFrontName();
    }

    /**
     * Extract first path part from url (in most cases this is area code)
     *
     * @param Zend_Controller_Request_Http $request
     * @return string
     */
    protected function _extractPathPrefixFromUrl($request)
    {
        $pathPrefix = ltrim($request->getPathInfo(), '/');
        $urlDelimiterPos = strpos($pathPrefix, '/');
        if ($urlDelimiterPos) {
            $pathPrefix = substr($pathPrefix, 0, $urlDelimiterPos);
        }

        return $pathPrefix;
    }

    /**
     * Check is current request may be redirected into base URL
     *
     * @param Zend_Controller_Request_Http $request
     * @return bool
     */
    protected function _isForbiddenForRedirectArea($request)
    {
        $result = false;
        $pathPrefix = $this->_extractPathPrefixFromUrl($request);

        $forbiddenForRedirectAreas = Mage::app()->getConfig()->getNode();
        if ($forbiddenForRedirectAreas) {
            $areasList = $forbiddenForRedirectAreas->asArray();
            foreach ($areasList as $nodeName => $nodeValue)
            {
                if ($nodeName == $pathPrefix) {
                    $result = true;
                    break;
                }
            }
        }
        return $result;
    }
}
