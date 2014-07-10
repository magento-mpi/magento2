<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TargetRule\Model\Indexer\Product\Rule\Plugin;

abstract class AbstractPlugin
{
    /**
     * @var \Magento\Indexer\Model\IndexerInterface
     */
    protected $indexer;

    /**
     * @param \Magento\Indexer\Model\IndexerInterface $indexer
     */
    public function __construct(\Magento\Indexer\Model\IndexerInterface $indexer)
    {
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
            $this->indexer->load(\Magento\TargetRule\Model\Indexer\Product\Rule\Processor::INDEXER_ID);
        }
        return $this->indexer;
    }

    /**
     * Invalidate indexer
     *
     * @return void
     */
    protected function invalidateIndexer()
    {
        $this->getIndexer()->invalidate();
    }
}
