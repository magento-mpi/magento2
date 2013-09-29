<?php
/**
 * ACL cache
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Model\Acl;

class Cache implements \Magento\Acl\CacheInterface
{
    /**
     * Cache
     *
     * @var \Magento\Core\Model\Cache\Type\Config
     */
    protected $_cache;

    /**
     * Cache key
     *
     * @var string
     */
    protected $_cacheKey;

    /**
     * @var \Magento\Acl
     */
    protected $_acl = null;

    /**
     * @param \Magento\Core\Model\Cache\Type\Config $cache
     * @param string $cacheKey
     */
    function __construct(\Magento\Core\Model\Cache\Type\Config $cache, $cacheKey)
    {
        $this->_cache = $cache;
        $this->_cacheKey = $cacheKey;
    }

    /**
     * Check whether ACL object is in cache
     *
     * @return bool
     */
    public function has()
    {
        return null !== $this->_acl || $this->_cache->test($this->_cacheKey);
    }

    /**
     * Retrieve ACL object from cache
     *
     * @return \Magento\Acl
     */
    public function get()
    {
        if (null == $this->_acl) {
            $this->_acl = unserialize($this->_cache->load($this->_cacheKey));
        }
        return $this->_acl;
    }

    /**
     * Save ACL object to cache
     *
     * @param \Magento\Acl $acl
     */
    public function save(\Magento\Acl $acl)
    {
        $this->_acl = $acl;
        $this->_cache->save(serialize($acl), $this->_cacheKey);
    }

    /**
     * Clear ACL instance cache
     */
    public function clean()
    {
        $this->_acl = null;
        $this->_cache->remove($this->_cacheKey);
    }
}
