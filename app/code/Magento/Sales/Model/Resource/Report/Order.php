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
 * Order entity resource model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Report_Order extends Magento_Sales_Model_Resource_Report_Abstract
{
    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('sales_order_aggregated_created', 'id');
    }

    /**
     * Aggregate Orders data
     *
     * @param mixed $from
     * @param mixed $to
     * @return Magento_Sales_Model_Resource_Report_Order
     */
    public function aggregate($from = null, $to = null)
    {
        Mage::getResourceModel('Magento_Sales_Model_Resource_Report_Order_Createdat')->aggregate($from, $to);
        Mage::getResourceModel('Magento_Sales_Model_Resource_Report_Order_Updatedat')->aggregate($from, $to);
        $this->_setFlagData(Magento_Reports_Model_Flag::REPORT_ORDER_FLAG_CODE);

        return $this;
    }
}
