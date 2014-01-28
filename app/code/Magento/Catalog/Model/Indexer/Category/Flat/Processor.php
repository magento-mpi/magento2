<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Category\Flat;

class Processor
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\Config
     */
    protected $config;

    /**
     * @var \Magento\Indexer\Model\Indexer
     */
    protected $indexer;

    /**
     * @param \Magento\Catalog\Model\Indexer\Category\Flat\Config $config
     * @param \Magento\Indexer\Model\Indexer $indexer
     */
    public function __construct(
        \Magento\Catalog\Model\Indexer\Category\Flat\Config $config,
        \Magento\Indexer\Model\Indexer $indexer
    ) {
        $this->config = $config;
        $this->indexer = $indexer;
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
     * Regenerate index for category if indexer does not use Mview
     *
     * @param int $categoryId
     */
    public function reindexRow($categoryId)
    {
        if ($this->config->isFlatEnabled()
            && $this->getIndexer()->getMode() == \Magento\Mview\View\StateInterface::MODE_DISABLED
        ) {
            $this->getIndexer()->reindexRow($categoryId);
        }
    }

    /**
     * Regenerate index for categories if indexer does not use Mview
     *
     * @param int[] $categoryIds
     */
    public function reindexList(array $categoryIds)
    {
        if ($this->config->isFlatEnabled()
            && $this->getIndexer()->getMode() == \Magento\Mview\View\StateInterface::MODE_DISABLED
        ) {
            $this->getIndexer()->reindexList($categoryIds);
        }
    }
}
