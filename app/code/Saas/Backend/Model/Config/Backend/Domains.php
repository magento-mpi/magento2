<?php
/**
 * Config source model for available tenant domains
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Backend_Model_Config_Backend_Domains extends Mage_Core_Model_Config_Data
{
    /**
     * Path to default Magento Go domain in configuration
     */
    const XML_DEFAULT_DOMAIN = 'default/web/tenant_domains/default_domain/domain';

    /**
     * Path to customer Magento Go domain in configuration
     */
    const XML_CUSTOM_DOMAIN = 'default/web/tenant_domains/custom_domain/domain';

    /**
     * Path to customer Magento Go domain in configuration SSL availability
     */
    const XML_CUSTOM_SSL = 'default/web/tenant_domains/custom_domain/enabled_ssl';

    /**
     * Unsecure http protocol
     */
    const HTTP = 'http';

    /**
     * Secure https protocol
     */
    const HTTPS = 'https';

    /**
     * Path to current active domain in configuration
     */
    const ACTIVE_DOMAIN_PATH = 'groups/active_domain/fields/active_domain/value';

    /**
     * Main Magento config
     *
     * @var Mage_Core_Model_Config
     */
    protected  $_config;

    /**
     * Config writer (in most cases DB)
     *
     * @var Mage_Core_Model_Config_Storage_WriterInterface
     */
    protected $_configWriter;

    /**
     * Store model, used for access some constants
     *
     * @var Mage_Core_Model_Store
     */
    protected $_storeModel;

    /**
     * Create instance of current class with appropriate parameters
     *
     * @param Mage_Core_Model_Config $config
     * @param Mage_Core_Model_Config_Storage_WriterInterface $configWriter
     * @param Mage_Core_Model_Context $context
     * @param Mage_Core_Model_Resource_Abstract $resource
     * @param Varien_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Config $config,
        Mage_Core_Model_Config_Storage_WriterInterface $configWriter,
        Mage_Core_Model_Context $context,
        Mage_Core_Model_Resource_Abstract $resource = null,
        Varien_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    )
    {
        parent::__construct(
            $context,
            $resource,
            $resourceCollection,
            $data
        );
        $this->_config = $config;
        $this->_configWriter = $configWriter;
    }

    /**
     * After save call
     *
     * @return Saas_Backend_Model_Config_Backend_Domains
     */
    public function afterCommitCallback()
    {
        $domain = $this->sanitizeDomain($this->getDataByPath(self::ACTIVE_DOMAIN_PATH));

        $this->saveDefaultSecureDomain($this->getDefaultDomain());
        $this->saveDefaultUnsecureDomain($this->getDefaultDomain());

        if (!$this->isDefaultDomain($domain)) {
            if ($this->isSslEnabled($this->getCustomDomain())) {
                $this->saveWebsitesSecureDomain($this->getCustomDomain());
            } else {
                $this->saveWebsitesSecureDomain($this->getDefaultDomain());
            }
            $this->saveWebsitesUnsecureDomain($this->getCustomDomain());
        } else {
            $this->saveWebsitesSecureDomain($this->getDefaultDomain());
            $this->saveWebsitesUnsecureDomain($this->getDefaultDomain());
        }

        $this->_config->reinit();

        return $this;
    }

    /**
     * Save domain for secure links (default scope)
     *
     * @param string $domain
     * @return $this
     */
    protected function saveDefaultSecureDomain($domain)
    {
        $secureUrl = $this->formatUrl(self::HTTPS, $domain);
        $this->_configWriter->save(
            Mage_Core_Model_Store::XML_PATH_SECURE_BASE_LINK_URL,
            $secureUrl,
            Mage_Core_Model_Config::SCOPE_DEFAULT
        );
        $this->_configWriter->save(
            Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL,
            $secureUrl,
            Mage_Core_Model_Config::SCOPE_DEFAULT
        );

        return $this;
    }

    /**
     * Save domain for unsecure links (default scope)
     *
     * @param string $domain
     * @return $this
     */
    protected function saveDefaultUnsecureDomain($domain)
    {
        $unsecureUrl = $this->formatUrl(self::HTTP, $domain);
        $this->_configWriter->save(
            Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL,
            $unsecureUrl,
            Mage_Core_Model_Config::SCOPE_DEFAULT
        );
        $this->_configWriter->save(
            Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_LINK_URL,
            $unsecureUrl,
            Mage_Core_Model_Config::SCOPE_DEFAULT
        );
        return $this;
    }

    /**
     * Save domain for secure links (website scope)
     *
     * @param string $domain
     */
    protected function saveWebsitesSecureDomain($domain)
    {
        $secureUrl = $this->formatUrl(self::HTTPS, $domain);
        $this->_configWriter->save(
            Mage_Core_Model_Store::XML_PATH_SECURE_BASE_LINK_URL,
            $secureUrl,
            Mage_Core_Model_Config::SCOPE_WEBSITES,
            1
        );
        $this->_configWriter->save(
            Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL,
            $secureUrl,
            Mage_Core_Model_Config::SCOPE_WEBSITES,
            1
        );
    }

    /**
     * Save domain for unsecure links (websites scope)
     *
     * @param string $domain
     */
    protected function saveWebsitesUnsecureDomain($domain)
    {
        $unsecureUrl   =  $this->formatUrl(self::HTTP, $domain);

        $this->_configWriter->save(
            Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL,
            $unsecureUrl,
            Mage_Core_Model_Config::SCOPE_WEBSITES,
            1
        );
        $this->_configWriter->save(
            Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_LINK_URL,
            $unsecureUrl,
            Mage_Core_Model_Config::SCOPE_WEBSITES,
            1
        );
    }

    /**
     * Format protocol and domain into valid URL
     *
     * @param string $protocol
     * @param string $domain
     * @return string
     */
    public function formatUrl($protocol, $domain)
    {
        $domain = $this->sanitizeDomain($domain);
        return sprintf('%s://%s', $protocol, $domain);
    }

    /**
     * Return true if exists ssl flag for domain
     *
     * @param string $domain
     * @return bool
     */
    protected function isSslEnabled($domain)
    {
        if ($this->isDefaultDomain($domain)) {
            return true;
        }
        $enabledSsl = (bool) $this->_config->getNode(self::XML_CUSTOM_SSL);

        return $enabledSsl;
    }

    /**
     * Get domain part from url
     *
     * @param string $url
     * @return string
     */
    protected function sanitizeDomain($url)
    {
        $url = str_replace(
            array(self::HTTP . ':', self::HTTPS . ':', '//'),
            array('', '', ''),
            $url
        );
        $urlParts = explode('/', $url);
        return !empty($urlParts[0]) ? $urlParts[0] : '';

    }

    /**
     * @param $domain
     * @return bool
     */
    protected function isDefaultDomain($domain)
    {
        return ($this->getDefaultDomain() == $domain);
    }

    /**
     * Retrieve tenant domains
     *
     * @return array
     */
    public function getAvailableDomains()
    {
        $customerDomain = $this->getCustomDomain();
        $goDomain       = $this->getDefaultDomain();
        $domains = array($goDomain => $goDomain);
        if ($customerDomain) {
            $domains[$customerDomain] = $customerDomain;
        }
        return $domains;
    }

    /**
     * Retrieve tenant default domain
     *
     * @return string
     */
    public function getDefaultDomain()
    {
        return (string) $this->_config->getNode(self::XML_DEFAULT_DOMAIN);
    }

    /**
     * Get custom tenant's domain
     *
     * @return string
     */
    public function getCustomDomain()
    {
        return (string) $this->_config->getNode(self::XML_CUSTOM_DOMAIN);
    }
}