<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\FullPageCache\Model;

class Processor implements \Magento\FullPageCache\Model\RequestProcessorInterface
{
    const XML_PATH_ALLOWED_DEPTH        = 'system/page_cache/allowed_depth';
    const XML_PATH_CACHE_MULTICURRENCY  = 'system/page_cache/multicurrency';
    const XML_PATH_CACHE_DEBUG          = 'system/page_cache/debug';
    const CACHE_TAG                     = \Magento\FullPageCache\Model\Cache\Type::CACHE_TAG;

    const CACHE_SIZE_KEY                = 'FPC_CACHE_SIZE_CAHCE_KEY';
    const XML_PATH_CACHE_MAX_SIZE       = 'system/page_cache/max_cache_size';

    /**
     * Cache tags related with request
     * @var array
     */
    protected $_requestTags;

    /**
     * Request processor model
     * @var mixed
     */
    protected $_requestProcessor = null;

    /**
     * subProcessor model
     *
     * @var \Magento\FullPageCache\Model\Cache\SubProcessorInterface
     */
    protected $_subProcessor;

    /**
     * Page cache processor restriction model
     *
     * @var \Magento\FullPageCache\Model\Processor\RestrictionInterface
     */
    protected $_restriction;

    /**
     * SubProcessor factory
     *
     * @var \Magento\FullPageCache\Model\Cache\SubProcessorFactory
     */
    protected $_subProcessorFactory;

    /**
     * Placeholder factory
     *
     * @var \Magento\FullPageCache\Model\Container\PlaceholderFactory
     */
    protected $_placeholderFactory;

    /**
     * Container factory
     *
     * @var \Magento\FullPageCache\Model\ContainerFactory
     */
    protected $_containerFactory;

    /**
     * FPC cache model
     * @var \Magento\FullPageCache\Model\Cache
     */
    protected $_fpcCache;

    /**
     * Application environment
     *
     * @var \Magento\FullPageCache\Model\Environment
     */
    protected $_environment;

    /**
     * Request identifier model
     *
     * @var \Magento\FullPageCache\Model\Request\Identifier
     */
    protected $_requestIdentifier;

    /**
     * Design info model
     *
     * @var \Magento\FullPageCache\Model\DesignPackage\Info
     */
    protected $_designInfo;

    /**
     * Metadata storage model
     *
     * @var \Magento\FullPageCache\Model\Metadata
     */
    protected $_metadata;

    /**
     * Store id identifier model
     *
     * @var \Magento\FullPageCache\Model\Store\Identifier
     */
    protected $_storeIdentifier;

    /**
     * Store manager model
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager = null;

    /**
     * @param \Magento\Event\ManagerInterface $eventManager
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_registry = null;

    /**
     * @var \Magento\App\Cache\TypeListInterface
     */
    protected $_typeList;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_coreConfig;

    /**
     * @var \Magento\Core\Model\Session
     */
    protected $_coreSession;

    /**
     * @var \Magento\FullPageCache\Model\Cookie
     */
    protected $_fpcCookie;

    /**
     * @var \Magento\FullPageCache\Helper\Url
     */
    protected $_urlHelper;

    /**
     * @var \Magento\FullPageCache\Model\Observer
     */
    protected $_fpcObserverFactory;

    /**
     * @var array
     */
    protected $_allowedCache = array();

