<?php
/**
 * Configuration data container for default, stores and websites config values
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Data implements Mage_Core_Model_Config_DataInterface
{
    /**
     * Configuration reader model
     *
     * @var Mage_Core_Model_Config_Data_Reader
     */
    protected $_reader;

    /**
     * Configuration cache model
     *
     * @var Magento_Config_CacheInterface
     */
    protected $_cache;

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheId;

    /**
     * Config data
     *
     * @var array
     */
    protected $_data = null;

    /**
     * @param Mage_Core_Model_Config_Data_Reader $reader
     * @param Magento_Config_CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        Mage_Core_Model_Config_Data_Reader $reader,
        Magento_Config_CacheInterface $cache,
        $cacheId = 'default_config_cache'
    ) {
        $this->_reader = $reader;
        $this->_cache = $cache;
        $this->_cacheId = $cacheId;
    }

    /**
     * Get config value by scope
     *
     * @param null $path
     * @param string $scope
     * @param null $scopeCode
     * @return mixed
     */
    public function getValue($path = null, $scope = '', $scopeCode = null)
    {
        if ($this->_data == null) {
            $data = $this->_cache->get('config', $this->_cacheId);
            if (false === $data) {
                $data = $this->_reader->read($scope, $scopeCode);
                $this->_cache->put($data, 'config', $this->_cacheId);
            }
            $this->_data = $data;
        }

        if ($path === null) {
            return $this->_data;
        }
        $keys = explode('/', $path);
        $data = $this->_data;
        foreach ($keys as $key) {
            if (is_array($data) && array_key_exists($key, $data)) {
                $data = $data[$key];
            } else {
                return false;
            }
        }
        return $data;
    }
}