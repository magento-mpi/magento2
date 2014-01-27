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
     * Use session var instead of SID for session in URL
     *
     * @var bool
     */
    protected $_useSessionVar = false;

    /**
     * Use session in URL flag
     *
     * @see \Magento\UrlInterface
     * @var bool
     */
    protected $_useSessionInUrl = true;

    /**
     * @param \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig
     * @param \Magento\UrlInterface $urlBuilder
     * @param \Magento\App\RequestInterface $request
     * @param array $sidNameMap
     */
    public function __construct(
        \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig,
        \Magento\UrlInterface $urlBuilder,
        \Magento\App\RequestInterface $request,
        array $sidNameMap = array()
    ) {
        $this->coreStoreConfig = $coreStoreConfig;
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->sidNameMap = $sidNameMap;
    }

    /**
     * @param \Magento\Session\SessionManagerInterface $session
     * @return string
     */
    public function getSid(\Magento\Session\SessionManagerInterface $session)
    {
        $sidKey = null;
        if ($this->coreStoreConfig->getConfig(self::XML_PATH_USE_FRONTEND_SID)
            && $this->request->getQuery($this->getSessionIdQueryParam($session), false)
            && $this->urlBuilder->isOwnOriginUrl()
        ) {
            $sidKey = $this->request->getQuery($this->getSessionIdQueryParam($session));
        }
        return $sidKey;
    }

    /**
     * Get session id query param
     *
     * @param \Magento\Session\SessionManagerInterface $session
     * @return string
     */
    public function getSessionIdQueryParam(\Magento\Session\SessionManagerInterface $session)
    {
        $sessionName = $session->getName();
        if ($sessionName && isset($this->sidNameMap[$sessionName])) {
            return $this->sidNameMap[$sessionName];
        }
        return self::SESSION_ID_QUERY_PARAM;
    }

    /**
     * Set use session var instead of SID for URL
     *
     * @param bool $var
     * @return \Magento\Session\SidResolverInterface
     */
    public function setUseSessionVar($var)
    {
        $this->_useSessionVar = (bool)$var;
        return $this;
    }

    /**
     * Retrieve use flag session var instead of SID for URL
     *
     * @return bool
     */
    public function getUseSessionVar()
    {
        return $this->_useSessionVar;
    }

    /**
     * Set Use session in URL flag
     *
     * @param bool $flag
     * @return \Magento\Session\SidResolverInterface
     */
    public function setUseSessionInUrl($flag = true)
    {
        $this->_useSessionInUrl = (bool)$flag;
        return $this;
    }

    /**
     * Retrieve use session in URL flag
     *
     * @return bool
     */
    public function getUseSessionInUrl()
    {
        return $this->_useSessionInUrl;
    }
}
