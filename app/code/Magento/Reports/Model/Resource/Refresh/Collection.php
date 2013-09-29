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
 *  Refresh Statistic Grid collection
 *
 * @category    Magento
 * @package     Magento_Report
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Model\Resource\Refresh;

class Collection extends \Magento\Data\Collection
{
    /**
     * Get if updated
     *
     * @param $reportCode
     * @return string|Zend_Date
     */
    protected function _getUpdatedAt($reportCode)
    {
        $flag = \Mage::getModel('Magento\Reports\Model\Flag')->setReportFlagCode($reportCode)->loadSelf();
        return ($flag->hasData())
            ? \Mage::app()->getLocale()
                ->storeDate(0, new \Zend_Date($flag->getLastUpdate(), \Magento\Date::DATETIME_INTERNAL_FORMAT), true)
            : '';
    }

    /**
     * Load data
     * @return \Magento\Reports\Model\Resource\Refresh\Collection|\Magento\Data\Collection
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if (!count($this->_items)) {
            $data = array(
                array(
                    'id'            => 'sales',
                    'report'        => __('Orders'),
                    'comment'       => __('Total Ordered Report'),
                    'updated_at'    => $this->_getUpdatedAt(\Magento\Reports\Model\Flag::REPORT_ORDER_FLAG_CODE)
                ),
                array(
                    'id'            => 'tax',
                    'report'        => __('Tax'),
                    'comment'       => __('Order Taxes Report Grouped by Tax Rates'),
                    'updated_at'    => $this->_getUpdatedAt(\Magento\Reports\Model\Flag::REPORT_TAX_FLAG_CODE)
                ),
                array(
                    'id'            => 'shipping',
                    'report'        => __('Shipping'),
                    'comment'       => __('Total Shipped Report'),
                    'updated_at'    => $this->_getUpdatedAt(\Magento\Reports\Model\Flag::REPORT_SHIPPING_FLAG_CODE)
                ),
                array(
                    'id'            => 'invoiced',
                    'report'        => __('Total Invoiced'),
                    'comment'       => __('Total Invoiced VS Paid Report'),
                    'updated_at'    => $this->_getUpdatedAt(\Magento\Reports\Model\Flag::REPORT_INVOICE_FLAG_CODE)
                ),
                array(
                    'id'            => 'refunded',
                    'report'        => __('Total Refunded'),
                    'comment'       => __('Total Refunded Report'),
                    'updated_at'    => $this->_getUpdatedAt(\Magento\Reports\Model\Flag::REPORT_REFUNDED_FLAG_CODE)
                ),
                array(
                    'id'            => 'coupons',
                    'report'        => __('Coupons'),
                    'comment'       => __('Promotion Coupons Usage Report'),
                    'updated_at'    => $this->_getUpdatedAt(\Magento\Reports\Model\Flag::REPORT_COUPONS_FLAG_CODE)
                ),
                array(
                    'id'            => 'bestsellers',
                    'report'        => __('Bestsellers'),
                    'comment'       => __('Products Bestsellers Report'),
                    'updated_at'    => $this->_getUpdatedAt(\Magento\Reports\Model\Flag::REPORT_BESTSELLERS_FLAG_CODE)
                ),
                array(
                    'id'            => 'viewed',
                    'report'        => __('Most Viewed'),
                    'comment'       => __('Most Viewed Products Report'),
                    'updated_at'    => $this->_getUpdatedAt(\Magento\Reports\Model\Flag::REPORT_PRODUCT_VIEWED_FLAG_CODE)),
            );
            foreach ($data as $value) {
                $item = new \Magento\Object();
                $item->setData($value);
                $this->addItem($item);
            }
        }
        return $this;
    }
}
