<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Backend_Model_Config_Structure_Data extends Magento_Config_Data_Scoped
{
    /**
     * @param Magento_Backend_Model_Config_Structure_Reader $reader
     * @param Magento_Config_ScopeInterface $configScope
     * @param Magento_Config_CacheInterface $cache
     * @param $cacheId
     */
    public function __construct(
        Magento_Backend_Model_Config_Structure_Reader $reader,
        Magento_Config_ScopeInterface $configScope,
        Magento_Config_CacheInterface $cache,
        $cacheId
    ) {
        parent::__construct($reader, $configScope, $cache, $cacheId);
    }

    /**
     * Merge additional config
     *
     * @param array $config
     */
    public function merge(array $config)
    {
        if (isset($config['config']['system'])) {
            $config = $config['config']['system'];
        }
        parent::merge($config);
    }
}
