<?php
/**
 * Locales hierarchy configuration model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Locale_Hierarchy_Config extends Magento_Config_Data
{
    /**
     * Configuration data reader
     *
     * @var Magento_Core_Model_Locale_Hierarchy_Config_Reader
     */
    protected $_reader;

    /**
     * Configuration cache model
     *
     * @var Magento_Config_CacheInterface
     */
    protected $_cache;

    /**
     * Cache identifier
     *
     * @var string
     */
    protected $_cacheId;

    /**
     * Configuration scope
     *
     * @var string
     */
    protected $_scope = 'global';

    /**
     * @param Magento_Config_ReaderInterface $reader
     * @param Magento_Config_CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        Magento_Config_ReaderInterface $reader,
        Magento_Config_CacheInterface $cache,
        $cacheId = 'local_hierarchy_cache'
    ) {
        parent::__construct($reader, $cache, $cacheId);
    }

    /**
     * Get locale hierarchy
     *
     * @return array
     */
    public function getHierarchy()
    {
        return $this->get();
    }
}
