<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Core_Model_Config_Section_Reader_Store
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
     * @var Magento_Core_Model_Config_Section_Store_Converter
     */
    protected $_converter;

    /**
     * @var Magento_Core_Model_Resource_Config_Value_Collection_ScopedFactory
     */
    protected $_collectionFactory;

    /**
     * @var Magento_Core_Model_StoreFactory
     */
    protected $_storeFactory;

    /**
     * @var Magento_Core_Model_App_State
     */
    protected $_appState;

    /**
     * @param Magento_Core_Model_Config_Initial $initialConfig
     * @param Magento_Core_Model_Config_SectionPool $sectionPool
     * @param Magento_Core_Model_Config_Section_Store_Converter $converter
     * @param Magento_Core_Model_Resource_Config_Value_Collection_ScopedFactory $collectionFactory
     * @param Magento_Core_Model_StoreFactory $storeFactory
     * @param Magento_Core_Model_App_State $appState
     */
    public function __construct(
        Magento_Core_Model_Config_Initial $initialConfig,
        Magento_Core_Model_Config_SectionPool $sectionPool,
        Magento_Core_Model_Config_Section_Store_Converter $converter,
        Magento_Core_Model_Resource_Config_Value_Collection_ScopedFactory $collectionFactory,
        Magento_Core_Model_StoreFactory $storeFactory,
        Magento_Core_Model_App_State $appState
    ) {
        $this->_initialConfig = $initialConfig;
        $this->_sectionPool = $sectionPool;
        $this->_converter = $converter;
        $this->_collectionFactory = $collectionFactory;
        $this->_storeFactory = $storeFactory;
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
        if ($this->_appState->isInstalled()) {
            $store = $this->_storeFactory->create();
            $store->load($code);
            $websiteConfig = $this->_sectionPool->getSection('website', $store->getWebsite()->getCode())->getValue();
            $config = array_replace_recursive($websiteConfig, $this->_initialConfig->getStore($code));

            $collection = $this->_collectionFactory->create(array('scope' => 'stores', 'scopeId' => $store->getId()));
            $dbStoreConfig = array();
            foreach ($collection as $item) {
                $dbStoreConfig[$item->getPath()] = $item->getValue();
            }
            $config = $this->_converter->convert($dbStoreConfig, $config);
        } else {
            $websiteConfig = $this->_sectionPool->getSection('website', 'default')->getValue();
            $config = $this->_converter->convert($websiteConfig, $this->_initialConfig->getStore($code));
        }
        return $config;
    }
} 
