<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Indexer\Fulltext\Plugin;

abstract class AbstractPlugin
{
    /**
     * @var \Magento\Indexer\Model\IndexerInterface
     */
    protected $indexer;

    /**
     * @param \Magento\Indexer\Model\IndexerInterface $indexer
     */
    public function __construct(
        \Magento\Indexer\Model\IndexerInterface $indexer
    ) {
        $this->indexer = $indexer;
    }

    /**
     * Return own indexer object
     *
     * @return \Magento\Indexer\Model\IndexerInterface
     */
    protected function getIndexer()
    {
        if (!$this->indexer->getId()) {
            $this->indexer->load(\Magento\CatalogSearch\Model\Indexer\Fulltext::INDEXER_ID);
        }
        return $this->indexer;
    }
}
