<?php
/**
 * Represents configuration data for a given scope.
 * Provides an abstraction of where the actual data is coming from: config files in the file system, or cache.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Config_Data_SingleScope
{
    /**
     * @var Magento_Config_ReaderInterface
     */
    private $_reader;

    /**
     * @var Magento_Config_CacheInterface
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
     * @param Magento_Config_ReaderInterface $reader
     * @param Magento_Config_CacheInterface $cache
     * @param string $cacheId
     * @param string $scope
     */
    public function __construct(
        Magento_Config_ReaderInterface $reader,
        Magento_Config_CacheInterface $cache,
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
            $this->_data = $this->_cache->get($this->_scope, $this->_cacheId);
            if ($this->_data === false) {
                $this->_data = $this->_reader->read($this->_scope);
                $this->_cache->put($this->_data, $this->_scope, $this->_cacheId);
            }
        }
        return $this->_data;
    }
}
