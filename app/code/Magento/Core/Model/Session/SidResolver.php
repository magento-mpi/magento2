<?php
/**
 * SID resolver
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Session;

class SidResolver implements \Magento\Session\SidResolverInterface
{
    /**
     * Config path for flag whether use SID on frontend
     */
    const XML_PATH_USE_FRONTEND_SID = 'web/session/use_frontend_sid';

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Core\Model\Store\ConfigInterface
     */
    protected $coreStoreConfig;

    /**
     * @var \Magento\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\App\RequestInterface
     */
    protected $request;

    /**
     * @var array
     */
    protected $sidNameMap;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig
     * @param \Magento\UrlInterface $urlBuilder
     * @param \Magento\App\RequestInterface $request
     * @param array $sidNameMap
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig,
        \Magento\UrlInterface $urlBuilder,
        \Magento\App\RequestInterface $request,
        array $sidNameMap = array()
    ) {
        $this->storeManager = $storeManager;
        $this->coreStoreConfig = $coreStoreConfig;
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->sidNameMap = $sidNameMap;
    }

    /**
     * @param AbstractSession $session
     * @return string
     */
    public function getSid(AbstractSession $session)
    {
        $sidKey = null;
        if ($this->coreStoreConfig->getConfig(self::XML_PATH_USE_FRONTEND_SID)
            && isset($_GET[$this->getSessionIdQueryParam($session)])
            && $this->urlBuilder->isOwnOriginUrl()
        ) {
            $sidKey = $this->request->getQuery($this->getSessionIdQueryParam($session));
        }
        return $sidKey;
    }

    /**
     * Get session id query param
     *
     * @param AbstractSession $session
     * @return string
     */
    public function getSessionIdQueryParam(AbstractSession $session)
    {
        $sessionName = $session->getName();
        if ($sessionName && isset($this->sidNameMap[$sessionName])) {
            return $this->sidNameMap[$sessionName];
        }
        return self::SESSION_ID_QUERY_PARAM;
    }
}
