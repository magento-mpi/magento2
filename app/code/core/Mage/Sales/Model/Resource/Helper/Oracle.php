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

        $postfix = false;
        $periodCol = 'tt.period';
        if ($aggregation == $aggregationAliases['monthly']) {
            $postfix = $adapter->quote('-01');
            $periodCol = $adapter->getDateFormatSql('tt.period', '%Y-%m');
        } elseif ($aggregation == $aggregationAliases['yearly']) {
            $postfix =$adapter->quote('-01-01');
            $periodCol = $adapter->getDateFormatSql('tt.period', '%Y');
        }
        // SubLevel 2
        $periodSubSelect->from(array('tt' => $mainTable), array(
                'period'            => $periodCol,
                'store_id'          => 'tt.store_id',
                'product_id'        => 'tt.product_id',
                'product_name'      => new Zend_Db_expr('MAX(tt.product_name)'),
                'product_price'     => new Zend_Db_expr('MAX(tt.product_price)'),
                'total_qty_ordered' => new Zend_Db_expr('SUM(tt.qty_ordered)')
        ))->group(array('tt.store_id', $periodCol, 'tt.product_id'));

        // SubLevel 1
        $orderByColumns      = array(
            'tr.store_id ' . Varien_Db_Select::SQL_ASC,
            'tr.period ' . Varien_Db_Select::SQL_ASC,
            'tr.total_qty_ordered ' . Varien_Db_Select::SQL_DESC
        );
        $ratingSubSelect->from(array('tr' => new Zend_Db_Expr(sprintf('(%s)', $periodSubSelect))), array(
            'period'        => 'tr.period',
            'store_id'      => 'tr.store_id',
            'product_id'    => 'tr.product_id',
            'product_name'  => 'tr.product_name',
            'product_price' => 'tr.product_price',
            'qty_ordered'   => 'tr.total_qty_ordered',
            'rating_pos'    => $this->prepareColumn('RANK()', 'tr.store_id, tr.period', $orderByColumns)
        ));
        // Top level
        $cols = array(
            'period'        => ($postfix == false) ? 't.period' : $adapter->getConcatSql(array('t.period', $postfix)),
            'store_id'      => 't.store_id',
            'product_id'    => 't.product_id',
            'product_name'  => 't.product_name',
            'product_price' => 't.product_price',
            'qty_ordered'   => 't.qty_ordered',
            'rating_pos'    => 't.rating_pos'
        );
        $ratingSelect->from(array('t' => new Zend_Db_Expr(sprintf('(%s)', $ratingSubSelect))), $cols);
        $sql = $ratingSelect->insertFromSelect($aggregationTable, array_keys($cols));

        $adapter->query($sql);

        return $this;
    }
}
