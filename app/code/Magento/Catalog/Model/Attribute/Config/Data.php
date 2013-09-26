<?php
/**
 * Represents catalog attributes data for a given scope.
 * Provides an abstraction from where the actual data is coming from: config files in the file system, or cache.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Attribute\Config;

class Data
{
    /**
     * @var \Magento\Config\ReaderInterface
     */
    private $_reader;

    /**
     * @var \Magento\Config\CacheInterface
     */
    private $_cache;

    /**
     * @var string
     */
    private $_cacheId;

    /**
     * @var string
     */
    private $_scope;

    /**
     * @var array
     */
    private $_data;

    /**
     * @param \Magento\Catalog\Model\Attribute\Config\Reader $reader
     * @param \Magento\Config\CacheInterface $cache
     * @param string $cacheId
     * @param string $scope
     */
    public function __construct(
        \Magento\Catalog\Model\Attribute\Config\Reader $reader,
        \Magento\Config\CacheInterface $cache,
        $cacheId,
        $scope
    ) {
        $this->_reader = $reader;
        $this->_cache = $cache;
        $this->_cacheId = $cacheId;
        $this->_scope = $scope;
    }

    /**
     * Retrieve data from the storage
     *
     * @return array
     */
    public function getData()
    {
        if ($this->_data === null) {
            $cachedData = $this->_cache->load($this->_scope . '::' . $this->_cacheId);
            if ($cachedData === false) {
                $this->_data = $this->_reader->read($this->_scope);
                $this->_cache->save(serialize($this->_data), $this->_scope . '::' . $this->_cacheId);
            } else {
                $this->_data = unserialize($cachedData);
            }
        }
        return $this->_data;
    }
}
