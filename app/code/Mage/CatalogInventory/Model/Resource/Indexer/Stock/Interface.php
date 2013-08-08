<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CatalogInventory Stock Indexer Interface
 *
 * @category    Mage
 * @package     Mage_CatalogInventory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Mage_CatalogInventory_Model_Resource_Indexer_Stock_Interface
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
     * @throws Magento_Core_Exception
     *
     */
    public function getTypeId()
;
}
