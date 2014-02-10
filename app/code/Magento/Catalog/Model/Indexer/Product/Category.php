<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Product;

class Category implements \Magento\Indexer\Model\ActionInterface, \Magento\Mview\ActionInterface
{
    /**
     * Indexer ID in configuration
     */
    const INDEXER_ID = 'catalog_product_category';

    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Product\Action\FullFactory
     */
    protected $fullActionFactory;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Category\Action\RowsFactory
     */
    protected $rowsActionFactory;

    /**
     * @var \Magento\Indexer\Model\IndexerInterface
     */
    protected $indexer;

    /**
     * @param \Magento\Catalog\Model\Indexer\Category\Product\Action\FullFactory $fullActionFactory
     * @param Category\Action\RowsFactory $rowsActionFactory
     * @param \Magento\Indexer\Model\IndexerInterface $indexer
     */
    public function __construct(
        \Magento\Catalog\Model\Indexer\Category\Product\Action\FullFactory $fullActionFactory,
        Category\Action\RowsFactory $rowsActionFactory,
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
     */
    public function execute($ids)
    {
        $this->executeAction($ids);
    }

    /**
     * Execute full indexation
     */
    public function executeFull()
    {
        $this->fullActionFactory->create()
            ->execute();
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     */
    public function executeList($ids)
    {
        $this->executeAction($ids);
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
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
        $this->indexer->load(self::INDEXER_ID);
        if ($this->indexer->isInvalid()) {
            return $this;
        }

        /** @var Category\Action\Rows $action */
        $this->rowsActionFactory->create()
            ->execute($ids);

        return $this;
    }
}