    /**
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param Processor\RestrictionInterface $restriction
     * @param Cache $fpcCache
     * @param Cache\SubProcessorFactory $subProcessorFactory
     * @param Container\PlaceholderFactory $placeholderFactory
     * @param ContainerFactory $containerFactory
     * @param Environment $environment
     * @param Request\Identifier $requestIdentifier
     * @param DesignPackage\Info $designInfo
     * @param Metadata $metadata
     * @param Store\Identifier $storeIdentifier
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\App\Cache\TypeListInterface $typeList
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param Cookie $fpcCookie
     * @param \Magento\Core\Model\Session $coreSession
     * @param \Magento\FullPageCache\Helper\Url $urlHelper
     * @param ObserverFactory $fpcObserverFactory
     * @param array $allowedCache
     */
    public function __construct(
        \Magento\Event\ManagerInterface $eventManager,
        Processor\RestrictionInterface $restriction,
        Cache $fpcCache,
        Cache\SubProcessorFactory $subProcessorFactory,
        Container\PlaceholderFactory $placeholderFactory,
        ContainerFactory $containerFactory,
        Environment $environment,
        Request\Identifier $requestIdentifier,
        DesignPackage\Info $designInfo,
        Metadata $metadata,
        Store\Identifier $storeIdentifier,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Registry $registry,
        \Magento\App\Cache\TypeListInterface $typeList,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        Cookie $fpcCookie,
        \Magento\Core\Model\Session $coreSession,
        \Magento\FullPageCache\Helper\Url $urlHelper,
        ObserverFactory $fpcObserverFactory,
        array $allowedCache = array()
    ) {
        $this->_eventManager = $eventManager;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_registry = $registry;
        $this->_containerFactory = $containerFactory;
        $this->_placeholderFactory = $placeholderFactory;
        $this->_subProcessorFactory = $subProcessorFactory;
        $this->_restriction = $restriction;
        $this->_fpcCache = $fpcCache;
        $this->_environment = $environment;
        $this->_designInfo = $designInfo;
        $this->_requestIdentifier = $requestIdentifier;
        $this->_metadata = $metadata;
        $this->_storeIdentifier = $storeIdentifier;
        $this->_storeManager = $storeManager;
        $this->_typeList = $typeList;
        $this->_requestTags = array(self::CACHE_TAG);
        $this->_fpcCookie = $fpcCookie;
        $this->_coreSession = $coreSession;
        $this->_urlHelper = $urlHelper;
        $this->_fpcObserverFactory = $fpcObserverFactory;
        $this->_allowedCache = $allowedCache;
    }


    /**
     * Get HTTP request identifier
     *
     * @return string
     */
    public function getRequestId()
    {
        return $this->_requestIdentifier->getRequestId();
    }

    /**
     * Get page identifier for loading page from cache
     *
     * @return string
     */
    public function getRequestCacheId()
    {
        return $this->_requestIdentifier->getRequestCacheId();
    }

    /**
     * Check if processor is allowed for current HTTP request.
     * Disable processing HTTPS requests and requests with "NO_CACHE" cookie
     *
     * @return bool
     */
    public function isAllowed()
    {
        return $this->_restriction->isAllowed($this->_requestIdentifier->getRequestId());
    }

    /**
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\App\ResponseInterface $response
     * @param string $content
     * @return bool
     */
    public function extractContent(
        \Magento\App\RequestInterface $request,
        \Magento\App\ResponseInterface $response,
        $content
    ) {

        if (!$this->_designInfo->isDesignExceptionExistsInCache()) {
            return false;
        }

        if (!$content && $this->isAllowed()) {
            $subProcessorClass = $this->_metadata->getMetadata('cache_subprocessor');
            if (!$subProcessorClass) {
                return $content;
            }

            /*
             * @var \Magento\FullPageCache\Model\Processor\DefaultProcessor
             */
            $subProcessor = $this->_subProcessorFactory->create($subProcessorClass);
            $this->setSubprocessor($subProcessor);
            $cacheId = $this->_requestIdentifier->prepareCacheId($subProcessor->getPageIdWithoutApp($this));

            $content = $this->_fpcCache->load($cacheId);

            if ($content) {
                $content = $this->_processContent($this->_compressContent($content), $request);

                $this->_restoreResponseHeaders($response);

                $this->_updateRecentlyViewedProducts();
            }
        }

        return $content;
    }

