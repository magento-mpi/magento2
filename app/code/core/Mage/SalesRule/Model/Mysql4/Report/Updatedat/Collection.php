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
 * @package     Mage_SalesRule
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales report coupons collection
 *
 * @category   Mage
 * @package    Mage_SalesRule
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_SalesRule_Model_Mysql4_Report_Updatedat_Collection extends Mage_Sales_Model_Mysql4_Report_Order_Updatedat_Collection
{
    protected $_selectedColumns = array(
        'store_id'          => 'e.store_id',
        'order_status'      => 'e.status',
        'coupon_code'       => 'e.coupon_code',
        'coupon_uses'       => 'COUNT(e.`entity_id`)',
        'subtotal_amount'   => 'SUM(e.`base_subtotal` * e.`base_to_global_rate`)',
        'discount_amount'   => 'SUM(e.`base_discount_amount` * e.`base_to_global_rate`)',
        'total_amount'      => 'SUM((e.`base_subtotal` - e.`base_discount_amount`) * e.`base_to_global_rate`)'
    );

    /**
     * Add selected data
     *
     * @return Mage_SalesRule_Model_Mysql4_Report_Updatedat_Collection
     */
    protected function _initSelect()
    {
        if ($this->_inited) {
            return $this;
        }

        $columns = $this->_getSelectedColumns();

        $mainTable = $this->getResource()->getMainTable();

        $select = $this->getSelect()
            ->from(array('e' => $mainTable), $columns);

        $this->_applyStoresFilter();
        $this->_applyOrderStatusFilter();

        if ($this->_to !== null) {
            $select->where('DATE(e.updated_at) <= DATE(?)', $this->_to);
        }

        if ($this->_from !== null) {
            $select->where('DATE(e.updated_at) >= DATE(?)', $this->_from);
        }

        if (!$this->isTotals()) {
            $select->group($this->_periodFormat);
        }

        $this->_inited = true;
        return $this;
    }
}
