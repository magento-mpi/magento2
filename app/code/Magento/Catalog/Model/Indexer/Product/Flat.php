<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Product;

class Flat implements \Magento\Indexer\Model\ActionInterface, \Magento\Mview\ActionInterface
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Flat\Action\Row
     */
    protected $_productFlatIndexerRow;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Flat\Action\Rows
     */
    protected $_productFlatIndexerRows;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Flat\Action\Full
     */
    protected $_productFlatIndexerFull;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param Flat\Action\Row $productFlatIndexerRow
     * @param Flat\Action\Rows $productFlatIndexerRows
     * @param Flat\Action\Full $productFlatIndexerFull
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        \Magento\Catalog\Model\Indexer\Product\Flat\Action\Row $productFlatIndexerRow,
        \Magento\Catalog\Model\Indexer\Product\Flat\Action\Rows $productFlatIndexerRows,
        \Magento\Catalog\Model\Indexer\Product\Flat\Action\Full $productFlatIndexerFull
    ) {
        $this->_objectManager = $objectManager;
        $this->_productFlatIndexerRow = $productFlatIndexerRow;
        $this->_productFlatIndexerRows = $productFlatIndexerRows;
        $this->_productFlatIndexerFull = $productFlatIndexerFull;
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     */
    public function execute($ids)
    {
        $this->_productFlatIndexerRows->execute($ids);
    }

    /**
     * Execute full indexation
     */
    public function executeFull()
    {
        $this->_productFlatIndexerFull->execute();
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     */
    public function executeList($ids)
    {
        $this->_productFlatIndexerRows->execute($ids);
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     */
    public function executeRow($id)
    {
        $this->_productFlatIndexerRow->execute($id);
    }
}
