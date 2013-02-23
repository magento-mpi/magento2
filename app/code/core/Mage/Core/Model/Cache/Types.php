<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * An ultimate accessor to cache types' statuses
 */
class Mage_Core_Model_Cache_Types
{
    /**
     * Cache identifier used to store cache type statuses
     */
    const CACHE_ID  = 'core_cache_options';

    /**
     * Persistent storage of cache type statuses
     *
     * @var Mage_Core_Model_Resource_Cache
     */
    private $_resource;

    /**
     * Cache frontend to delegate actual cache operations to
     *
     * @var Mage_Core_Model_CacheInterface
     */
    private $_cache;

    /**
     * Associative array of cache type codes and their statuses (enabled/disabled)
     *
     * @var array
     */
    private $_typeStatuses = array();

    /**
     * @param Mage_Core_Model_Resource_Cache $resource
     * @param Mage_Core_Model_CacheInterface $cache
     * @param Mage_Core_Model_App_State $appState
     * @param bool $banAll Whether all cache types are forced to be disabled
     */
    public function __construct(
        Mage_Core_Model_Resource_Cache $resource,
        Mage_Core_Model_CacheInterface $cache,
        Mage_Core_Model_App_State $appState,
        $banAll = false
    ) {
        $this->_resource = $resource;
        $this->_cache = $cache;
        if ($appState->isInstalled()) {
            $this->_loadTypeStatuses($banAll);
        }
    }

    /**
     * Load statuses (enabled/disabled) of cache types
     *
     * @param bool $forceDisableAll
     */
    private function _loadTypeStatuses($forceDisableAll = false)
    {
        $typeOptions = $this->_cache->load(self::CACHE_ID);
        if ($typeOptions !== false) {
            $typeOptions = unserialize($typeOptions);
        } else {
            $typeOptions = $this->_resource->getAllOptions();
            if ($typeOptions !== false) {
                $this->_cache->save(serialize($typeOptions), self::CACHE_ID);
            }
        }
        if ($typeOptions) {
            foreach ($typeOptions as $cacheType => $isTypeEnabled) {
                $this->setEnabled($cacheType, $isTypeEnabled && !$forceDisableAll);
            }
        }
    }

    /**
     * Whether a cache type is enabled or not at the moment
     *
     * @param string $cacheType
     * @return bool
     */
    public function isEnabled($cacheType)
    {
        return isset($this->_typeStatuses[$cacheType]) ? (bool)$this->_typeStatuses[$cacheType] : false;
    }

    /**
     * Enable/disable a cache type in run-time
     *
     * @param string $cacheType
     * @param bool $isEnabled
     */
    public function setEnabled($cacheType, $isEnabled)
    {
        $this->_typeStatuses[$cacheType] = (int)$isEnabled;
    }

    /**
     * Save the current statuses (enabled/disabled) of cache types to the persistent storage
     */
    public function persist()
    {
        $this->_resource->saveAllOptions($this->_typeStatuses);
        $this->_cache->remove(self::CACHE_ID);
    }
}
