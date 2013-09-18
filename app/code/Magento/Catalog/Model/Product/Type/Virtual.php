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
 * Virtual product type implementation
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Type_Virtual extends Magento_Catalog_Model_Product_Type_Abstract
{
    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Helper_File_Storage_Database $fileStorageDb
     * @param Magento_Filesystem $filesystem
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Core_Model_Logger $logger
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Helper_File_Storage_Database $fileStorageDb,
        Magento_Filesystem $filesystem,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Core_Model_Logger $logger,
        array $data = array()
    ) {
        parent::__construct($eventManager, $coreData, $fileStorageDb, $filesystem, $coreRegistry, $logger, $data);
    }

    /**
     * Check is virtual product
     *
     * @param Magento_Catalog_Model_Product $product
     * @return bool
     */
    public function isVirtual($product)
    {
        return true;
    }

    /**
     * Check that product of this type has weight
     *
     * @return bool
     */
    public function hasWeight()
    {
        return false;
    }

    /**
     * Delete data specific for Virtual product type
     *
     * @param Magento_Catalog_Model_Product $product
     */
    public function deleteTypeSpecificData(Magento_Catalog_Model_Product $product)
    {
    }
}
