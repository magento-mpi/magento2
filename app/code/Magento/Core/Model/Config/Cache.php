<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Config_Cache
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
     * @var Magento_Core_Model_Config_ContainerFactory
     */
    protected $_containerFactory;

    /**
     * Base config factory model
     *
     * @var Magento_Core_Model_Config_BaseFactory
     */
    protected $_baseFactory;

    /**
     * @var Magento_Core_Model_Cache_Type_Config
     */
    protected $_configCacheType;

    /**
     * Configuration sections
     *
     * @var Magento_Core_Model_Config_Sections
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
     * Config container
     *
     * @var Magento_Core_Model_Config_Container
     */
    protected $_loadedConfig = null;

    /**
     * @param Magento_Core_Model_Cache_Type_Config $configCacheType
     * @param Magento_Core_Model_Config_Sections $configSections
     * @param Magento_Core_Model_Config_ContainerFactory $containerFactory
     * @param Magento_Core_Model_Config_BaseFactory $baseFactory
     */
    public function __construct(
        Magento_Core_Model_Cache_Type_Config $configCacheType,
        Magento_Core_Model_Config_Sections $configSections,
        Magento_Core_Model_Config_ContainerFactory $containerFactory,
        Magento_Core_Model_Config_BaseFactory $baseFactory
    ) {
        $this->_containerFactory = $containerFactory;
        $this->_configCacheType = $configCacheType;
        $this->_configSections = $configSections;
        $this->_cacheLockId = $this->_cacheId . '.lock';
        $this->_baseFactory = $baseFactory;
    }

    /**
     * Save cache of specified
     *
     * @param   string $idPrefix cache id prefix
     * @param   string $sectionName
     * @param   Magento_Simplexml_Element $source
     * @param   int $recursionLevel
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _saveSectionCache($idPrefix, $sectionName, $source, $recursionLevel = 0)
    {
        if ($source && $source->$sectionName) {
            $cacheId = $idPrefix . '_' . $sectionName;
            if ($recursionLevel > 0) {
                foreach ($source->$sectionName->children() as $subSectionName => $node) {
                    $this->_saveSectionCache($cacheId, $subSectionName, $source->$sectionName, $recursionLevel-1);
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
        $this->_configCacheType->save((string)time(), $this->_cacheLockId, array(), 60);
    }

    /**
     * Unlock caching
     */
    protected function _unlock()
    {
        $this->_configCacheType->remove($this->_cacheLockId);
    }

    /**
     * Check whether caching is locked
     *
     * @return bool
     */
    protected function _isLocked()
    {
        return (bool)$this->_configCacheType->load($this->_cacheLockId);
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
     * @return Magento_Core_Model_ConfigInterface|bool
     */
    public function load()
    {
        if (!$this->_loadedConfig) {
            $config = (false == $this->_isLocked()) ? $this->_configCacheType->load($this->_cacheId) : false;
            if ($config) {
                $this->_loadedConfig = $this->_containerFactory->create(array('sourceData' => $config));
            }
        }
        return $this->_loadedConfig ? $this->_loadedConfig : false;
    }

    /**
     * Save config cache
     *
     * @param Magento_Core_Model_Config_Base $config
     */
    public function save(Magento_Core_Model_Config_Base $config)
    {
        if (false == $this->_isLocked()) {
            $cacheSections = $this->_configSections->getSections();
            $xml = clone $config->getNode();
            if (!empty($cacheSections)) {
                foreach ($cacheSections as $sectionName => $level) {
                    $this->_saveSectionCache($this->_cacheId, $sectionName, $xml, $level);
                    unset($xml->$sectionName);
                }
            }
            $this->_cachePartsForSave[$this->_cacheId] = $xml->asNiceXml('', false);
            $this->_lock();
            $this->clean();
            foreach ($this->_cachePartsForSave as $cacheId => $cacheData) {
                $this->_configCacheType->save((string)$cacheData, $cacheId, array(), $this->_cacheLifetime);
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
        $this->_loadedConfig = null;
        return $this->_configCacheType->clean();
    }

    /**
     * Load config section cached data
     *
     * @param   string $sectionKey
     * @return  Magento_Core_Model_Config_Base|bool
     * @throws  Magento_Core_Model_Config_Cache_Exception
     */
    public function getSection($sectionKey)
    {
        $cacheId = $this->_cacheId . '_' . $sectionKey;
        $xmlString = $this->_configCacheType->load($cacheId);
        if ($xmlString) {
            return $this->_baseFactory->create($xmlString);
        }
        throw new Magento_Core_Model_Config_Cache_Exception();
    }
}
