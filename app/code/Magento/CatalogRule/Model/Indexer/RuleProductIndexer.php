<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogRule\Model\Indexer;

use Magento\CatalogRule\Model\Indexer\Rule\Action;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;
use Magento\Indexer\Model\ActionInterface as IndexerActionInterface;
use Magento\Indexer\Model\IndexerInterface;

class RuleProductIndexer implements IndexerActionInterface, MviewActionInterface
{
    /**
     * TODO: create state object (see \Magento\CatalogRule\Model\Indexer\Product\IndexProcessor)
     * Indexer id
     */
    const INDEXER_ID = 'catalogrule_rule';

    /**
     * @var Action\Full
     */
    protected $fullAction;

    /**
     * @var Action\Rows
     */
    protected $rowsAction;

    /**
     * @var IndexerInterface
     */
    protected $indexer;

    /**
     * @param Action\Full $fullAction
     * @param Action\Rows $rowsAction
     * @param IndexerInterface $indexer
     */
    public function __construct(
        Action\Full $fullAction,
        Action\Rows $rowsAction,
        IndexerInterface $indexer
    ) {
        $this->fullAction = $fullAction;
        $this->rowsAction = $rowsAction;
        $this->indexer = $indexer;
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     */
    public function execute($ids)
    {
        $ids = array_unique($ids);

        $this->executeList($ids);
    }

    /**
     * Execute full indexation
     */
    public function executeFull()
    {
        $this->fullAction->execute();
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     */
    public function executeList($ids)
    {
        $ids = array_unique($ids);

        $this->executeAction($ids);
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     */
    public function executeRow($id)
    {
        $this->executeAction([$id]);
    }

    /**
     * Execute action for single entity or list of entities
     *
     * @param int[] $ids
     * @return $this
     */
    protected function executeAction($ids)
    {
        $this->indexer->load(static::INDEXER_ID);

        if (!$this->indexer->isWorking()) {
            $this->fullAction->execute();
        }

        return $this;
    }
}
