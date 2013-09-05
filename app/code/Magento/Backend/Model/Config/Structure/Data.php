<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Backend_Model_Config_Structure_Data extends \Magento\Config\Data
{
    /**
     * @param Magento_Backend_Model_Config_Structure_Reader $reader
     * @param \Magento\Config\ScopeInterface $configScope
     * @param \Magento\Config\CacheInterface $cache
     * @param $cacheId
     */
    public function __construct(
        Magento_Backend_Model_Config_Structure_Reader $reader,
        \Magento\Config\ScopeInterface $configScope,
        \Magento\Config\CacheInterface $cache,
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
        parent::merge($config['config']['system']);
    }
}
