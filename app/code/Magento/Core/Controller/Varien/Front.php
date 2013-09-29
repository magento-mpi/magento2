<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Controller\Varien;

class Front extends \Magento\Object implements \Magento\Core\Controller\FrontInterface
{
    /**
     * @var \Magento\Core\Model\Url\RewriteFactory
     */
    protected $_rewriteFactory;

    /**
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @var array
     */
    protected $_defaults = array();

    /**
     * @var \Magento\Core\Model\RouterList
     */
    protected $_routerList;

    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_coreConfig;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $_backendData;

    /**
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\Core\Model\Url\RewriteFactory $rewriteFactory
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\RouterList $routerList
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\Config $coreConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Core\Model\Url\RewriteFactory $rewriteFactory,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\RouterList $routerList,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\Config $coreConfig,
        array $data = array()
    ) {
        parent::__construct($data);

        $this->_backendData = $backendData;
        $this->_rewriteFactory = $rewriteFactory;
        $this->_eventManager = $eventManager;
        $this->_routerList = $routerList;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_coreConfig = $coreConfig;
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
     * @return \Magento\Core\Controller\Request\Http
     */
    public function getRequest()
    {
        return \Mage::app()->getRequest();
    }

    /**
     * Retrieve response object
     *
     * @return \Magento\Core\Controller\Response\Http
     */
    public function getResponse()
    {
        return \Mage::app()->getResponse();
    }

    /**
     * Get routerList model
     *
     * @return \Magento\Core\Model\RouterList
     */
    public function getRouterList()
    {
        return $this->_routerList;
    }

    /**
     * Retrieve router by name
     *
     * @param   string $name
     * @return  \Magento\Core\Controller\Varien\Router\AbstractRouter
     */
    public function getRouter($name)
    {
        $routers = $this->_routerList->getRouters();
        if (isset($routers[$name])) {
            return $routers[$name];
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
        return $this->_routerList->getRouters();
    }

    /**
     * Dispatch user request
     *
     * @return \Magento\Core\Controller\Varien\Front
     */
    public function dispatch()
    {
        $request = $this->getRequest();

        // If pre-configured, check equality of base URL and requested URL
        $this->_checkBaseUrl($request);

        \Magento\Profiler::start('dispatch');

        $request->setPathInfo()->setDispatched(false);
        $this->applyRewrites($request);

        \Magento\Profiler::stop('dispatch');

        \Magento\Profiler::start('routers_match');
        $routingCycleCounter = 0;
        while (!$request->isDispatched() && $routingCycleCounter++ < 100) {
            /** @var $router \Magento\Core\Controller\Varien\Router\AbstractRouter */
            foreach ($this->_routerList->getRouters() as $router) {
                $router->setFront($this);

                /** @var $controllerInstance \Magento\Core\Controller\Varien\Action */
                $controllerInstance = $router->match($this->getRequest());
                if ($controllerInstance) {
                    $controllerInstance->dispatch($request->getActionName());
                    break;
                }
            }
        }
        \Magento\Profiler::stop('routers_match');
        if ($routingCycleCounter > 100) {
            \Mage::throwException('Front controller reached 100 router match iterations');
        }
        // This event gives possibility to launch something before sending output (allow cookie setting)
        $this->_eventManager->dispatch('controller_front_send_response_before', array('front' => $this));
        \Magento\Profiler::start('send_response');
        $this->_eventManager->dispatch('http_response_send_before', array('response' => $this));
        $this->getResponse()->sendResponse();
        \Magento\Profiler::stop('send_response');
        $this->_eventManager->dispatch('controller_front_send_response_after', array('front' => $this));
        return $this;
    }

    /**
     * Apply rewrites to current request
     *
     * @param \Magento\Core\Controller\Request\Http $request
     */
    public function applyRewrites(\Magento\Core\Controller\Request\Http $request)
    {
        // URL rewrite
        if (!$request->isStraight()) {
            \Magento\Profiler::start('db_url_rewrite');
            /** @var $urlRewrite \Magento\Core\Model\Url\Rewrite */
            $urlRewrite = $this->_rewriteFactory->create();
            $urlRewrite->rewrite($request);
            \Magento\Profiler::stop('db_url_rewrite');
        }

        // config rewrite
        \Magento\Profiler::start('config_url_rewrite');
        $this->rewrite($request);
        \Magento\Profiler::stop('config_url_rewrite');
    }

    /**
     * Apply configuration rewrites to current url
     *
     * @param \Magento\Core\Controller\Request\Http $request
     */
    public function rewrite(\Magento\Core\Controller\Request\Http $request = null)
    {
        if (!$request) {
            $request = $this->getRequest();
        }

        $config = $this->_coreConfig->getNode('global/rewrite');
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
            $router = $this->_routerList->getRouterByRoute($routeId);
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
     * @param \Zend_Controller_Request_Http $request
     */
    protected function _checkBaseUrl($request)
    {
        if (!\Mage::isInstalled() || $request->getPost() || strtolower($request->getMethod()) == 'post') {
            return;
        }

        $redirectCode = (int)$this->_coreStoreConfig->getConfig('web/url/redirect_to_base');
        if (!$redirectCode) {
            return;
        } elseif ($redirectCode != 301) {
            $redirectCode = 302;
        }

        if ($this->_isAdminFrontNameMatched($request)) {
            return;
        }

        $baseUrl = \Mage::getBaseUrl(
            \Magento\Core\Model\Store::URL_TYPE_WEB,
            \Mage::app()->getStore()->isCurrentlySecure()
        );
        if (!$baseUrl) {
            return;
        }

        $uri = parse_url($baseUrl);
        $requestUri = $request->getRequestUri() ? $request->getRequestUri() : '/';
        if (isset($uri['scheme']) && $uri['scheme'] != $request->getScheme()
            || isset($uri['host']) && $uri['host'] != $request->getHttpHost()
            || isset($uri['path']) && strpos($requestUri, $uri['path']) === false
        ) {
            $redirectUrl = \Mage::getSingleton('Magento\Core\Model\Url')->getRedirectUrl(
                \Mage::getUrl(ltrim($request->getPathInfo(), '/'), array('_nosid' => true))
            );

            \Mage::app()->getFrontController()->getResponse()
                ->setRedirect($redirectUrl, $redirectCode)
                ->sendResponse();
            exit;
        }
    }

    /**
     * Check if requested path starts with one of the admin front names
     *
     * @param \Zend_Controller_Request_Http $request
     * @return boolean
     */
    protected function _isAdminFrontNameMatched($request)
    {
        $pathPrefix = $this->_extractPathPrefixFromUrl($request);
        return $pathPrefix == $this->_backendData->getAreaFrontName();
    }

    /**
     * Extract first path part from url (in most cases this is area code)
     *
     * @param \Zend_Controller_Request_Http $request
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
}
