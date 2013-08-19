<?php
/**
 * Default configuration data reader. Reads configuration data from database
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Data_Reader
{
    /**
     * @var Mage_Core_Model_Resource_Config
     */
    protected $_storage;

    /**
     * @var Mage_Core_Model_Config_Data_Converter
     */
    protected $_converter;

    /**
     * @var Mage_Core_Model_Config_Initial
     */
    protected $_initialConfig;

    /**
     * @param Mage_Core_Model_Config_Data_Converter $converter
     * @param Mage_Core_Model_Resource_Config $storage
     * @param Mage_Core_Model_Config_Initial $initialConfig
     */
    public function __construct(
        Mage_Core_Model_Config_Data_Converter $converter,
        Mage_Core_Model_Resource_Config $storage,
        Mage_Core_Model_Config_Initial $initialConfig

    ) {
        $this->_initialConfig = $initialConfig;
        $this->_storage = $storage;
        $this->_converter = $converter;
    }

    public function read($scope = '', $scopeCode = null)
    {
        $initialConfig = array(
            'default' => $this->_initialConfig->getDefault(),
            'stores' => $this->_initialConfig->getStores(),
            'websites' => $this->_initialConfig->getWebsites(),
        );

        $reader = $this->_storage->getReadConnection();

        /** Process websites */
        $select = $reader->select()
            ->from($this->_storage->getTable('core_website'), array('website_id', 'code', 'name'));
        $webSiteData = $reader->fetchAssoc($select);
        $webSiteList = array();
        $websiteConfig = array();
        foreach ($webSiteData as $configValue) {
            $websiteConfig[$configValue['code'] . '/system/website/id'] = $configValue['website_id'];
            $websiteConfig[$configValue['code'] . '/system/website/name'] = $configValue['name'];
            $webSiteList[$configValue['website_id']] = array('code' => $configValue['code']);
        }

        /** Process stores */
        $select = $reader->select()
            ->from($this->_storage->getTable('core_store'), array('store_id', 'code', 'name', 'website_id'))
            ->order('sort_order ' . Magento_DB_Select::SQL_ASC);
        $storesData = $reader->fetchAssoc($select);
        $storeList = array();
        $storeConfig = array();
        foreach ($storesData as $configValue) {
            if (!isset($webSiteList[$configValue['website_id']])) {
                continue;
            }
            $storeConfig[$configValue['code'] . '/system/store/id'] = $configValue['store_id'];
            $storeConfig[$configValue['code'] . '/system/store/name'] = $configValue['name'];
            $storeConfig[$configValue['code'] . '/system/website/id'] = $configValue['website_id'];
            $websiteConfig[$webSiteList[$configValue['website_id']]['code'] . '/system/stores/' . $configValue['code']] = $configValue['store_id'];

            $storeList[$configValue['store_id']] = array('code' => $configValue['code']);
            $webSiteList[$configValue['website_id']][Mage_Core_Model_Config::SCOPE_STORES][$configValue['store_id']] = $configValue['code'];
        }
        $initialConfig['stores'] = array_replace_recursive($initialConfig['stores'], $this->_converter->convert($storeConfig));
        $initialConfig['websites'] = array_replace_recursive($initialConfig['websites'], $this->_converter->convert($websiteConfig));


        /** Process default values from db*/
        $select = $reader->select()
            ->from($this->_storage->getMainTable(), array('scope', 'scope_id', 'path', 'value'));
        $configData = $reader->fetchAll($select);
        $dbDefaultConfig = array();
        foreach ($configData as $configValue) {
            if ($configValue['scope'] !=  Mage_Core_Model_Store::DEFAULT_CODE) {
                continue;
            }
            $path = $configValue['path'];
            $value = $configValue['value'];
            $dbDefaultConfig[$path] = $value;
        }
        $dbDefaultConfig = $this->_converter->convert($dbDefaultConfig);
        $initialConfig['default'] = array_replace_recursive($initialConfig['default'], $dbDefaultConfig);

        /** Process websites config values from database */
        $deleteWebsites = array();
        $dbWebsiteConfig = array();
        foreach ($configData as $configValue) {
            if ($configValue['scope'] !== Mage_Core_Model_Config::SCOPE_WEBSITES) {
                continue;
            }
            $value = $configValue['value'];
            if (isset($webSiteList[$configValue['scope_id']])) {
                $configPath = $webSiteList[$configValue['scope_id']]['code'] . '/' . $configValue['path'];
                $dbWebsiteConfig[$configPath] = $value;
            } else {
                $deleteWebsites[$configValue['scope_id']] = $configValue['scope_id'];
            }
        }
        $dbWebsiteConfig = $this->_converter->convert($dbWebsiteConfig);

        /** Inherit default config values to all websites */
        foreach ($webSiteList as $configValue) {
            $code = $configValue['code'];
            $initialConfig['websites'][$code] = array_replace_recursive($initialConfig['default'], $initialConfig['websites'][$code]);
            if (isset($dbWebsiteConfig[$code])) {
                $initialConfig['websites'][$code] = array_replace_recursive($initialConfig['websites'][$code], $dbWebsiteConfig[$code]);
            }
        }

        /** Extend website config values to all associated stores */
        foreach ($webSiteList as $configValue) {
            $code = $configValue['code'];
            $extendData = $initialConfig['websites'][$code];
            if (isset($configValue[Mage_Core_Model_Config::SCOPE_STORES])) {
                foreach ($configValue[Mage_Core_Model_Config::SCOPE_STORES] as $storeCode) {
                    $initialConfig['stores'][$storeCode] = array_replace_recursive($extendData, $initialConfig['stores'][$storeCode]);
                }
            }
        }

        /** Process stores config values from database */
        $deleteStores = array();
        $dbStoreConfig = array();
        foreach ($configData as $configValue) {
            if ($configValue['scope'] !== Mage_Core_Model_Config::SCOPE_STORES) {
                continue;
            }
            $value = $configValue['value'];
            if (isset($storeList[$configValue['scope_id']])) {
                $path = $storeList[$configValue['scope_id']]['code'] . '/' . $configValue['path'];
                $dbStoreConfig[$path] = $value;
            } else {
                $deleteStores[$configValue['scope_id']] = $configValue['scope_id'];
            }
        }
        $dbStoreConfig = $this->_converter->convert($dbStoreConfig);
        $initialConfig['stores'] = array_replace_recursive($initialConfig['stores'], $dbStoreConfig);


        return $initialConfig;
    }

}
