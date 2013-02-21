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
 * System / Cache Management / Cache type "Configuration"
 */
class Mage_Core_Model_Cache_Type_Config
{
    /**
     * Cache type code unique among all cache types
     */
    const CACHE_TYPE_CODE = 'config';

    /**
     * Cache tag used to distinguish the cache type from all other cache
     */
    const CACHE_TAG = 'CONFIG';

    /**
     * Cache frontend to delegate actual cache operations to
     *
     * @var Mage_Core_Model_CacheInterface
     */
    private $_cache;

    /**
     * Cache types manager
     *
     * @var Mage_Core_Model_Cache_Types
     */
    private $_cacheTypes;

    /**
     * @param Mage_Core_Model_CacheInterface $cache
     * @param Mage_Core_Model_Cache_Types $cacheTypes
     */
    public function __construct(Mage_Core_Model_CacheInterface $cache, Mage_Core_Model_Cache_Types $cacheTypes)
    {
        $this->_cache = $cache;
        $this->_cacheTypes = $cacheTypes;
    }

    /**
     * Whether the cache type is enabled at the moment or not
     *
     * @return bool
     */
    protected function _isEnabled()
    {
        return $this->_cacheTypes->isEnabled(self::CACHE_TYPE_CODE);
    }

    /**
     * Load and return a record from the cache
     *
     * @param string $id
     * @return string|bool
     */
    public function load($id)
    {
        if (!$this->_isEnabled()) {
            return false;
        }
        return $this->_cache->load($id);
    }

    /**
     * Save a record into the cache
     *
     * @param string $data
     * @param string $id
     * @param array $tags
     * @param null|bool $lifeTime
     * @return bool
     */
    public function save($data, $id, $tags = array(), $lifeTime = null)
    {
        if (!$this->_isEnabled()) {
            return true;
        }
        $tags[] = self::CACHE_TAG;
        return $this->_cache->save($data, $id, $tags, $lifeTime);
    }

    /**
     * Remove a record from the cache
     *
     * @param string $id
     * @return bool
     */
    public function remove($id)
    {
        if (!$this->_isEnabled()) {
            return true;
        }
        return $this->_cache->remove($id);
    }

    /**
     * Clean all cache records belonging to the cache type
     *
     * @return bool
     */
    public function flush()
    {
        if (!$this->_isEnabled()) {
            return true;
        }
        return $this->_cache->clean(self::CACHE_TAG);
    }
}
