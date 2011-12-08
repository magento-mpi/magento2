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
 * Staging unlinked product model
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Staging_Product_Unlinked extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Enterprise_Staging_Model_Resource_Staging_Product_Unlinked');
    }

    /**
     * Check if specified websites ids are staging websites ids
     *
     * @param  array $websiteIds
     * @return array
     */
    protected function _prepareWebsiteIds($websiteIds)
    {
        if (!is_array($websiteIds)) {
            $websiteIds = array($websiteIds);
        }

        $stagingWebsiteIds = array();
        foreach ($websiteIds as $websiteId) {
            $website = Mage::app()->getWebsite($websiteId);
            if ($website && $website->getIsStaging()) {
                $stagingWebsiteIds[] = $websiteId;
            }
        }

        return $stagingWebsiteIds;
    }

    /**
     * Add products unlink associations to staging websites
     *
     * @param  int|array $productIds
     * @param  int|array $websiteIds
     * @return Enterprise_Staging_Model_Staging_Product_Unlinked
     */
    public function addProductsUnlinkAssociations($productIds, $websiteIds)
    {
        $websiteIds = $this->_prepareWebsiteIds($websiteIds);

        try {
            $this->_getResource()->addProductsUnlinkAssociations($productIds, $websiteIds);
        } catch (Exception $e) {
            Mage::throwException(
                Mage::helper('Enterprise_Staging_Helper_Data')->__('An error occurred while adding products that must be unlinked on merge with staging website.')
            );
        }

        return $this;
    }

    /**
     * Remove products unlink associations to staging websites
     *
     * @param  int|array $productIds
     * @param  int|array $websiteIds
     * @return Enterprise_Staging_Model_Staging_Product_Unlinked
     */
    public function removeProductsUnlinkAssociations($productIds, $websiteIds)
    {
        $websiteIds = $this->_prepareWebsiteIds($websiteIds);

        try {
            $this->_getResource()->removeProductsUnlinkAssociations($productIds, $websiteIds);
        } catch (Exception $e) {
            Mage::throwException(
                Mage::helper('Enterprise_Staging_Helper_Data')->__('An error occurred while removing products that must be unlinked on merge with staging website.')
            );
        }

        return $this;
    }
}
