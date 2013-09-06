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
 * Customers by totals Report collection
 *
 * @category    Magento
 * @package     Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reports_Model_Resource_Customer_Totals_Collection extends Magento_Reports_Model_Resource_Order_Collection
{
    /**
     * Join fields
     *
     * @param string $fromDate
     * @param string $toDate
     * @return Magento_Reports_Model_Resource_Customer_Totals_Collection
     */
    protected function _joinFields($fromDate = '', $toDate = '')
    {
        $this->joinCustomerName()
            ->groupByCustomer()
            ->addOrdersCount()
            ->addAttributeToFilter('created_at', array('from' => $fromDate, 'to' => $toDate, 'datetime' => true));
        return $this;
    }

    /**
     * Set date range
     *
     * @param string $fromDate
     * @param string $toDate
     * @return Magento_Reports_Model_Resource_Customer_Totals_Collection
     */
    public function setDateRange($fromDate, $toDate)
    {
        $this->_reset()
            ->_joinFields($fromDate, $toDate);
        return $this;
    }

    /**
     * Set store filter collection
     *
     * @param array $storeIds
     * @return Magento_Reports_Model_Resource_Customer_Totals_Collection
     */
    public function setStoreIds($storeIds)
    {
        if ($storeIds) {
            $this->addAttributeToFilter('store_id', array('in' => (array)$storeIds));
            $this->addSumAvgTotals(1)
                ->orderByTotalAmount();
        } else {
            $this->addSumAvgTotals()
                ->orderByTotalAmount();
        }

        return $this;
    }
}
