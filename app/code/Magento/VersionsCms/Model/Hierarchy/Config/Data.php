<?php
/**
 * Cms menu hierarchy configuration data container
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_VersionsCms_Model_Hierarchy_Config_Data extends Magento_Config_Data
{
    /**
     * Scope priority loading scheme
     *
     * @var array
     */
    protected $_scopePriorityScheme = array('global');

    /**
     * @param Magento_VersionsCms_Model_Hierarchy_Config_Reader $reader
     * @param Magento_Config_ScopeInterface $configScope
     * @param Magento_Config_CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        Magento_VersionsCms_Model_Hierarchy_Config_Reader $reader,
        Magento_Config_ScopeInterface $configScope,
        Magento_Config_CacheInterface $cache,
        $cacheId = "menuHierarchyConfigCache"
    ) {
        parent::__construct($reader, $configScope, $cache, $cacheId);
    }
}
