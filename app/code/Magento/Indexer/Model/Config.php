<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Indexer\Model;

class Config extends \Magento\Config\Data implements ConfigInterface
{
    /**
     * @param \Magento\Config\ReaderInterface $reader
     * @param \Magento\Config\CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        \Magento\Config\ReaderInterface $reader,
        \Magento\Config\CacheInterface $cache,
        $cacheId = 'indexer_config'
    ) {
        parent::__construct($reader, $cache, $cacheId);
    }

    /**
     * Get indexer's config
     *
     * @return mixed
     */
    public function getAll()
    {
        return $this->get();
    }
}
