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
 * @method \Magento\CatalogRule\Model\Resource\Rule\Product\Price _getResource()
 * @method \Magento\CatalogRule\Model\Resource\Rule\Product\Price getResource()
 * @method string getRuleDate()
 * @method \Magento\CatalogRule\Model\Rule\Product\Price setRuleDate(string $value)
 * @method int getCustomerGroupId()
 * @method \Magento\CatalogRule\Model\Rule\Product\Price setCustomerGroupId(int $value)
 * @method int getProductId()
 * @method \Magento\CatalogRule\Model\Rule\Product\Price setProductId(int $value)
 * @method float getRulePrice()
 * @method \Magento\CatalogRule\Model\Rule\Product\Price setRulePrice(float $value)
 * @method int getWebsiteId()
 * @method \Magento\CatalogRule\Model\Rule\Product\Price setWebsiteId(int $value)
 * @method string getLatestStartDate()
 * @method \Magento\CatalogRule\Model\Rule\Product\Price setLatestStartDate(string $value)
 * @method string getEarliestEndDate()
 * @method \Magento\CatalogRule\Model\Rule\Product\Price setEarliestEndDate(string $value)
 *
 * @category    Magento
 * @package     Magento_CatalogRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogRule\Model\Rule\Product;

class Price extends \Magento\Core\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\CatalogRule\Model\Resource\Rule\Product\Price');
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
     * @return \Magento\CatalogRule\Model\Rule\Product\Price
     */
    public function applyPriceRuleToIndexTable(\Magento\DB\Select $select, $indexTable, $entityId, $customerGroupId,
        $websiteId, $updateFields, $websiteDate)
    {

        $this->_getResource()->applyPriceRuleToIndexTable($select, $indexTable, $entityId, $customerGroupId, $websiteId,
            $updateFields, $websiteDate);

        return $this;
    }
}
