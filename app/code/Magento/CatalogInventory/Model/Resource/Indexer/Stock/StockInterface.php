<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CatalogInventory Stock Indexer Interface
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogInventory\Model\Resource\Indexer\Stock;

interface StockInterface
{
    /**
     * Reindex all stock status data
     *
     */
    public function reindexAll()
;

    /**
     * Reindex stock status data for defined ids
     *
     * @param int|array $entityIds
     */
    public function reindexEntity($entityIds)
;

    /**
     * Set Product Type Id for indexer
     *
     * @param string $typeId
     */
    public function setTypeId($typeId)
;

    /**
     * Retrieve Product Type Id for indexer
     *
     * @throws \Magento\Core\Exception
     *
     */
    public function getTypeId()
;
}