    /**
     * Renew recently viewed products
     */
    protected function _updateRecentlyViewedProducts()
    {
        $productId = $this->_fpcCache->load($this->getRequestCacheId() . '_current_product_id');
        $countLimit = $this->_fpcCache->load($this->getRecentlyViewedCountCacheId());
        if ($productId && $countLimit) {
            \Magento\FullPageCache\Model\Cookie::registerViewedProducts($productId, $countLimit);
        }
    }

    /**
     * Restore response headers
     *
     * @param \Magento\App\ResponseInterface $response
     */
    protected function _restoreResponseHeaders(\Magento\App\ResponseInterface $response)
    {
        $responseHeaders = $this->_metadata->getMetadata('response_headers');
        if (is_array($responseHeaders)) {
            foreach ($responseHeaders as $header) {
                $response->setHeader($header['name'], $header['value'], $header['replace']);
            }
        }
    }

    /**
     * Compress content if possible
     *
     * @param string $content
     * @return string
     */
    protected function _compressContent($content)
    {
        if (function_exists('gzuncompress')) {
            $content = gzuncompress($content);
            return $content;
        }
        return $content;
    }

    /**
     * Retrieve recently viewed count cache identifier
     *
     * @return string
     */
    public function getRecentlyViewedCountCacheId()
    {
        $cookieName = \Magento\Core\Model\Store::COOKIE_NAME;
        $additional = $this->_environment->hasCookie($cookieName) ?
            '_' . $this->_environment->getCookie($cookieName) :
            '';
        return 'recently_viewed_count' . $additional;
    }

    /**
     * Retrieve session info cache identifier
     *
     * @return string
     */
    public function getSessionInfoCacheId()
    {
        $cookieName = \Magento\Core\Model\Store::COOKIE_NAME;
        $additional = $this->_environment->hasCookie($cookieName) ?
            '_' . $this->_environment->getCookie($cookieName) :
            '';
        return 'full_page_cache_session_info' . $additional;
    }

    /**
     * Determine and process all defined containers.
     * Direct request to pagecache/request/process action if necessary for additional processing
     *
     * @param string $content
     * @param \Zend_Controller_Request_Http $request
     * @return string|bool
     */
    protected function _processContent($content, \Zend_Controller_Request_Http $request)
    {
        $containers = $this->_processContainers($content);
        $isProcessed = empty($containers);
        // renew session cookie
        $sessionInfo = $this->_fpcCache->load($this->getSessionInfoCacheId());

        if ($sessionInfo) {
            $sessionInfo = unserialize($sessionInfo);
            foreach ($sessionInfo as $cookieName => $cookieInfo) {
                if ($this->_environment->hasCookie($cookieName) && isset($cookieInfo['lifetime'])
                    && isset($cookieInfo['path']) && isset($cookieInfo['domain'])
                    && isset($cookieInfo['secure']) && isset($cookieInfo['httponly'])
                ) {
                    $lifeTime = (0 == $cookieInfo['lifetime']) ? 0 : time() + $cookieInfo['lifetime'];
                    setcookie($cookieName, $this->_environment->getCookie($cookieName), $lifeTime,
                        $cookieInfo['path'], $cookieInfo['domain'],
                        $cookieInfo['secure'], $cookieInfo['httponly']
                    );
                }
            }
        } else {
            $isProcessed = false;
        }

        /**
         * restore session_id in content whether content is completely processed or not
         */
        $sidCookieName  = $this->_metadata->getMetadata('sid_cookie_name');
        $sidCookieValue = $this->_environment->getCookie($sidCookieName, '');
        \Magento\FullPageCache\Helper\Url::restoreSid($content, htmlspecialchars($sidCookieValue, ENT_QUOTES));

        if ($isProcessed) {
            return $content;
        } else {
            $this->_registry->register('cached_page_content', $content);
            $this->_registry->register('cached_page_containers', $containers);
            $request->setModuleName('pagecache')
                ->setControllerName('request')
                ->setActionName('process')
                ->isStraight(true);

            // restore original routing info
            $routingInfo = array(
                'aliases'              => $this->_metadata->getMetadata('routing_aliases'),
                'requested_route'      => $this->_metadata->getMetadata('routing_requested_route'),
                'requested_controller' => $this->_metadata->getMetadata('routing_requested_controller'),
                'requested_action'     => $this->_metadata->getMetadata('routing_requested_action')
            );

            $request->setRoutingInfo($routingInfo);
            return false;
        }
    }

