<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogPermissions\Model\Indexer;

class Product extends Category
{
    /**
     * Indexer ID in configuration
     */
    const INDEXER_ID = 'catalogpermissions_product';

    /**
     * @param Category\Action\FullFactory $fullActionFactory
     * @param Product\Action\RowsFactory $rowsActionFactory
     * @param \Magento\Indexer\Model\IndexerInterface $indexer
     */
    public function __construct(
        Category\Action\FullFactory $fullActionFactory,
        Product\Action\RowsFactory $rowsActionFactory,
        \Magento\Indexer\Model\IndexerInterface $indexer
    ) {
        parent::__construct($fullActionFactory, $rowsActionFactory, $indexer);
    }
}
