<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category;

class Flat implements \Magento\Indexer\Model\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\Action\FullFactory
     */
    protected $fullActionFactory;

    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\Action\RowsFactory
     */
    protected $rowsActionFactory;

    /**
     * @var \Magento\Indexer\Model\IndexerInterface
     */
    protected $indexer;

    /**
     * @param Flat\Action\FullFactory $fullActionFactory
     * @param Flat\Action\RowsFactory $rowsActionFactory
     * @param \Magento\Indexer\Model\IndexerInterface $indexer
     */
    public function __construct(
        Flat\Action\FullFactory $fullActionFactory,
        Flat\Action\RowsFactory $rowsActionFactory,
        \Magento\Indexer\Model\IndexerInterface $indexer
    ) {
        $this->fullActionFactory = $fullActionFactory;
        $this->rowsActionFactory = $rowsActionFactory;
        $this->indexer = $indexer;
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     * @return void
     */
    public function execute($ids)
    {
        $this->indexer->load(Flat\State::INDEXER_ID);
        if ($this->indexer->isInvalid()) {
            return;
        }

        /** @var Flat\Action\Rows $action */
        $action = $this->rowsActionFactory->create();
        if ($this->indexer->isWorking()) {
            $action->reindex($ids, true);
        }
        $action->reindex($ids);
    }

    /**
     * Execute full indexation
     *
     * @return void
     */
    public function executeFull()
    {
        $this->fullActionFactory->create()->reindexAll();
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     * @return void
     */
    public function executeList(array $ids)
    {
        $this->execute($ids);
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     * @return void
     */
    public function executeRow($id)
    {
        $this->execute(array($id));
    }
}
