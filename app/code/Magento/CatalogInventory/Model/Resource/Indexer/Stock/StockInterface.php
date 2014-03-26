<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Model\Resource\Indexer\Stock;

/**
 * CatalogInventory Stock Indexer Interface
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface StockInterface
{
    /**
     * Reindex all stock status data
     *
     * @return $this
     */
    public function reindexAll();

    /**
     * Reindex stock status data for defined ids
     *
     * @param int|array $entityIds
     * @return $this
     */
    public function reindexEntity($entityIds);

    /**
     * Set Product Type Id for indexer
     *
     * @param string $typeId
     * @return $this
     */
    public function setTypeId($typeId);

    /**
     * Retrieve Product Type Id for indexer
     *
     * @return string
     * @throws \Magento\Model\Exception
     */
    public function getTypeId();
}
