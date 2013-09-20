<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * CatalogSearch Index Engine Interface
 */
interface Magento_CatalogSearch_Model_Resource_EngineInterface
{
    /**
     * Add entity data to fulltext search table
     *
     * @param int $entityId
     * @param int $storeId
     * @param array $index
     * @param string $entity 'product'|'cms'
     * @return Magento_CatalogSearch_Model_Resource_EngineInterface
     */
    public function saveEntityIndex($entityId, $storeId, $index, $entity = 'product');

    /**
     * Multi add entities data to fulltext search table
     *
     * @param int $storeId
     * @param array $entityIndexes
     * @param string $entity 'product'|'cms'
     * @return Magento_CatalogSearch_Model_Resource_EngineInterface
     */
    public function saveEntityIndexes($storeId, $entityIndexes, $entity = 'product');

    /**
     * Retrieve allowed visibility values for current engine
     *
     * @return array
     */
    public function getAllowedVisibility();

    /**
     * Define if current search engine supports advanced index
     *
     * @return bool
     */
    public function allowAdvancedIndex();

    /**
     * Remove entity data from fulltext search table
     *
     * @param int $storeId
     * @param int $entityId
     * @param string $entity 'product'|'cms'
     * @return Magento_CatalogSearch_Model_Resource_EngineInterface
     */
    public function cleanIndex($storeId = null, $entityId = null, $entity = 'product');

    /**
     * Prepare index array as a string glued by separator
     *
     * @param array $index
     * @param string $separator
     * @return string
     */
    public function prepareEntityIndex($index, $separator = ' ');

    /**
     * Return resource model for the full text search
     *
     * @return Magento_Core_Model_Resource_Abstract
     */
    public function getResource();

    /**
     * Return resource collection model for the full text search
     *
     * @return Magento_Data_Collection_Db
     */
    public function getResourceCollection();

    /**
     * Retrieve fulltext search result data collection
     *
     * @return Magento_Catalog_Model_Resource_Product_Collection
     */
    public function getResultCollection();

    /**
     * Retrieve advanced search result data collection
     *
     * @return Magento_Catalog_Model_Resource_Product_Collection
     */
    public function getAdvancedResultCollection();

    /**
     * Define if Layered Navigation is allowed
     *
     * @return bool
     */
    public function isLayeredNavigationAllowed();

    /**
     * Define if engine is available
     *
     * @return bool
     */
    public function test();
}
