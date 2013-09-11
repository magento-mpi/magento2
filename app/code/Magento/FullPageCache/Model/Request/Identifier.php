<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\FullPageCache\Model\Request;

class Identifier
{
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
     * Application scope code
     *
     * @var string
     */
    protected $_scopeCode;

    /**
     * Request id prefix
     */
    const REQUEST_ID_PREFIX = 'REQUEST_';

    /**
     * Request page cache identifier
     *
     * @var string
     */
    protected $_requestCacheId;

    /**
     * Request identifier
     *
     * @var string
     */
    protected $_requestId;

    /**
     * Design info model
     *
     * @var \Magento\FullPageCache\Model\DesignPackage\Info
     */
    protected $_designInfo;

    /**
     * Store identifier model
     *
     * @var \Magento\FullPageCache\Model\Store\Identifier
     */
    protected $_storeIdentifier;

    /**
     * Store id cache key
     *
     * @var string
     */
    protected $_storeKeyCacheKey;

    /**
     * @param \Magento\FullPageCache\Model\Cache $fpcCache
     * @param \Magento\FullPageCache\Model\DesignPackage\Info $designInfo
     * @param \Magento\FullPageCache\Model\Environment $environment
     * @param \Magento\FullPageCache\Model\Store\Identifier $storeIdentifier
     * @param string $scopeCode
     */
    public function __construct(
        \Magento\FullPageCache\Model\Cache $fpcCache,
        \Magento\FullPageCache\Model\DesignPackage\Info $designInfo,
        \Magento\FullPageCache\Model\Environment $environment,
        \Magento\FullPageCache\Model\Store\Identifier $storeIdentifier,
        $scopeCode = ''
    ) {
        $this->_scopeCode = $scopeCode;
        $this->_fpcCache = $fpcCache;
        $this->_environment = $environment;
        $this->_designInfo = $designInfo;
        $this->_storeIdentifier = $storeIdentifier;
        $this->_createRequestIds();
    }

    /**
     * Return current page base url
     *
     * @return string
     */
    protected function _getFullPageUrl()
    {
        /**
         * Define server HTTP HOST
         */
        $uri = $this->_environment->getServer('HTTP_HOST', $this->_environment->getServer('SERVER_NAME', false));

        /**
         * Define request URI
         */
        if ($uri) {
            $iisWasUrlRewritten = $this->_environment->getServer('IIS_WasUrlRewritten');
            $unencodedUrl = $this->_environment->getServer('UNENCODED_URL');
            if ($this->_environment->hasServer('REQUEST_URI')) {
                $uri .= $this->_environment->getServer('REQUEST_URI');
            } elseif (false == empty($iisWasUrlRewritten) && false == empty($unencodedUrl)) {
                $uri .= $unencodedUrl;
            } elseif ($this->_environment->hasServer('ORIG_PATH_INFO')) {
                $uri .= $this->_environment->getServer('ORIG_PATH_INFO');
                $queryString = $this->_environment->getServer('QUERY_STRING');
                if (false == empty($queryString)) {
                    $uri .= $queryString;
                }
            }
        }
        return $uri;
    }

    /**
     * Initialize request ids
     */
    protected function _createRequestIds()
    {
        $uri = $this->_getFullPageUrl();

        //Removing get params
        $pieces = explode('?', $uri);
        $uri = array_shift($pieces);

        /**
         * Define COOKIE state
         */
        if ($uri) {
            $uriParts = array($uri);
            $cookieParams = array(
                'store',
                'currency',
                \Magento\FullPageCache\Model\Cookie::COOKIE_CUSTOMER_GROUP,
                \Magento\FullPageCache\Model\Cookie::COOKIE_CUSTOMER_LOGGED_IN,
                \Magento\FullPageCache\Model\Cookie::CUSTOMER_SEGMENT_IDS,
                \Magento\FullPageCache\Model\Cookie::IS_USER_ALLOWED_SAVE_COOKIE
            );

            foreach ($cookieParams as $paramName) {
                if ($this->_environment->hasCookie($paramName)) {
                    $uriParts[] = $this->_environment->getCookie($paramName);
                }
            }

            $this->_initializeStoreCacheId($uriParts);

            $storeId = $this->_storeIdentifier->getStoreId($this->getStoreCacheId());
            $designPackage = $this->_designInfo->getPackageName($storeId);
            if ($designPackage) {
                $uriParts[] = $designPackage;
            }
            $uri = implode('_', $uriParts);
        }

        $this->_requestId       = $uri;
        $this->_requestCacheId  = $this->prepareCacheId($this->_requestId);
    }

    /**
     * Get page identifier for loading page from cache
     * @return string
     */
    public function getRequestCacheId()
    {
        return $this->_requestCacheId;
    }

    /**
     * Initialize store cache id
     *
     * @param array $uriParts
     */
    protected function _initializeStoreCacheId(array $uriParts)
    {
        $id = implode('_', $uriParts);
        $this->_storeKeyCacheKey = md5($id);
    }

    /**
     * Get store cache id
     *
     * @return string
     */
    public function getStoreCacheId()
    {
        return $this->_storeKeyCacheKey;
    }

    /**
     * Prepare page identifier
     *
     * @param string $id
     * @return string
     */
    public function prepareCacheId($id)
    {
        $cacheId = self::REQUEST_ID_PREFIX . md5($id . $this->_scopeCode);
        return $cacheId;
    }

    /**
     * Get request id
     *
     * @return string
     */
    public function getRequestId()
    {
        return $this->_requestId;
    }

    /**
     * Refresh values of request ids
     *
     * Some parts of $this->_requestId and $this->_requestCacheId might be changed in runtime
     * E.g. we may not know about design package
     * But during cache save we need this data to be actual
     */
    public function refreshRequestIds()
    {
        if (false == $this->_designInfo->isDesignExceptionExistsInCache()) {
            $this->_createRequestIds();
        }
    }
}
