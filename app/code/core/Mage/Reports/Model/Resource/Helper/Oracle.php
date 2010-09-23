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
 * @package     Mage_Reports
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Eav Mssql resource helper model
 *
 * @category    Mage
 * @package     Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Resource_Helper_Oracle extends Mage_Core_Model_Resource_Helper_Oracle
{
    /**
     * Adds SUM and AVG columns
     *
     * @param int $storeId
     * @param  $collection
     * @return void
     */
    public function orderCollectionaddSumAvgTotals($storeId = 0, $collection)
    {
        $adapter = $collection->getSelect()->getAdapter();
        $baseSubtotalRefunded = $adapter->getCheckSql('main_table.base_subtotal_refunded IS NULL', 0, 'main_table.base_subtotal_refunded');
        $baseSubtotalCanceled = $adapter->getCheckSql('main_table.base_subtotal_canceled IS NULL', 0, 'main_table.base_subtotal_canceled');
        /**
         * calculate average and total amount
         */
        $expr = ($storeId == 0)
            ? "(main_table.base_subtotal - {$baseSubtotalRefunded} - {$baseSubtotalCanceled}) * main_table.base_to_global_rate"
            : "main_table.base_subtotal - {$baseSubtotalCanceled} - {$baseSubtotalRefunded}";


        $group = $collection->getSelect()->getPart(Zend_Db_Select::GROUP);
        $innerSelect = $adapter->select()
            ->from(
                array('main_table' => $collection->getMainTable()),
                array(
                    'orders_avg_amount' =>
                        $this->getAnalyticColumn("AVG({$expr})", $group),
                    'orders_sum_amount' =>
                        $this->getAnalyticColumn("SUM({$expr})", $group),
                    'entity_id'
                )
            )
            ->columns($group)
            ->magicGroup($group);

        $collection->getSelect()
            ->join(
                array('avg_and_sum' => $innerSelect),
                'main_table.entity_id = avg_and_sum.entity_id',
                array('orders_avg_amount', 'orders_sum_amount')
            );
    }

}
