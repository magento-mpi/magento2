<?php
/**
 * Config data. Represents loaded and cached configuration data. Should be used to gain access to different types
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Config_Data implements Magento_Config_DataInterface
{
    /**
     * Internal configuration data
     *
     * @var Magento_Config_ScopeInterface
     */
    protected $_configScope;

    /**
     * Configuration reader model
     *
     * @var Magento_Config_ReaderInterface
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
    protected $_data = array();

    /**
     * @param Magento_Config_ReaderInterface $reader
     * @param Magento_Config_CacheInterface $cache
     * @param $cacheId
     */
    public function __construct(
        Magento_Config_ReaderInterface $reader,
        Magento_Config_CacheInterface $cache,
        $cacheId
    ) {
        $data = $cache->load($cacheId);
        if (false === $data) {
            $data = $reader->read();
            $cache->save($data, $cacheId);
        }
        $this->merge($data);
    }

    /**
     * Merge config data to the object
     *
     * @param array $config
     */
    public function merge(array $config)
    {
        $this->_data = array_replace_recursive($this->_data, $config);
    }

    /**
     * Get config value by key
     *
     * @param string $path
     * @param null $default
     * @return mixed
     */
    public function get($path = null, $default = null)
    {
        if ($path === null) {
            return $this->_data;
        }
        $keys = explode('/', $path);
        $data = $this->_data;
        foreach ($keys as $key) {
            if (is_array($data) && array_key_exists($key, $data)) {
                $data = $data[$key];
            } else {
                return $default;
            }
        }
        return $data;
    }
}
