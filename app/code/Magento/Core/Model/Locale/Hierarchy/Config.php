<?php
/**
 * Locales hierarchy configuration model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Locale_Hierarchy_Config
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
     * @var \Magento\Config\CacheInterface
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
     * @param Magento_Core_Model_Locale_Hierarchy_Config_Reader $reader
     * @param \Magento\Config\CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        Magento_Core_Model_Locale_Hierarchy_Config_Reader $reader,
        \Magento\Config\CacheInterface $cache,
        $cacheId = 'local_hierarchy_cache'
    ) {
        $this->_cache = $cache;
        $this->_reader = $reader;
        $this->_cacheId = $cacheId;
    }

    /**
     * Get locale hierarchy
     *
     * @return array
     */
    public function getHierarchy()
    {
        $data = $this->_cache->get($this->_scope, $this->_cacheId);
        if (!$data) {
            $data = $this->_reader->read($this->_scope);
            $this->_cache->put($data, $this->_scope, $this->_cacheId);
        }
        return $data;
    }
}
