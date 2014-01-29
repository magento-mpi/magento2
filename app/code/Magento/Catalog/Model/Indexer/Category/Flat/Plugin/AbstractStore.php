<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category\Flat\Plugin;

class AbstractStore
{
    /**
     * @var \Magento\Indexer\Model\Indexer
     */
    protected $indexer;

    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\Config
     */
    protected $config;

    /**
     * Category flat resource
     *
     * @var \Magento\Catalog\Model\Resource\Category\Flat
     */
    protected $flatResource;

    /**
     * @param \Magento\Indexer\Model\Indexer $indexer
     * @param \Magento\Catalog\Model\Indexer\Category\Flat\Config $config
     * @param \Magento\Catalog\Model\Resource\Category\Flat $flatResource
     */
    public function __construct(
        \Magento\Indexer\Model\Indexer $indexer,
        \Magento\Catalog\Model\Indexer\Category\Flat\Config $config,
        \Magento\Catalog\Model\Resource\Category\Flat $flatResource
    ) {
        $this->indexer = $indexer;
        $this->config = $config;
        $this->flatResource = $flatResource;
    }

    /**
     * Return own indexer object
     *
     * @return \Magento\Indexer\Model\Indexer
     */
    protected function getIndexer()
    {
        if (!$this->indexer->getId()) {
            $this->indexer->load(\Magento\Catalog\Model\Indexer\Category\Flat\Config::INDEXER_ID);
        }
        return $this->indexer;
    }

    /**
     * Invalidating indexer
     */
    protected function invalidatingIndexer()
    {
        if ($this->config->isFlatEnabled()) {
            $this->getIndexer()->getState()
                ->setStatus(\Magento\Indexer\Model\Indexer\State::STATUS_INVALID)
                ->save();
        }
    }

    /**
     * Cleaning a data after removing store
     *
     * @param $storeIds
     */
    protected function cleanStoreData($storeIds)
    {
        $this->flatResource->deleteStores($storeIds);
    }
}
