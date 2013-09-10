<?php
/**
 * Placeholder configuration model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_FullPageCache_Model_Placeholder_Config
    extends Magento_Config_Data
    implements Magento_FullPageCache_Model_Placeholder_ConfigInterface
{
    /**
     * Scope priority loading scheme
     *
     * @var array
     */
    protected $_scopePriorityScheme = array('global');

    /**
     * @param Magento_FullPageCache_Model_Placeholder_Config_Reader $reader
     * @param Magento_Config_ScopeInterface $configScope
     * @param Magento_Config_CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        Magento_FullPageCache_Model_Placeholder_Config_Reader $reader,
        Magento_Config_ScopeInterface $configScope,
        Magento_Config_CacheInterface $cache,
        $cacheId = 'placeholders_config'
    ) {
        parent::__construct($reader, $configScope, $cache, $cacheId);
    }

    /**
     * * Get placeholders config by block instance name
     *
     * @param string $name
     * @return array
     */
    public function getPlaceholders($name)
    {
        return $this->get($name, array());
    }
}
