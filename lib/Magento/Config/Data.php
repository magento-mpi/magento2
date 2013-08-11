<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Config_Data implements Magento_Config_DataInterface
{
    /**
     * Configuration scope resolver model
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
     * Current scope identifier
     *
     * @var string
     */
    protected $_currentScope = null;

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
     * @param Magento_Config_ScopeInterface $configScope
     * @param Magento_Config_CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        Magento_Config_ReaderInterface $reader,
        Magento_Config_ScopeInterface $configScope,
        Magento_Config_CacheInterface $cache,
        $cacheId
    ) {
        $this->_reader = $reader;
        $this->_configScope = $configScope;
        $this->_cache = $cache;
        $this->_cacheId = $cacheId;
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
        if ($this->_currentScope != $this->_configScope->getCurrentScope()) {
            $this->_currentScope = $this->_configScope->getCurrentScope();
            $data = $this->_cache->get($this->_currentScope, $this->_cacheId);
            if (false === $data) {
                $data = $this->_reader->read($this->_currentScope);
                $this->_cache->put($data, $this->_currentScope, $this->_cacheId);
            }
            $this->merge($data);
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
                return $default;
            }
        }
        return $data;
    }
}
