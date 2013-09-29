<?php
/**
 * Locales hierarchy configuration model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Locale\Hierarchy;

class Config extends \Magento\Config\Data
{
    /**
     * Configuration data reader
     *
     * @var \Magento\Core\Model\Locale\Hierarchy\Config\Reader
     */
    protected $_reader;

    /**
     * Configuration cache model
     *
     * @var \Magento\Config\CacheInterface
     */
    protected $_cache;

    /**
     * Cache identifier
     *
     * @var string
     */
    protected $_cacheId;

    /**
     * @param \Magento\Config\ReaderInterface $reader
     * @param \Magento\Config\CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        \Magento\Config\ReaderInterface $reader,
        \Magento\Config\CacheInterface $cache,
        $cacheId = 'local_hierarchy_cache'
    ) {
        parent::__construct($reader, $cache, $cacheId);
    }

    /**
     * Get locale hierarchy
     *
     * @return array
     */
    public function getHierarchy()
    {
        return $this->get();
    }
}
