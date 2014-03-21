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
     * @var \Magento\App\Config\Scope\ReaderPoolInterface
     */
    protected $_readerPool;

    /**
     * @var DataFactory
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
     * @var DataInterface[]
     */
    protected $_scopes = array();

    /**
     * @var \Magento\App\ScopeResolverPool
     */
    protected $_scopeResolverPool;

    /**
     * @param \Magento\App\Config\Scope\ReaderPoolInterface $readerPool
     * @param DataFactory $dataFactory
     * @param \Magento\Cache\FrontendInterface $cache
     * @param \Magento\App\ScopeResolverPool $scopeResolverPool
     * @param string $cacheId
     */
    public function __construct(
        \Magento\App\Config\Scope\ReaderPoolInterface $readerPool,
        DataFactory $dataFactory,
        \Magento\Cache\FrontendInterface $cache,
        \Magento\App\ScopeResolverPool $scopeResolverPool,
        $cacheId = 'default_config_cache'
    ) {
        $this->_readerPool = $readerPool;
        $this->_dataFactory = $dataFactory;
        $this->_cache = $cache;
        $this->_cacheId = $cacheId;
        $this->_scopeResolverPool = $scopeResolverPool;
    }

    /**
     * Retrieve config section
     *
     * @param string $scopeType
     * @param string|\Magento\Object|null $scopeCode
     * @return \Magento\App\Config\DataInterface
     */
    public function getScope($scopeType, $scopeCode = null)
    {
        $scopeCode = $this->_getScopeCode($scopeType, $scopeCode);
        $code = $scopeType . '|' . $scopeCode;
        if (!isset($this->_scopes[$code])) {
            $cacheKey = $this->_cacheId . '|' . $code;
            $data = $this->_cache->load($cacheKey);
            if ($data) {
                $data = unserialize($data);
            } else {
                $reader = $this->_readerPool->getReader($scopeType);
                if ($scopeType === \Magento\App\ScopeInterface::SCOPE_DEFAULT) {
                    $data = $reader->read();
                } else {
                    $data = $reader->read($scopeCode);
                }
                $this->_cache->save(serialize($data), $cacheKey, array(self::CACHE_TAG));
            }
            $this->_scopes[$code] = $this->_dataFactory->create(array('data' => $data));
        }
        return $this->_scopes[$code];
    }

    /**
     * Clear cache of all scopes
     *
     * @return void
     */
    public function clean()
    {
        $this->_scopes = array();
        $this->_cache->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, array(self::CACHE_TAG));
    }

    /**
     * Retrieve scope code value
     *
     * @param string $scopeType
     * @param string|\Magento\Object|null $scopeCode
     * @return string
     */
    protected function _getScopeCode($scopeType, $scopeCode)
    {
        if (is_null($scopeCode) && $scopeType !== \Magento\App\ScopeInterface::SCOPE_DEFAULT) {
            $scopeResolver = $this->_scopeResolverPool->get($scopeType);
            $scopeCode = $scopeResolver->getScope();
        }

        if ($scopeCode instanceof \Magento\App\ScopeInterface) {
            $scopeCode = $scopeCode->getCode();
        }

        return $scopeCode;
    }
}
