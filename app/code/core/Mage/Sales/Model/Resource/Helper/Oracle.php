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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Sales Oracle resource helper model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Helper_Oracle extends Mage_Core_Model_Resource_Helper_Oracle
{
    /**
     * Update rating position
     *
     * @param string $aggregation One of Mage_Sales_Model_Resource_Report_Bestsellers::AGGREGATION_XXX constants
     * @param array $aggregationAliases
     * @param string $mainTable
     * @param string $aggregationTable
     * @return Mage_Sales_Model_Resource_Helper_Abstract
     */
    public function getBestsellersReportUpdateRatingPos($aggregation, $aggregationAliases, $mainTable, $aggregationTable)
    {
        $adapter         = $this->_getWriteAdapter();
        $periodSubSelect = $adapter->select();
        $ratingSubSelect = $adapter->select();
        $ratingSelect    = $adapter->select();

        $periodCol = 't.period';
        if ($aggregation == $aggregationAliases['monthly']) {
            $periodCol = $adapter->getDateFormatSql('t.period', '%Y-%m-01');
        } elseif ($aggregation == $aggregationAliases['yearly']) {
            $periodCol = $adapter->getDateFormatSql('t.period', '%Y-01-01');
        }

        $cols = array(
            'period'            => 't.period',
            'store_id'          => 't.store_id',
            'product_id'        => 't.product_id',
            'product_name'      => new Zend_Db_expr('MAX(t.product_name)'),
            'product_price'     => new Zend_Db_expr('MAX(t.product_price)'),
            'total_qty_ordered' => new Zend_Db_expr('SUM(t.qty_ordered)')
        );

        $periodSubSelect->from(array('t' => $mainTable), $cols)
            ->group(array('t.store_id', $periodCol, 't.product_id'));

        $periodSubSelect = $this->getQueryUsingAnalyticFunction($periodSubSelect);

        $columns = array(
            'period'        => 't.period',
            'store_id'      => 't.store_id',
            'product_id'    => 't.product_id',
            'product_name'  => 't.product_name',
            'product_price' => 't.product_price',
        );

        $cols = $columns;
        $cols['qty_ordered'] = 't.total_qty_ordered';
        $orderByColumns      = array(
            't.store_id ' . Varien_Db_Select::SQL_ASC,
            't.period ' . Varien_Db_Select::SQL_ASC,
            'total_qty_ordered ' . Varien_Db_Select::SQL_DESC
        );
        $cols['rating_pos']  = $this->prepareColumn('RANK()', 't.store_id, t.period', $orderByColumns);
        $ratingSubSelect->from(new Zend_Db_Expr(sprintf('(%s)', $periodSubSelect)), $cols);

        $cols = $columns;
        $cols['period']      = $periodCol;  // important!
        $cols['qty_ordered'] = 't.qty_ordered';
        $cols['rating_pos']  = 't.rating_pos';
        $ratingSelect->from($ratingSubSelect, $cols);
        $sql = $ratingSelect->insertFromSelect($aggregationTable, array_keys($cols));

        $adapter->query($sql);

        return $this;
    }
}
