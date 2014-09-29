<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogRule\Model\Indexer\Product;

use Magento\CatalogRule\Model\Indexer\Product\IndexProcessor;
use Magento\TargetRule\Model\Resource\Rule\CollectionFactory as RuleCollectionFactory;

class ObjectWhichWorkWithCatalogRulesAndIndexer
{
    /**
     * @var RuleCollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * @var IndexProcessor
     */
    protected $indexer;

    /**
     * @param RuleCollectionFactory $ruleCollectionFactory
     * @param IndexProcessor $indexer
     */
    public function __construct(
        RuleCollectionFactory $ruleCollectionFactory,
        IndexProcessor $indexer
    ) {
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->indexer = $indexer;
    }

    /**
     * Reindex by id
     *
     * @param int $id
     * @return void
     */
    public function reindexById($id)
    {
        $rules = $this->ruleCollectionFactory->create()
            ->addFieldToFilter('is_active', 1);

        $collectedData = [];
        foreach ($rules as $rule) {
            // TODO: only array_intersect($product->getWebsiteIds(), $rule->getWebsiteIds())
            $collectedData[] = $rule->getId();
        }
        $this->indexer->reindexRow($id);
    }

    /**
     * Reindex by ids
     *
     * @param array $ids
     * @return void
     */
    public function reindexByIds(array $ids)
    {
        $rules = $this->ruleCollectionFactory->create()
            ->addFieldToFilter('is_active', 1);

        $collectedData = [];
        foreach ($rules as $rule) {
            // TODO: only array_intersect($product->getWebsiteIds(), $rule->getWebsiteIds())
            $collectedData[] = $rule->getId();
        }
        $this->indexer->reindexList($collectedData);
    }

    /**
     * Reindex all
     *
     * @return void
     */
    public function reindexAll()
    {
    }

    /**
     * Clean all
     *
     * @return void
     */
    public function cleanAll()
    {
    }
}
