<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Report Sold Products collection
 *
 * @category    Magento
 * @package     Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reports_Model_Resource_Product_Sold_Collection extends Magento_Reports_Model_Resource_Product_Collection
{
    /**
     * Set Date range to collection
     *
     * @param int $from
     * @param int $to
     * @return Magento_Reports_Model_Resource_Product_Sold_Collection
     */
    public function setDateRange($from, $to)
    {
        $this->_reset()
            ->addAttributeToSelect('*')
            ->addOrderedQty($from, $to)
            ->setOrder('ordered_qty', self::SORT_ORDER_DESC);
        return $this;
    }

    /**
     * Set store filter to collection
     *
     * @param array $storeIds
     * @return Magento_Reports_Model_Resource_Product_Sold_Collection
     */
    public function setStoreIds($storeIds)
    {
        if ($storeIds) {
            $this->getSelect()->where('order_items.store_id IN (?)', (array)$storeIds);
        }
        return $this;
    }

    /**
     * Add website product limitation
     *
     * @return Magento_Reports_Model_Resource_Product_Sold_Collection
     */
    protected function _productLimitationJoinWebsite()
    {
        $filters     = $this->_productLimitationFilters;
        $conditions  = array('product_website.product_id=e.entity_id');
        if (isset($filters['website_ids'])) {
            $conditions[] = $this->getConnection()
                ->quoteInto('product_website.website_id IN(?)', $filters['website_ids']);

            $subQuery = $this->getConnection()->select()
                ->from(array('product_website' => $this->getTable('catalog_product_website')),
                    array('product_website.product_id')
                )
                ->where(join(' AND ', $conditions));
            $this->getSelect()->where('e.entity_id IN( '.$subQuery.' )');
        }

        return $this;
    }
}
