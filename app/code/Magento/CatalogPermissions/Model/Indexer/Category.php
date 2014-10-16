<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogPermissions\Model\Indexer;

class Category implements \Magento\Indexer\Model\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    /**
     * Indexer ID in configuration
     */
    const INDEXER_ID = 'catalogpermissions_category';

    /**
     * @var Category\Action\FullFactory
     */
    protected $fullActionFactory;

    /**
     * @var Category\Action\RowsFactory
     */
    protected $rowsActionFactory;

    /**
     * @param Category\Action\FullFactory $fullActionFactory
     * @param Category\Action\RowsFactory $rowsActionFactory
     * @param \Magento\Indexer\Model\IndexerInterface $indexer
     */
    public function __construct(
        Category\Action\FullFactory $fullActionFactory,
        Category\Action\RowsFactory $rowsActionFactory,
        \Magento\Indexer\Model\IndexerInterface $indexer
    ) {
        $this->fullActionFactory = $fullActionFactory;
        $this->rowsActionFactory = $rowsActionFactory;
        $this->indexer = $indexer;
    }

    /**
     * Execute full indexation
     *
     * @return void
     */
    public function executeFull()
    {
        $this->fullActionFactory->create()->execute();
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     * @return void
     */
    public function executeList(array $ids)
    {
        $this->executeAction($ids);
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     * @return void
     */
    public function executeRow($id)
    {
        $this->executeAction(array($id));
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     * @return void
     */
    public function execute($ids)
    {
        $this->executeAction($ids);
    }

    /**
     * Execute action for single entity or list of entities
     *
     * @param int[] $ids
     * @return void
     */
    protected function executeAction($ids)
    {
        $ids = array_unique($ids);
        $this->indexer->load(static::INDEXER_ID);

        /** @var Category\Action\Rows $action */
        $action = $this->rowsActionFactory->create();
        if ($this->indexer->isWorking()) {
            $action->execute($ids, true);
        }
        $action->execute($ids);
    }
}
