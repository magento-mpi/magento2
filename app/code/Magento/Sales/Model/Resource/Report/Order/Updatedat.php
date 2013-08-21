<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Order entity resource model with aggregation by updated at
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Report_Order_Updatedat extends Magento_Sales_Model_Resource_Report_Order_Createdat
{
    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('sales_order_aggregated_updated', 'id');
    }

    /**
     * Aggregate Orders data by order updated at
     *
     * @param mixed $from
     * @param mixed $to
     * @return Magento_Sales_Model_Resource_Report_Order_Updatedat
     */
    public function aggregate($from = null, $to = null)
    {
        return $this->_aggregateByField('updated_at', $from, $to);
    }
}
