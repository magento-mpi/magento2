<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Report order collection
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Mysql4_Report_Order_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_from        = null;
    protected $_to          = null;
    protected $_orderStatus = null;
    protected $_period      = null;
    protected $_storesIds   = 0;

    /**
     * Initialize custom resource model
     *
     * @param array $parameters
     */
    public function __construct($parameters = array())
    {
        parent::_construct();

        $this->_period = (!isset($parameters['period'])) ? 'day' : $parameters['period'];
        $reportDateType = (!isset($parameters['reportDateType']))
            ? Mage_Sales_Model_Order::REPORT_DATE_TYPE_CREATED : $parameters['reportDateType'];

        $this->setModel('varien_object');

        $table = 'sales/order_aggregated_created';
        if ($reportDateType == Mage_Sales_Model_Order::REPORT_DATE_TYPE_UPDATED) {
            $table = 'sales/order_aggregated_updated';
        }

        $this->_resource = Mage::getResourceModel('sales/report')->init($table);
        $this->setConnection($this->getResource()->getReadConnection());
        $this->_initSelect();
    }

    /**
     * Set date range
     *
     * @return Mage_Sales_Model_Mysql4_Report_Order_Collection
     */
    public function setDateRange($from = null, $to = null)
    {
        $this->_from = $from;
        $this->_to = $to;
        return $this;
    }

    /**
     * Apply date range filter
     *
     * @return Mage_Sales_Model_Mysql4_Report_Order_Collection
     */
    protected function _applyDateRangeFilter()
    {
        if (!is_null($this->_from)) {
            $this->getSelect()->where(
                'period ' . (($this->_period == 'day') ? '=' : '>=') . ' ?', $this->_from
            );
        }
        if (!is_null($this->_to)) {
            $this->getSelect()->where('period >= ?', $this->_to);
        }
        return $this;
    }

    /**
     * Add selected data
     *
     * @return Mage_Sales_Model_Mysql4_Report_Order_Collection
     */
    protected function _initSelect()
    {
        if ('month' == $this->_period) {
            $period = 'DATE_FORMAT(period, \'%Y-%m\')';
        } elseif ('year' == $this->_period) {
            $period = 'EXTRACT(YEAR FROM period)';
        } else {
            $period = 'period';
        }

        $columns = array(
            'period'                    => $period,
            'orders_count'              => 'SUM(orders_count)',
            'total_qty_ordered'         => 'SUM(total_qty_ordered)',
            'base_profit_amount'        => 'SUM(base_profit_amount)',
            'base_subtotal_amount'      => 'SUM(base_subtotal_amount)',
            'base_tax_amount'           => 'SUM(base_tax_amount)',
            'base_shipping_amount'      => 'SUM(base_shipping_amount)',
            'base_discount_amount'      => 'SUM(base_discount_amount)',
            'base_grand_total_amount'   => 'SUM(base_grand_total_amount)',
            'base_invoiced_amount'      => 'SUM(base_invoiced_amount)',
            'base_refunded_amount'      => 'SUM(base_refunded_amount)',
        );
        $this->getSelect()->from($this->getResource()->getMainTable(), $columns)->group($period);
        return $this;
    }

    /**
     * Set store ids
     *
     * @param mixed $storeIds (null, int|string, array, array may contain null)
     * @return Mage_Sales_Model_Mysql4_Report_Order_Collection
     */
    public function addStoreFilter($storeIds)
    {
        $this->_storesIds = $storeIds;
        return $this;
    }

    /**
     * Apply stores filter
     *
     * @return Mage_Sales_Model_Mysql4_Report_Order_Collection
     */
    protected function _applyStoresFilter()
    {
        $nullCheck = false;
        $storeIds = $this->_storesIds;

        if (!is_array($storeIds)) {
            $storeIds = array($storeIds);
        }

        $storeIds = array_unique($storeIds);

        if ($index = array_search(null, $storeIds)) {
            unset($storeIds[$index]);
            $nullCheck = true;
        }

        $storeIds[0] = ($storeIds[0] == '') ? 0 : $storeIds[0];

        if ($nullCheck) {
            $this->getSelect()->where('store_id IN(?) OR store_id IS NULL', $storeIds);
        } else {
            $this->getSelect()->where('store_id IN(?)', $storeIds);
        }

        return $this;
    }

    /**
     * Set status filter
     *
     * @param string|array $state
     * @return Mage_Sales_Model_Mysql4_Report_Order_Collection
     */
    public function addOrderStatusFilter($orderStatus)
    {
        $this->_orderStatus = $orderStatus;
        return $this;
    }

    /**
     * Apply order status filter
     *
     * @return Mage_Sales_Model_Mysql4_Report_Order_Collection
     */
    protected function _applyOrderStatusFilter()
    {
        if (is_null($this->_orderStatus)) {
            return $this;
        }
        $orderStatus = $this->_orderStatus;
        if (!is_array($orderStatus)) {
            $orderStatus = array($orderStatus);
        }
        $this->getSelect()->where('order_status IN(?)', $orderStatus);
        return $this;
    }

    /**
     * Load data
     *
     * @return  Varien_Data_Collection_Db
     */
    public function load($printQuery = false, $logQuery = false)
    {
        $this->_applyDateRangeFilter();
        $this->_applyStoresFilter();
        $this->_applyOrderStatusFilter();
        return parent::load($printQuery, $logQuery);
    }
}
