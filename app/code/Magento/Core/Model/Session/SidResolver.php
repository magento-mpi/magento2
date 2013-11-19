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
     * @var array
     */
    protected $sidNameMap;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig
     * @param \Magento\UrlInterface $urlBuilder
     * @param array $sidNameMap
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig,
        \Magento\UrlInterface $urlBuilder,
        array $sidNameMap = array()
    ) {
        $this->storeManager = $storeManager;
        $this->coreStoreConfig = $coreStoreConfig;
        $this->urlBuilder = $urlBuilder;
        $this->sidNameMap = $sidNameMap;
    }

    /**
     * @param AbstractSession $session
     * @return string
     */
    public function getSid(AbstractSession $session)
    {
        $id = null;
        if (($this->storeManager->getStore()->isAdmin()
                || $this->coreStoreConfig->getConfig(self::XML_PATH_USE_FRONTEND_SID))
            && isset($_GET[$this->getSessionIdQueryParam($session)])
            && $this->urlBuilder->isOwnOriginUrl()
        ) {
            $id = $_GET[$this->getSessionIdQueryParam($session)];
        }
        return $id;
    }

    /**
     * Get session id query param
     *
     * @param AbstractSession $session
     * @return string
     */
    public function getSessionIdQueryParam(AbstractSession $session)
    {
        $sessionName = $session->getSessionName();
        if ($sessionName && isset($this->sidNameMap[$sessionName])) {
            return $this->sidNameMap[$sessionName];
        }
        return self::SESSION_ID_QUERY_PARAM;
    }
}
