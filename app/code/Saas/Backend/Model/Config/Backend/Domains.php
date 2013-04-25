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
     * @return Saas_Saas_Model_Adminhtml_System_Config_Backend_Domains
     */
    public function afterCommitCallback()
    {
        $data = $this->getData();
        $domain = $data['groups']['active_domain']['fields']['active_domain']['value'];

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

        return $this;
    }

    /**
     * Save domain for secure links (default scope)
     *
     * @param string $domain
     */
    public function saveDefaultSecureDomain($domain)
    {
        $secureUrl   =  'https://' . $domain . '/';
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
    }

    /**
     * Save domain for unsecure links (default scope)
     *
     * @param string $domain
     */
    public function saveDefaultUnsecureDomain($domain)
    {
        $unsecureUrl = 'http://' . $domain . '/';
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
    }

    /**
     * Save domain for secure links (website scope)
     *
     * @param string $domain
     */
    public function saveWebsitesSecureDomain($domain)
    {
        $secureUrl   =  'https://' . $domain . '/';
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
    public function saveWebsitesUnsecureDomain($domain)
    {
        $unsecureUrl   =  'http://' . $domain . '/';
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