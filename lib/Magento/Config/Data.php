<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Config;

class Data implements \Magento\Config\DataInterface
{
    /**
     * Configuration scope resolver model
     *
     * @var \Magento\Config\ScopeInterface
     */
    protected $_configScope;

    /**
     * Configuration reader model
     *
     * @var \Magento\Config\ReaderInterface
     */
    protected $_reader;

    /**
     * Configuration cache model
     *
     * @var \Magento\Config\CacheInterface
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
     * Scope priority loading scheme
     *
     * @var array
     */
    protected $_scopePriorityScheme = array();

    /**
     * Loaded scopes
     *
     * @var array
     */
    protected $_loadedScopes = array();

    /**
     * @param \Magento\Config\ReaderInterface $reader
     * @param \Magento\Config\ScopeInterface $configScope
     * @param \Magento\Config\CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        \Magento\Config\ReaderInterface $reader,
        \Magento\Config\ScopeInterface $configScope,
        \Magento\Config\CacheInterface $cache,
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
        $this->_loadScopedData();
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

    /**
     * Load data for current scope
     */
    protected function _loadScopedData()
    {
        $scope = $this->_configScope->getCurrentScope();
        if (false == isset($this->_loadedScopes[$scope])) {
            if (false == in_array($scope, $this->_scopePriorityScheme)) {
                $this->_scopePriorityScheme[] = $scope;
            }
            foreach ($this->_scopePriorityScheme as $scopeCode) {
                if (false == isset($this->_loadedScopes[$scopeCode])) {
                    $data = $this->_cache->get($scopeCode, $this->_cacheId);
                    if (false === $data) {
                        $data = $this->_reader->read($scopeCode);
                        $this->_cache->put($data, $scopeCode, $this->_cacheId);
                    }
                    $this->merge($data);
                    $this->_loadedScopes[$scopeCode] = true;
                }
                if ($scopeCode == $scope) {
                    break;
                }
            }
        }
    }
}