    /**
     * Process Containers
     *
     * @param $content
     * @return \Magento\FullPageCache\Model\ContainerInterface[]
     */
    protected function _processContainers(&$content)
    {
        $placeholders = array();
        preg_match_all(
            \Magento\FullPageCache\Model\Container\Placeholder::HTML_NAME_PATTERN,
            $content, $placeholders, PREG_PATTERN_ORDER
        );
        $placeholders = array_unique($placeholders[1]);
        $containers = array();
        foreach ($placeholders as $definition) {
            $placeholder = $this->_placeholderFactory->create($definition);
            $container = $placeholder->getContainerClass();
            if (!$container) {
                continue;
            }
            $arguments = array('placeholder' => $placeholder);
            $container = $this->_containerFactory->create($container, $arguments);
            $container->setProcessor($this);
            if (!$container->applyWithoutApp($content)) {
                $containers[] = $container;
            } else {
                preg_match($placeholder->getPattern(), $content, $matches);
                if (array_key_exists(1, $matches)) {
                    $containers = array_merge($this->_processContainers($matches[1]), $containers);
                    $content = preg_replace($placeholder->getPattern(), str_replace('$', '\\$', $matches[1]), $content);
                }
            }
        }
        return $containers;
    }

    /**
     * Associate tag with page cache request identifier
     *
     * @param array|string $tag
     * @return \Magento\FullPageCache\Model\Processor
     */
    public function addRequestTag($tag)
    {
        if (is_array($tag)) {
            $this->_requestTags = array_merge($this->_requestTags, $tag);
        } else {
            $this->_requestTags[] = $tag;
        }
        return $this;
    }

    /**
     * Get cache request associated tags
     * @return array
     */
    public function getRequestTags()
    {
        return $this->_requestTags;
    }

    /**
     * Process response body by specific request
     *
     * @param \Zend_Controller_Request_Http $request
     * @param \Magento\App\ResponseInterface $response
     * @return \Magento\FullPageCache\Model\Processor
     */
    public function processRequestResponse(
        \Zend_Controller_Request_Http $request,
        \Magento\App\ResponseInterface $response
    ) {
        /**
         * Basic validation for request processing
         */
        if ($this->canProcessRequest($request)) {
            $processor = $this->getRequestProcessor($request);
            if ($processor && $processor->allowCache($request)) {
                $this->_metadata->setMetadata('cache_subprocessor', get_class($processor));

                $cacheId = $this->_requestIdentifier->prepareCacheId($processor->getPageIdInApp($this));
                $content = $processor->prepareContent($response);

                /**
                 * Replace all occurrences of session_id with unique marker
                 */
                $this->_urlHelper->replaceSid($content);

                if (function_exists('gzcompress')) {
                    $content = gzcompress($content);
                }

                $contentSize = strlen($content);
                $currentStorageSize = (int) $this->_fpcCache->load(self::CACHE_SIZE_KEY);

                $maxSizeInBytes = $this->_coreStoreConfig->getConfig(self::XML_PATH_CACHE_MAX_SIZE) * 1024 * 1024;

                if ($currentStorageSize >= $maxSizeInBytes) {
                    $this->_typeList->invalidate('full_page');
                    return $this;
                }

                $this->_fpcCache->save($content, $cacheId, $this->getRequestTags());

                $this->_fpcCache->save(
                    $currentStorageSize + $contentSize,
                    self::CACHE_SIZE_KEY,
                    $this->getRequestTags()
                );

                $this->_storeIdentifier->save(
                    $this->_storeManager->getStore()->getId(),
                    $this->_requestIdentifier->getStoreCacheId(),
                    $this->getRequestTags()
                );

                // save response headers
                $this->_metadata->setMetadata('response_headers', $response->getHeaders());

                // save original routing info
                $this->_metadata->setMetadata('routing_aliases', $request->getAliases());
                $this->_metadata->setMetadata('routing_requested_route', $request->getRequestedRouteName());
                $this->_metadata->setMetadata('routing_requested_controller', $request->getRequestedControllerName());
                $this->_metadata->setMetadata('routing_requested_action', $request->getRequestedActionName());

                $this->_metadata->setMetadata('sid_cookie_name', $this->_coreSession->getName());

                $this->_metadata->saveMetadata($this->getRequestTags());
            }

            if ($this->_environment->hasQuery(\Magento\Core\Model\Session\SidResolver::SESSION_ID_QUERY_PARAM)) {
                $this->_fpcCookie->updateCustomerCookies();
                $this->_fpcObserverFactory->create()->updateCustomerProductIndex();
            }
        }
        return $this;
    }

