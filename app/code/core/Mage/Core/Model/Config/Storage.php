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
     * Cache lifetime in seconds
     *
     * @var int
     */
    protected $_cacheLifetime;
    /**
     * Configuration loader
     *
     * @var Mage_Core_Model_Config_Loader
     */
    protected $_loader;

    /**
     * Configuration sections
     *
     * @var Mage_Core_Model_Config_Sections
     */
    protected $_configSections;

    /**
     * @var array
     */
    protected $_cachePartsForSave;

    /**
     * @param Mage_Core_Model_Cache $cache
     * @param Mage_Core_Model_Config_Sections $configSections
     * @param Mage_Core_Model_Config_Loader $loader
     */
    public function __construct(
        Mage_Core_Model_Cache $cache,
        Mage_Core_Model_Config_Sections $configSections,
        Mage_Core_Model_Config_Loader $loader
    ) {
        $this->_cache = $cache;
        $this->_configSections = $configSections;
        $this->_loader = $loader;
        $this->_cacheLockId = $this->_cacheId . '.lock';
    }

    /**
     * Lock caching to prevent concurrent cache writes
     */
    protected function _lock()
    {
        $this->_cache->save(time(), $this->_cacheLockId, array(), 60);
    }

    /**
     * Unlock caching
     */
    protected function _unlock()
    {
        $this->_cache->remove($this->_cacheLockId);
    }

    /**
     * Check whether caching is locked
     *
     * @return bool
     */
    protected function _isLocked()
    {
        return !!$this->_cache->load($this->_cacheLockId);
    }

    /**
     * Set cache lifetime
     *
     * @param int $lifetime
     */
    public function setCacheLifetime($lifetime)
    {
        $this->_cacheLifetime = $lifetime;
    }

    /**
     * Retrieve cache lifetime
     *
     * @return int
     */
    public function getCacheLifeTime()
    {
        return $this->_cacheLifetime;
    }

    /**
     * Save cache of specified
     *
     * @param   string $idPrefix cache id prefix
     * @param   string $sectionName
     * @param   Varien_Simplexml_Element $source
     * @param   int $recursionLevel
     * @param   array $tags
     * @return  Mage_Core_Model_Config
     */
    protected function _saveSectionCache($idPrefix, $sectionName, $source, $recursionLevel=0, $tags=array())
    {
        if ($source && $source->$sectionName) {
            $cacheId = $idPrefix . '_' . $sectionName;
            if ($recursionLevel > 0) {
                foreach ($source->$sectionName->children() as $subSectionName => $node) {
                    $this->_saveSectionCache(
                        $cacheId, $subSectionName, $source->$sectionName, $recursionLevel-1, $tags
                    );
                }
            }
            $this->_cachePartsForSave[$cacheId] = $source->$sectionName->asNiceXml('', false);
        }
        return $this;
    }

    /**
     * Retrieve application configuration
     *
     * @param bool $useCache
     * @return mixed|string
     */
    public function getConfiguration($useCache = true)
    {
        $config = ($useCache && $this->_cache->canUse('config') && !$this->_isLocked())
            ? $this->_cache->load($this->_cacheId)
            : '';
        if (!$config) {
            $config = new Mage_Core_Model_Config_Base('<config/>');
            $this->_loader->load($config);
            if ($this->_cache->canUse('config') && !$this->_isLocked()) {
                $this->_cache->clean(Mage_Core_Model_Config::CACHE_TAG);

                $cacheSections = $this->_configSections->getSections();
                $xml = clone $config->getNode();
                if (!empty($cacheSections)) {
                    foreach ($cacheSections as $sectionName => $level) {
                        $this->_saveSectionCache(
                            $this->_cacheId, $sectionName, $xml, $level, array(Mage_Core_Model_Config::CACHE_TAG)
                        );
                        unset($xml->$sectionName);
                    }
                }
                $this->_cachePartsForSave[$this->_cacheId] = $xml->asNiceXml('', false);
                $this->_lock();
                $this->removeCache();
                foreach ($this->_cachePartsForSave as $cacheId => $cacheData) {
                    $this->_cache->save(
                        $cacheData, $cacheId, array(Mage_Core_Model_Config::CACHE_TAG), $this->_cacheLifetime
                    );
                }
                unset($this->_cachePartsForSave);
                $this->_unlock();
                $config = $config->getNode()->asXml();
            }
        }
        return $config;
    }

    /**
     * Load config section cached data
     *
     * @param   string $sectionKey
     * @return  Varien_Simplexml_Element|bool
     */
    public function getSection($sectionKey)
    {
        $cacheId = $this->_cacheId . '_' . $sectionKey;
        $result = false;
        if ($this->_cache->canUse('config')) {
            $xmlString = $this->_cache->load($cacheId);
            if ($xmlString) {
                $result = new Mage_Core_Model_Config_Base($xmlString);
            }
        }
        return $result;
    }

    /**
     * Remove configuration cache
     */
    public function removeCache()
    {
        $this->_cache->clean(array(Mage_Core_Model_Config::CACHE_TAG));
    }
}
