<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Indexer\Model\Config;

class Data extends \Magento\Config\Data
{
    /**
     * @var \Magento\Indexer\Model\Resource\Indexer\State\Collection
     */
    protected $stateCollection;

    /**
     * @param \Magento\Indexer\Model\Config\Reader $reader
     * @param \Magento\Config\CacheInterface $cache
     * @param \Magento\Indexer\Model\Resource\Indexer\State\Collection $stateCollection
     * @param string $cacheId
     */
    public function __construct(
        \Magento\Indexer\Model\Config\Reader $reader,
        \Magento\Config\CacheInterface $cache,
        \Magento\Indexer\Model\Resource\Indexer\State\Collection $stateCollection,
        $cacheId = 'indexer_config'
    ) {
        $this->stateCollection = $stateCollection;

        $isCacheExists = $cache->test($cacheId);

        parent::__construct($reader, $cache, $cacheId);

        if (!$isCacheExists) {
            $this->deleteNonexistentStates();
        }
    }

    /**
     * Delete all states that are not in configuration
     */
    protected function deleteNonexistentStates()
    {
        foreach ($this->stateCollection->getItems() as $state) {
            /** @var \Magento\Indexer\Model\Indexer\State $state */
            if (!isset($this->_data[$state->getIndexerId()])) {
                $state->delete();
            }
        }
    }
}
