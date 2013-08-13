<?php
/**
 * Event configuration data container
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Event_Config_Data extends Magento_Config_Data
{
    /**
     * @param Mage_Core_Model_Event_Config_Reader $reader
     * @param Magento_Config_ScopeInterface $configScope
     * @param Magento_Config_CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        Mage_Core_Model_Event_Config_Reader $reader,
        Magento_Config_ScopeInterface $configScope,
        Magento_Config_CacheInterface $cache,
        $cacheId = "event_config_cache"
    ) {
        parent::__construct($reader, $configScope, $cache, $cacheId);
    }
}
