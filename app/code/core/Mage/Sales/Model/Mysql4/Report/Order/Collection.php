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
class Mage_Sales_Model_Mysql4_Report_Order_Collection extends Mage_Sales_Model_Mysql4_Report_Collection_Abstract
{
    /**
     * Initialize custom resource model
     *
     * @param array $parameters
     */

    public function __construct()
    {
        parent::_construct();
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('sales/report')->init('sales/order_aggregated_created');
        $this->setConnection($this->getResource()->getReadConnection());
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
}