    /**
     * Do basic validation for request to be cached
     *
     * @param \Zend_Controller_Request_Http $request
     * @return bool
     */
    public function canProcessRequest(\Zend_Controller_Request_Http $request)
    {
        $output = $this->isAllowed();

        if ($output) {
            $maxDepth = $this->_coreStoreConfig->getConfig(self::XML_PATH_ALLOWED_DEPTH);
            $queryParams = $request->getQuery();
            unset($queryParams[\Magento\FullPageCache\Model\Cache::REQUEST_MESSAGE_GET_PARAM]);
            $output = count($queryParams) <= $maxDepth;
        }
        if ($output) {
            $multiCurrency = $this->_coreStoreConfig->getConfig(self::XML_PATH_CACHE_MULTICURRENCY);
            $currency = $this->_environment->getCookie('currency');
            if (!$multiCurrency && !empty($currency)) {
                $output = false;
            }
        }
        return $output;
    }

    /**
     * Get specific request processor based on request parameters.
     *
     * @param \Zend_Controller_Request_Http $request
     * @return \Magento\FullPageCache\Model\Processor\DefaultProcessor
     */
    public function getRequestProcessor(\Zend_Controller_Request_Http $request)
    {
        if ($this->_requestProcessor === null) {
            $this->_requestProcessor = false;
            $module = $request->getModuleName();
            if (isset($this->_allowedCache[$module])) {
                $model = $this->_allowedCache[$module];
                $controller = $request->getControllerName();
                if (is_array($this->_allowedCache[$module]) && isset($this->_allowedCache[$module][$controller])) {
                    $model = $this->_allowedCache[$module][$controller];
                    $action = $request->getActionName();
                    if (is_array($this->_allowedCache[$module][$controller])
                            && isset($this->_allowedCache[$module][$controller][$action])) {
                        $model = $this->_allowedCache[$module][$controller][$action];
                    }
                }
                if (is_string($model)) {
                    $this->_requestProcessor = $this->_subProcessorFactory->create($model);
                }
            }
        }
        return $this->_requestProcessor;
    }

    /**
     * Set metadata value for specified key
     *
     * @param string $key
     * @param string $value
     *
     * @return \Magento\FullPageCache\Model\Processor
     */
    public function setMetadata($key, $value)
    {
        $this->_metadata->setMetadata($key, $value);
        return $this;
    }

    /**
     * Get metadata value for specified key
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getMetadata($key)
    {
        return $this->_metadata->getMetadata($key);
    }

    /**
     * Set subprocessor
     *
     * @param \Magento\FullPageCache\Model\Cache\SubProcessorInterface $subProcessor
     */
    public function setSubprocessor(\Magento\FullPageCache\Model\Cache\SubProcessorInterface $subProcessor)
    {
        $this->_subProcessor = $subProcessor;
    }

    /**
     * Get subprocessor
     *
     * @return \Magento\FullPageCache\Model\Cache\SubProcessorInterface
     */
    public function getSubprocessor()
    {
        return $this->_subProcessor;
    }
}
