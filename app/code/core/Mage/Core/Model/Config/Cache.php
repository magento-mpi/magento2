<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Config_Cache
{
    /**
     * Config cache id
     *
     * @var string
     */
    protected $_cacheId = 'config_global';

    /**
     * Cache lock id
     *
     * @var string
     */
    protected $_cacheLockId;

    /**
     * Container factory model
     *
     * @var Mage_Core_Model_Config_ContainerFactory
     */
    protected $_containerFactory;

    /**
     * Base config factory model
     *
     * @var Mage_Core_Model_Config_BaseFactory
     */
    protected $_baseFactory;

    /**
     * Cache object
     *
     * @var Mage_Core_Model_CacheInterface
     */
    protected $_cache;

    /**
     * Configuration sections
     *
     * @var Mage_Core_Model_Config_Sections
     */
    protected $_configSections;

    /**
     * List of configuration parts for save in cache
     *
     * @var array
     */
    protected $_cachePartsForSave;

    /**
     * Cache lifetime in seconds
     *
     * @var int
     */
    protected $_cacheLifetime;

    /**
     * @param Mage_Core_Model_Cache $cache
     * @param Mage_Core_Model_Config_Sections $configSections
     * @param Mage_Core_Model_Config_ContainerFactory $containerFactory
     * @param Mage_Core_Model_Config_BaseFactory $baseFactory
     */
    public function __construct(
        Mage_Core_Model_Cache $cache,
        Mage_Core_Model_Config_Sections $configSections,
        Mage_Core_Model_Config_ContainerFactory $containerFactory,
        Mage_Core_Model_Config_BaseFactory $baseFactory
    ) {
        $this->_containerFactory = $containerFactory;
        $this->_cache = $cache;
        $this->_configSections = $configSections;
        $this->_cacheLockId = $this->_cacheId . '.lock';
        $this->_baseFactory = $baseFactory;
    }

    /**
     * Save cache of specified
     *
     * @param   string $idPrefix cache id prefix
     * @param   string $sectionName
     * @param   Varien_Simplexml_Element $source
     * @param   int $recursionLevel
     * @param   array $tags
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _saveSectionCache($idPrefix, $sectionName, $source, $recursionLevel = 0, $tags = array())
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
     * @return Mage_Core_Model_ConfigInterface|bool
     */
    public function load()
    {
        $config = ($this->_cache->canUse('config') && false == $this->_isLocked())
            ? $this->_cache->load($this->_cacheId)
            : false;

        if ($config) {
            $config = $this->_containerFactory->create(array('sourceData' => $config));
        }
        return $config;
    }

    /**
     * Save config cache
     *
     * @param Mage_Core_Model_Config_Base $config
     */
    public function save(Mage_Core_Model_Config_Base $config)
    {
        if ($this->_cache->canUse('config') && false == $this->_isLocked()) {
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
            $this->clean();
            foreach ($this->_cachePartsForSave as $cacheId => $cacheData) {
                $this->_cache->save(
                    $cacheData, $cacheId, array(Mage_Core_Model_Config::CACHE_TAG), $this->_cacheLifetime
                );
            }
            unset($this->_cachePartsForSave);
            $this->_unlock();
        }
    }

    /**
     * Clean cached data
     *
     * @return bool
     */
    public function clean()
    {
        return $this->_cache->clean(array(Mage_Core_Model_Config::CACHE_TAG));
    }

    /**
     * Load config section cached data
     *
     * @param   string $sectionKey
     * @return  Mage_Core_Model_Config_Base|bool
     * @throws  Mage_Core_Model_Config_Cache_Exception
     */
    public function getSection($sectionKey)
    {
        $cacheId = $this->_cacheId . '_' . $sectionKey;
        $result = false;
        if ($this->_cache->canUse('config')) {
            $xmlString = $this->_cache->load($cacheId);
            if ($xmlString) {
                $result = $this->_baseFactory->create($xmlString);
            } else {
                throw new Mage_Core_Model_Config_Cache_Exception();
            }
        }
        return $result;
    }
}
