<?php
/**
 * Indexer configuration model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Index\Model\Indexer;

class Config extends \Magento\Config\Data\Scoped
    implements \Magento\Index\Model\Indexer\ConfigInterface
{
    /**
     * Scope priority loading scheme
     *
     * @var array
     */
    protected $_scopePriorityScheme = array('global');

    /**
     * @param \Magento\Index\Model\Indexer\Config\Reader $reader
     * @param \Magento\Config\ScopeInterface $configScope
     * @param \Magento\Config\CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        \Magento\Index\Model\Indexer\Config\Reader $reader,
        \Magento\Config\ScopeInterface $configScope,
        \Magento\Config\CacheInterface $cache,
        $cacheId = 'indexerConfigCache'
    ) {
        parent::__construct($reader, $configScope, $cache, $cacheId);
    }

    /**
     * Get indexer data by name
     *
     * @param string $name
     * @return array
     */
    public function getIndexer($name)
    {
        return $this->get($name, array());
    }

    /**
     * Get indexers configuration
     *
     * @return array
     */
    public function getAll()
    {
        return $this->get();
    }
}
