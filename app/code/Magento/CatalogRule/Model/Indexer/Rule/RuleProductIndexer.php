<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogRule\Model\Indexer\Rule;

use Magento\CatalogRule\CatalogRuleException;
use Magento\CatalogRule\Model\Indexer\IndexerBuilder;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;
use Magento\Indexer\Model\ActionInterface as IndexerActionInterface;

class RuleProductIndexer implements IndexerActionInterface, MviewActionInterface
{
    /**
     * @var IndexerBuilder
     */
    protected $indexerBuilder;

    /**
     * @param IndexerBuilder $indexerBuilder
     */
    public function __construct(IndexerBuilder $indexerBuilder)
    {
        $this->indexerBuilder = $indexerBuilder;
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     */
    public function execute($ids)
    {
        $this->executeList($ids);
    }

    /**
     * Execute full indexation
     */
    public function executeFull()
    {
        $this->indexerBuilder->reindexFull();
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     * @throws \Magento\CatalogRule\CatalogRuleException
     */
    public function executeList($ids)
    {
        if (!$ids) {
            throw new CatalogRuleException(__('Could not rebuild index for empty products array'));
        }
        $this->indexerBuilder->reindexFull();
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     * @throws \Magento\CatalogRule\CatalogRuleException
     */
    public function executeRow($id)
    {
        if (!$id) {
            throw new CatalogRuleException(__('Could not rebuild index for undefined product'));
        }
        $this->indexerBuilder->reindexFull();
    }
}
