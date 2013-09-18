<?php
/**
 * GiftRegistry configuration data container
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_GiftRegistry_Model_Config_Data extends Magento_Config_Data
{
    /**
     * Scope priority loading scheme
     *
     * @var array
     */
    protected $_scopePriorityScheme = array('global');

    /**
     * @param Magento_GiftRegistry_Model_Config_Reader $reader
     * @param Magento_Config_ScopeInterface $configScope
     * @param Magento_Config_CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        Magento_GiftRegistry_Model_Config_Reader $reader,
        Magento_Config_ScopeInterface $configScope,
        Magento_Config_CacheInterface $cache,
        $cacheId = "giftregistry_config_cache"
    ) {
        parent::__construct($reader, $configScope, $cache, $cacheId);
    }
}
