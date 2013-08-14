<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Router_Config_Data extends Magento_Config_Data
{
    /**
     * @param Mage_Core_Model_Router_Config_Reader $reader
     * @param Magento_Config_ScopeInterface $configScope
     * @param Magento_Config_CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        Mage_Core_Model_Router_Config_Reader $reader,
        Magento_Config_ScopeInterface $configScope,
        Magento_Config_CacheInterface $cache,
        $cacheId = 'RouterConfig'
    ) {
        $this->_reader = $reader;
        $this->_configScope = $configScope;
        $this->_cache = $cache;
        $this->_cacheId = $cacheId;
    }
}
