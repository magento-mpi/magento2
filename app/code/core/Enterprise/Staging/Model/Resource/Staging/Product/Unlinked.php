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
 * Staging unlinked product resource
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Resource_Staging_Product_Unlinked extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('enterprise_staging_product_unlinked', 'product_id');
    }

    /**
     * Add products that must be unlinked on merge staging website with master
     *
     * @param  int|array $productIds
     * @param  int|array $websiteIds
     * @return Enterprise_Staging_Model_Resource_Staging_Product_Unlinked
     */
    public function addProductsUnlinkAssociations($productIds, $websiteIds)
    {
        if (empty($productIds) || empty($websiteIds)) {
            return $this;
        }
        if (!is_array($productIds)) {
            $productIds = array($productIds);
        }
        if (!is_array($websiteIds)) {
            $websiteIds = array($websiteIds);
        }

        $writeAdapter = $this->_getWriteAdapter();
        $writeAdapter->beginTransaction();

        try {
            foreach ($websiteIds as $websiteId) {
                foreach ($productIds as $productId) {
                    $writeAdapter->insertOnDuplicate($this->getMainTable(), array(
                        'product_id' => $productId,
                        'website_id' => $websiteId
                    ));
                }
            }

            $writeAdapter->commit();
        } catch (Exception $e) {
            $writeAdapter->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Remove products that must be unlinked on merge staging website with master
     *
     * @param  array $productIds
     * @param  array $websiteIds
     * @return Enterprise_Staging_Model_Resource_Staging_Product_Unlinked
     */
    public function removeProductsUnlinkAssociations($productIds, $websiteIds)
    {
        if (empty($productIds) || empty($websiteIds)) {
            return $this;
        }
        if (!is_array($productIds)) {
            $productIds = array($productIds);
        }
        if (!is_array($websiteIds)) {
            $websiteIds = array($websiteIds);
        }

        $writeAdapter = $this->_getWriteAdapter();
        $writeAdapter->beginTransaction();

        try {
            $writeAdapter->delete($this->getMainTable(), array(
                'product_id IN (?)' => $productIds,
                'website_id IN (?)' => $websiteIds
            ));

            $writeAdapter->commit();
        } catch (Exception $e) {
            $writeAdapter->rollBack();
            throw $e;
        }

        return $this;
    }
}
