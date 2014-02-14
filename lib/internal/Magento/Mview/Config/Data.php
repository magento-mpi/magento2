<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Mview\Config;

class Data extends \Magento\Config\Data
{
    /**
     * @var \Magento\Mview\View\State\CollectionInterface
     */
    protected $stateCollection;

    /**
     * @param \Magento\Mview\Config\Reader $reader
     * @param \Magento\Config\CacheInterface $cache
     * @param \Magento\Mview\View\State\CollectionInterface $stateCollection
     * @param string $cacheId
     */
    public function __construct(
        \Magento\Mview\Config\Reader $reader,
        \Magento\Config\CacheInterface $cache,
        \Magento\Mview\View\State\CollectionInterface $stateCollection,
        $cacheId = 'mview_config'
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
            /** @var \Magento\Mview\View\StateInterface $state */
            if (!isset($this->_data[$state->getViewId()])) {
                $state->delete();
            }
        }
    }
}
