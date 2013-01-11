<?php
/**
 * Application config storage
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Storage implements Mage_Core_Model_Config_StorageInterface
{
    /**
     * Config cache id
     *
     * @var string
     */
    protected $_cacheId = 'config_global';

    /**
     * Cache object
     *
     * @var Mage_Core_Model_CacheInterface
     */
    protected $_cache;

    /**
     * Configuration loader
     *
     * @var Mage_Core_Model_Config_Loader
     */
    protected $_loader;

    /**
     * @param Mage_Core_Model_Cache $cache
     * @param Mage_Core_Model_Config_Loader $loader
     */
    public function __construct(
        Mage_Core_Model_Cache $cache,
        Mage_Core_Model_Config_Loader $loader
    ) {
        $this->_cache = $cache;
        $this->_loader = $loader;
    }

    /**
     * Retrieve application configuration
     *
     * @param bool $useCache
     * @return mixed|string
     */
    public function getConfiguration($useCache = true)
    {
        $config = $useCache ? $this->_cache->load($this->_cacheId) : '';
        if (!$config) {
            $config = $this->_loader->load()->asNiceXml(0, '');
            if ($useCache) {
                $this->_cache->save($config, $this->_cacheId, array(Mage_Core_Model_Config::CACHE_TAG));
            }
        }
        return $config;
    }

    /**
     * Remove configuration cache
     */
    public function removeCache()
    {
        $this->_cache->clean(array(Mage_Core_Model_Config::CACHE_TAG));
    }
}
