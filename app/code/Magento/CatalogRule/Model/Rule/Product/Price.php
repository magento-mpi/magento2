<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Rule Product Aggregated Price per date Model
 *
 * @method Magento_CatalogRule_Model_Resource_Rule_Product_Price _getResource()
 * @method Magento_CatalogRule_Model_Resource_Rule_Product_Price getResource()
 * @method string getRuleDate()
 * @method Magento_CatalogRule_Model_Rule_Product_Price setRuleDate(string $value)
 * @method int getCustomerGroupId()
 * @method Magento_CatalogRule_Model_Rule_Product_Price setCustomerGroupId(int $value)
 * @method int getProductId()
 * @method Magento_CatalogRule_Model_Rule_Product_Price setProductId(int $value)
 * @method float getRulePrice()
 * @method Magento_CatalogRule_Model_Rule_Product_Price setRulePrice(float $value)
 * @method int getWebsiteId()
 * @method Magento_CatalogRule_Model_Rule_Product_Price setWebsiteId(int $value)
 * @method string getLatestStartDate()
 * @method Magento_CatalogRule_Model_Rule_Product_Price setLatestStartDate(string $value)
 * @method string getEarliestEndDate()
 * @method Magento_CatalogRule_Model_Rule_Product_Price setEarliestEndDate(string $value)
 *
 * @category    Magento
 * @package     Magento_CatalogRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogRule_Model_Rule_Product_Price extends Magento_Core_Model_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_CatalogRule_Model_Resource_Rule_Product_Price');
    }

    /**
     * Apply price rule price to price index table
     *
     * @param \Magento\DB\Select $select
     * @param array|string $indexTable
     * @param string $entityId
     * @param string $customerGroupId
     * @param string $websiteId
     * @param array $updateFields       the array fields for compare with rule price and update
     * @param string $websiteDate
     * @return Magento_CatalogRule_Model_Rule_Product_Price
     */
    public function applyPriceRuleToIndexTable(\Magento\DB\Select $select, $indexTable, $entityId, $customerGroupId,
        $websiteId, $updateFields, $websiteDate)
    {

        $this->_getResource()->applyPriceRuleToIndexTable($select, $indexTable, $entityId, $customerGroupId, $websiteId,
            $updateFields, $websiteDate);

        return $this;
    }
}
