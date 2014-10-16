<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category;

class Product implements \Magento\Indexer\Model\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    /**
     * Indexer ID in configuration
     */
    const INDEXER_ID = 'catalog_category_product';

    /**
     * @var Product\Action\FullFactory
     */
    protected $fullActionFactory;

    /**
     * @var Product\Action\RowsFactory
     */
    protected $rowsActionFactory;

    /**
     * @var \Magento\Indexer\Model\IndexerInterface
     */
    protected $indexer;

    /**
     * @param Product\Action\FullFactory $fullActionFactory
     * @param Product\Action\RowsFactory $rowsActionFactory
     * @param \Magento\Indexer\Model\IndexerInterface $indexer
     */
    public function __construct(
        Product\Action\FullFactory $fullActionFactory,
        Product\Action\RowsFactory $rowsActionFactory,
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
        $this->executeAction($ids);
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
     * Execute action for single entity or list of entities
     *
     * @param int[] $ids
     * @return $this
     */
    protected function executeAction($ids)
    {
        $ids = array_unique($ids);
        $this->indexer->load(static::INDEXER_ID);

        /** @var Product\Action\Rows $action */
        $action = $this->rowsActionFactory->create();
        if ($this->indexer->isWorking()) {
            $action->execute($ids, true);
        }
        $action->execute($ids);

        return $this;
    }
}
