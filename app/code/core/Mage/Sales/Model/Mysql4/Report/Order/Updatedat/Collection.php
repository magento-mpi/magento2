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
 * Report order updated_at collection
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Mysql4_Report_Order_Updatedat_Collection extends Mage_Sales_Model_Mysql4_Report_Collection_Abstract
{
    protected $_inited = false;

    /**
     * Initialize custom resource model
     *
     * @param array $parameters
     */
    public function __construct()
    {
        parent::_construct();
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('sales/report')->init('sales/order', 'entity_id');
        $this->setConnection($this->getResource()->getReadConnection());
    }

    /**
     * Apply stores filter
     *
     * @return Mage_Sales_Model_Mysql4_Report_Order_Updatedat_Collection
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

        if ($nullCheck) {
            $this->getSelect()->where('store_id IN(?) OR store_id IS NULL', $storeIds);
        } elseif ($storeIds[0] != '') {
            $this->getSelect()->where('store_id IN(?)', $storeIds);
        }

        return $this;
    }

    /**
     * Add selected data
     *
     * @return Mage_Sales_Model_Mysql4_Report_Order_Updatedat_Collection
     */
    protected function _initSelect()
    {
        if ($this->_inited) {
            return $this;
        }
        if ('month' == $this->_period) {
            $period = 'DATE_FORMAT(e.updated_at, \'%Y-%m\')';
        } elseif ('year' == $this->_period) {
            $period = 'EXTRACT(YEAR FROM e.updated_at)';
        } else {
            $period = 'DATE(e.updated_at)';
        }

        $columns = array(
            'period'                    => $period,
            'orders_count'              => 'COUNT(e.entity_id)',
            'total_qty_ordered'         => 'SUM(oa.total_qty)',
            'base_profit_amount'        => 'SUM(e.base_total_paid * e.base_to_global_rate) - SUM(e.base_total_refunded * e.base_to_global_rate) - SUM(e.base_total_invoiced_cost * e.base_to_global_rate)',
            'base_subtotal_amount'      => 'SUM(e.base_subtotal * e.base_to_global_rate)',
            'base_tax_amount'           => 'SUM(e.base_tax_amount * e.base_to_global_rate)',
            'base_shipping_amount'      => 'SUM(e.base_shipping_amount * e.base_to_global_rate)',
            'base_discount_amount'      => 'SUM(e.base_discount_amount * e.base_to_global_rate)',
            'base_grand_total_amount'   => 'SUM(e.base_grand_total * e.base_to_global_rate)',
            'base_invoiced_amount'      => 'SUM(e.base_total_paid * e.base_to_global_rate)',
            'base_refunded_amount'      => 'SUM(e.base_total_refunded * e.base_to_global_rate)',
        );

        $mainTable = $this->getResource()->getMainTable();

        if (!is_null($this->_from) || !is_null($this->_to)) {
            $where = (!is_null($this->_from)) ? "so.updated_at >= '{$this->_from}'" : '';
            if (!is_null($this->_to)) {
                $where .= (!empty($where)) ? " AND so.updated_at <= '{$this->_to}'" : "so.updated_at <= '{$this->_to}'";
            }

            $subQuery = clone $this->getSelect();
            $subQuery->from(array('so' => $mainTable), array('DISTINCT DATE(so.updated_at)'))
                ->where($where);
        }

        $qtySelect = clone $this->getSelect();
        $qtySelect->from(array('p' => $this->getTable('sales/order_item')), array())
            ->columns(array(
                'order_id',
                'total_qty'  => 'IFNULL(SUM(c.qty_ordered), SUM(p.qty_ordered))'
            ))
            ->joinInner(array('o' => $mainTable), 'p.order_id = o.entity_id', array())
            ->joinLeft(array('c' => $this->getTable('sales/order_item')),
                'c.parent_item_id IS NOT NULL AND p.item_id = c.parent_item_id', array()
            )
            ->where('p.parent_item_id IS NULL')
            ->where('o.state <> ?', 'pending');

            if (!is_null($this->_from) || !is_null($this->_to)) {
                $qtySelect->where("DATE(o.updated_at) IN(?)", $subQuery);
            }

            $qtySelect->group('p.order_id');

        $select = $this->getSelect()
            ->from(array('e' => $mainTable), array())
            ->columns($columns)
            ->joinLeft(array('oa'=> $qtySelect), 'e.entity_id = oa.order_id', array())
            ->where('e.state <> ?', 'pending');

            $this->_applyStoresFilter();
            $this->_applyOrderStatusFilter();

            if (!is_null($this->_from) || !is_null($this->_to)) {
                $select->where("DATE(e.updated_at) IN(?)", $subQuery);
            }

            $select->group($period);
        $this->_inited = true;
        return $this;
    }

    /**
     * Load
     *
     * @param boolean $printQuery
     * @param boolean $logQuery
     * @return Mage_Sales_Model_Mysql4_Report_Order_Updatedat_Collection
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        $this->_initSelect();
        $this->setApplyFilters(false);
        return parent::load($printQuery, $logQuery);
    }

}
