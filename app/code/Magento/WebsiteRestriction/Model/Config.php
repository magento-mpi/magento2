<?php
/**
 * Website restrictions configuration model
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\WebsiteRestriction\Model;

class Config extends \Magento\Config\Data\Scoped implements \Magento\WebsiteRestriction\Model\ConfigInterface
{
    const XML_PATH_RESTRICTION_ENABLED = 'general/restriction/is_active';

    const XML_PATH_RESTRICTION_MODE = 'general/restriction/mode';

    const XML_PATH_RESTRICTION_LANDING_PAGE = 'general/restriction/cms_page';

    const XML_PATH_RESTRICTION_HTTP_STATUS = 'general/restriction/http_status';

    const XML_PATH_RESTRICTION_HTTP_REDIRECT = 'general/restriction/http_redirect';

    /**
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_storeConfig;

    /**
     * Scope priority loading scheme
     *
     * @var string[]
     */
    protected $_scopePriorityScheme = array('global');

    /**
     * @param \Magento\WebsiteRestriction\Model\Config\Reader $reader
     * @param \Magento\Config\ScopeInterface $configScope
     * @param \Magento\Config\CacheInterface $cache
     * @param \Magento\App\Config\ScopeConfigInterface $storeConfig
     * @param string $cacheId
     */
    public function __construct(
        \Magento\WebsiteRestriction\Model\Config\Reader $reader,
        \Magento\Config\ScopeInterface $configScope,
        \Magento\Config\CacheInterface $cache,
        \Magento\App\Config\ScopeConfigInterface $storeConfig,
        $cacheId = 'website_restrictions'
    ) {
        $this->_storeConfig = $storeConfig;
        parent::__construct($reader, $configScope, $cache, $cacheId);
    }

    /**
     * Get generic actions list
     *
     * @return mixed
     */
    public function getGenericActions()
    {
        return $this->get('generic', array());
    }

    /**
     * Get register actions list
     *
     * @return mixed
     */
    public function getRegisterActions()
    {
        return $this->get('register', array());
    }

    /**
     * Define if restriction is active
     *
     * @param \Magento\Store\Model\Store|string|int $store
     * @return bool
     */
    public function isRestrictionEnabled($store = null)
    {
        return (bool)(int)$this->_storeConfig->getValue(
            self::XML_PATH_RESTRICTION_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get restriction mode
     *
     * @return int
     */
    public function getMode()
    {
        return (int)$this->_storeConfig->getValue(
            self::XML_PATH_RESTRICTION_MODE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get restriction HTTP status
     *
     * @return int
     */
    public function getHTTPStatusCode()
    {
        return (int)$this->_storeConfig->getValue(
            self::XML_PATH_RESTRICTION_HTTP_STATUS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get restriction HTTP redirect code
     *
     * @return int
     */
    public function getHTTPRedirectCode()
    {
        return (int)$this->_storeConfig->getValue(
            self::XML_PATH_RESTRICTION_HTTP_REDIRECT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get restriction landing page code
     *
     * @return string
     */
    public function getLandingPageCode()
    {
        return $this->_storeConfig->getValue(
            self::XML_PATH_RESTRICTION_LANDING_PAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
