<?php
/**
 * Indexer configuration model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Index_Model_Indexer_Config extends Magento_Config_Data_Scoped
    implements Magento_Index_Model_Indexer_ConfigInterface
{
    /**
     * Scope priority loading scheme
     *
     * @var array
     */
    protected $_scopePriorityScheme = array('global');

    /**
     * @param Magento_Index_Model_Indexer_Config_Reader $reader
     * @param Magento_Config_ScopeInterface $configScope
     * @param Magento_Config_CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        Magento_Index_Model_Indexer_Config_Reader $reader,
        Magento_Config_ScopeInterface $configScope,
        Magento_Config_CacheInterface $cache,
        $cacheId = 'indexerConfigCache'
    ) {
        parent::__construct($reader, $configScope, $cache, $cacheId);
    }

    /**
     * Get indexer data by name
     *
     * @param string $name
     * @return array
     */
    public function getIndexer($name)
    {
        return $this->get($name, array());
    }

    /**
     * Get indexers configuration
     *
     * @return array
     */
    public function getAll()
    {
        return $this->get();
    }
}
