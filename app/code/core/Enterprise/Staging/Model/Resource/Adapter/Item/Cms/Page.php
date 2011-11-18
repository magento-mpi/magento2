<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Staging adapter for CMS pages
 *
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Resource_Adapter_Item_Cms_Page
    extends Enterprise_Staging_Model_Resource_Adapter_Item_Default
{
    /**
     * Executed before merging staging store to master store
     *
     * @param string $entityName
     * @param mixed $fields
     * @param int $masterStoreId
     * @param int $stagingStoreId
     *
     * @return Enterprise_Staging_Model_Resource_Adapter_Item_Cms_Page
     */
    protected function _beforeStoreMerge($entityName, $fields, $masterStoreId, $stagingStoreId)
    {
        if ($entityName == 'cms_page_store') {
            $model = Mage::getResourceSingleton('Mage_Cms_Model_Resource_Page_Service');
            $model->unlinkConflicts($masterStoreId, $stagingStoreId);
        }
        return $this;
    }

    /**
     * Executed before rolling back backup to master store
     *
     * @param string $srcTable
     * @param string $targetTable
     * @param object $connection
     * @param mixed $fields
     * @param int $masterStoreId
     * @param int $stagingStoreId
     *
     * @return Enterprise_Staging_Model_Resource_Adapter_Item_Cms_Page
     */
    protected function _beforeStoreRollback($srcTable, $targetTable, $connection, $fields, $masterStoreId, $stagingStoreId)
    {
        if ($targetTable == 'cms_page_store') {
            $model = Mage::getResourceSingleton('Mage_Cms_Model_Resource_Page_Service');
            $model->unlinkConflicts($masterStoreId, $masterStoreId, $this->getTable($srcTable));
        }
        return $this;
    }
}
