<?php
/**
* {license_notice}
*
* @copyright {copyright}
* @license {license_link}
*/
namespace Magento\App\Config;

class ScopePool
{
    const CACHE_TAG = 'config_scopes';

    /**
     * @var \Magento\App\Config\Scope\Reader
     */
    protected $_reader;

    /**
     * @var \Magento\App\Config\DataFactory
     */
    protected $_dataFactory;

    /**
     * @var \Magento\Cache\FrontendInterface
     */
    protected $_cache;

    /**
     * @var string
     */
    protected $_cacheId;

    /**
     * @var \Magento\App\Config\DataInterface[]
     */
    protected $_scopes = array();

    /**
     * @param \Magento\App\Config\Scope\Reader $reader
     * @param \Magento\App\Config\DataFactory $dataFactory
     * @param \Magento\Cache\FrontendInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        \Magento\App\Config\Scope\Reader $reader,
        \Magento\App\Config\DataFactory $dataFactory,
        \Magento\Cache\FrontendInterface $cache,
        $cacheId = 'default_config_cache'
    ) {
        $this->_reader = $reader;
        $this->_dataFactory = $dataFactory;
        $this->_cache = $cache;
        $this->_cacheId = $cacheId;
    }

    /**
     * Retrieve config section
     *
     * @param string $scope
     * @return \Magento\App\Config\Data
     */
    public function getScope($scope)
    {
        if (!isset($this->_scopes[$scope])) {
            $cacheKey = $this->_cacheId . '|' . $scope;
            $data = $this->_cache->load($cacheKey);
            if ($data) {
                $data = unserialize($data);
            } else {
                $data = $this->_reader->read($scope);
                $this->_cache->save(serialize($data), $cacheKey, array(self::CACHE_TAG));
            }
            $this->_scopes[$scope] = $this->_dataFactory->create(array('data' => $data));
        }
        return $this->_scopes[$scope];
    }

    /**
     * Clear all stired sections
     */
    public function clean()
    {
        $this->_scopes = array();
        $this->_cache->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, array(self::CACHE_TAG));
    }
}
