<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Core_Model_Config_Section_Reader_Website
{
    /**
     * @var Magento_Core_Model_Config_Initial
     */
    protected $_initialConfig;

    /**
     * @var Magento_Core_Model_Config_SectionPool
     */
    protected $_sectionPool;

    /**
     * @var Magento_Core_Model_Config_Section_Converter
     */
    protected $_converter;

    /**
     * @var Magento_Core_Model_Resource_Config_Value_Collection_ScopedFactory
     */
    protected $_collectionFactory;

    /**
     * @var Magento_Core_Model_WebsiteFactory
     */
    protected $_websiteFactory;

    /**
     * @var Magento_Core_Model_App_State
     */
    protected $_appState;

    /**
     * @param Magento_Core_Model_Config_Initial $initialConfig
     * @param Magento_Core_Model_Config_SectionPool $sectionPool
     * @param Magento_Core_Model_Config_Section_Converter $converter
     * @param Magento_Core_Model_Resource_Config_Value_Collection_ScopedFactory $collectionFactory
     * @param Magento_Core_Model_WebsiteFactory $websiteFactory
     * @param Magento_Core_Model_App_State $appState
     */
    public function __construct(
        Magento_Core_Model_Config_Initial $initialConfig,
        Magento_Core_Model_Config_SectionPool $sectionPool,
        Magento_Core_Model_Config_Section_Converter $converter,
        Magento_Core_Model_Resource_Config_Value_Collection_ScopedFactory $collectionFactory,
        Magento_Core_Model_WebsiteFactory $websiteFactory,
        Magento_Core_Model_App_State $appState
    ) {
        $this->_initialConfig = $initialConfig;
        $this->_sectionPool = $sectionPool;
        $this->_converter = $converter;
        $this->_collectionFactory = $collectionFactory;
        $this->_websiteFactory = $websiteFactory;
        $this->_appState = $appState;
    }

    /**
     * Read configuration by code
     *
     * @param string $code
     * @return array
     */
    public function read($code)
    {
        $config = array_replace_recursive(
            $this->_sectionPool->getSection('default')->getValue(), $this->_initialConfig->getWebsite($code)
        );

        if ($this->_appState->isInstalled()) {
            $website = $this->_websiteFactory->create();
            $website->load($code);
            $collection = $this->_collectionFactory->create(array(
                'scope' => 'websites', 'scopeId' => $website->getId())
            );
            $dbWebsiteConfig = array();
            foreach ($collection as $configValue) {
                $dbWebsiteConfig[$configValue->getPath()] = $configValue->getValue();
            }
            $dbWebsiteConfig = $this->_converter->convert($dbWebsiteConfig);

            if (count($dbWebsiteConfig)) {
                $config = array_replace_recursive($config, $dbWebsiteConfig);
            }
        }
        return $config;
    }
}
