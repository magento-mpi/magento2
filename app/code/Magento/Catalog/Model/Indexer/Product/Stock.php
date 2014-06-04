<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Product;

class Stock implements \Magento\Indexer\Model\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Stock\Action\Row
     */
    protected $_productStockIndexerRow;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Stock\Action\Rows
     */
    protected $_productStockIndexerRows;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Stock\Action\Full
     */
    protected $_productStockIndexerFull;

    /**
     * @param Stock\Action\Row $productStockIndexerRow
     * @param Stock\Action\Rows $productStockIndexerRows
     * @param Stock\Action\Full $productStockIndexerFull
     */
    public function __construct(
        \Magento\Catalog\Model\Indexer\Product\Stock\Action\Row $productStockIndexerRow,
        \Magento\Catalog\Model\Indexer\Product\Stock\Action\Rows $productStockIndexerRows,
        \Magento\Catalog\Model\Indexer\Product\Stock\Action\Full $productStockIndexerFull
    ) {
        $this->_productStockIndexerRow = $productStockIndexerRow;
        $this->_productStockIndexerRows = $productStockIndexerRows;
        $this->_productStockIndexerFull = $productStockIndexerFull;
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     */
    public function execute($ids)
    {
        $this->_productStockIndexerRows->execute($ids);
    }

    /**
     * Execute full indexation
     */
    public function executeFull()
    {
        $this->_productStockIndexerFull->execute();
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     */
    public function executeList($ids)
    {
        $this->_productStockIndexerRows->execute($ids);
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     */
    public function executeRow($id)
    {
        $this->_productStockIndexerRow->execute($id);
    }
}
