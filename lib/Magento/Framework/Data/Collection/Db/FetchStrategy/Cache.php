<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Retrieving collection data from cache, failing over to another fetch strategy, if cache not yet exists
 */
namespace Magento\Data\Collection\Db\FetchStrategy;

class Cache implements \Magento\Data\Collection\Db\FetchStrategyInterface
{
    /**
     * @var \Magento\Cache\FrontendInterface
     */
    private $_cache;

    /**
     * @var \Magento\Data\Collection\Db\FetchStrategyInterface
     */
    private $_fetchStrategy;

    /**
     * @var string
     */
    protected $_cacheIdPrefix = '';

    /**
     * @var array
     */
    protected $_cacheTags = array();

    /**
     * @var int|bool|null
     */
    protected $_cacheLifetime = null;

    /**
     * Constructor
     *
     * @param \Magento\Cache\FrontendInterface $cache
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param string $cacheIdPrefix
     * @param array $cacheTags
     * @param int|bool|null $cacheLifetime
     */
    public function __construct(
        \Magento\Cache\FrontendInterface $cache,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        $cacheIdPrefix = '',
        array $cacheTags = array(),
        $cacheLifetime = null
    ) {
        $this->_cache = $cache;
        $this->_fetchStrategy = $fetchStrategy;
        $this->_cacheIdPrefix = $cacheIdPrefix;
        $this->_cacheTags = $cacheTags;
        $this->_cacheLifetime = $cacheLifetime;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAll(\Zend_Db_Select $select, array $bindParams = array())
    {
        $cacheId = $this->_getSelectCacheId($select);
        $result = $this->_cache->load($cacheId);
        if ($result) {
            $result = unserialize($result);
        } else {
            $result = $this->_fetchStrategy->fetchAll($select, $bindParams);
            $this->_cache->save(serialize($result), $cacheId, $this->_cacheTags, $this->_cacheLifetime);
        }
        return $result;
    }

    /**
     * Determine cache identifier based on select query
     *
     * @param \Magento\DB\Select|string $select
     * @return string
     */
    protected function _getSelectCacheId($select)
    {
        return $this->_cacheIdPrefix . md5((string)$select);
    }
}
