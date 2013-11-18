<?php
/**
 * SID url resolver
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
    const XML_PATH_USE_FRONTEND_SID     = 'web/session/use_frontend_sid';

    const SESSION_ID_QUERY_PARAM        = 'SID';

    /**
     * @var \Magento\Core\Model\Session
     */
    protected $session;

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
     * @param \Magento\Core\Model\Session $session
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig
     * @param \Magento\UrlInterface $urlBuilder
     * @param array $sidNameMap
     */
    public function __construct(
        \Magento\Core\Model\Session $session,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig,
        \Magento\UrlInterface $urlBuilder,
        array $sidNameMap = array()
    ) {
        $this->session = $session;
        $this->storeManager = $storeManager;
        $this->coreStoreConfig = $coreStoreConfig;
        $this->urlBuilder = $urlBuilder;
        $this->sidNameMap = $sidNameMap;
    }

    /**
     * @return string
     */
    public function getSid()
    {
        $id = null;
        if (($this->storeManager->getStore()->isAdmin()
            ||
            $this->coreStoreConfig->getConfig(self::XML_PATH_USE_FRONTEND_SID))
            &&
            isset($_GET[$this->getSessionIdQueryParam()])
            &&
            $this->urlBuilder->isOwnOriginUrl()
        ) {
            $id = $_GET[$this->getSessionIdQueryParam()];
        }
        return $id;
    }

    /**
     * Get session id query param
     *
     * @return string
     */
    public function getSessionIdQueryParam()
    {
        $sessionName = $this->session->getSessionName();
        if ($sessionName && isset($this->sidNameMap[$sessionName])) {
            return $this->sidNameMap[$sessionName];
        }
        return self::SESSION_ID_QUERY_PARAM;
    }
}
