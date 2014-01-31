<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Category;

class Flat implements \Magento\Indexer\Model\ActionInterface, \Magento\Mview\ActionInterface
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
     * @param Flat\Action\FullFactory $fullActionFactory
     * @param Flat\Action\RowsFactory $rowsActionFactory
     */
    public function __construct(
        Flat\Action\FullFactory $fullActionFactory,
        Flat\Action\RowsFactory $rowsActionFactory
    ) {
        $this->fullActionFactory = $fullActionFactory;
        $this->rowsActionFactory = $rowsActionFactory;
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     */
    public function execute($ids)
    {
        $this->rowsActionFactory->create()->reindex($ids);
    }

    /**
     * Execute full indexation
     */
    public function executeFull()
    {
        $this->fullActionFactory->create()->reindexAll();
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     */
    public function executeList($ids)
    {
        $this->rowsActionFactory->create()->reindex($ids);
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     */
    public function executeRow($id)
    {
        $this->rowsActionFactory->create()->reindex(array($id));
    }
}
