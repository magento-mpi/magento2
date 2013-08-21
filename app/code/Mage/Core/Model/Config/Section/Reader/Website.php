<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Core_Model_Config_Section_Reader_Website
{
    /**
     * @var Mage_Core_Model_Config_Initial
     */
    protected $_initialConfig;

    /**
     * @var Mage_Core_Model_Config_SectionPool
     */
    protected $_sectionPool;

    /**
     * @var Mage_Core_Model_Config_Section_Converter
     */
    protected $_converter;

    /**
     * @var Mage_Core_Model_Resource_Config_Value_Collection_ScopedFactory
     */
    protected $_collectionFactory;

    /**
     * @var Mage_Core_Model_WebsiteFactory
     */
    protected $_websiteFactory;

    /**
     * @param Mage_Core_Model_Config_Initial $initialConfig
     * @param Mage_Core_Model_Config_SectionPool $sectionPool
     * @param Mage_Core_Model_Config_Section_Converter $converter
     * @param Mage_Core_Model_Resource_Config_Value_Collection_ScopedFactory $collectionFactory
     * @param Mage_Core_Model_WebsiteFactory $websiteFactory
     */
    public function __construct(
        Mage_Core_Model_Config_Initial $initialConfig,
        Mage_Core_Model_Config_SectionPool $sectionPool,
        Mage_Core_Model_Config_Section_Converter $converter,
        Mage_Core_Model_Resource_Config_Value_Collection_ScopedFactory $collectionFactory,
        Mage_Core_Model_WebsiteFactory $websiteFactory
    ) {
        $this->_initialConfig = $initialConfig;
        $this->_sectionPool = $sectionPool;
        $this->_converter = $converter;
        $this->_collectionFactory = $collectionFactory;
        $this->_websiteFactory = $websiteFactory;
    }

    /**
     * Read configuration by code
     *
     * @param string $code
     * @return array
     */
    public function read($code)
    {
        $website = $this->_websiteFactory->create();
        $website->load($code);
        $collection = $this->_collectionFactory->create(array('scope' => 'website', 'scopeId' => $website->getId()));
        $dbWebsiteConfig = array();
        foreach ($collection as $configValue) {
            $dbWebsiteConfig[$configValue->getPath()] = $configValue->getValue();
        }
        $dbWebsiteConfig = $this->_converter->convert($dbWebsiteConfig);

        $this->_initialConfig->getWebsite($code);
        $merged = array_replace_recursive(
            $this->_sectionPool->getSection('default')->getValue(), $this->_initialConfig->getWebsite($code)
        );
        if (isset($dbWebsiteConfig)) {
            $merged = array_replace_recursive($this->_initialConfig->getWebsite($code), $dbWebsiteConfig);
        }
        return $merged;
    }
}
