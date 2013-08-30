<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Product Type Price Indexer interface
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Magento_Catalog_Model_Resource_Product_Indexer_Price_Interface
{
    /**
     * Reindex temporary (price result data) for all products
     *
     */
    public function reindexAll()
;

    /**
     * Reindex temporary (price result data) for defined product(s)
     *
     * @param int|array $entityIds
     */
    public function reindexEntity($entityIds)
;

    /**
     * Register data required by product type process in event object
     *
     * @param Magento_Index_Model_Event $event
     */
    public function registerEvent(Magento_Index_Model_Event $event)
;
}
