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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Reports Mssql resource helper model
 *
 * @category    Mage
 * @package     Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Resource_Helper_Mssql extends Mage_Core_Model_Resource_Helper_Mssql
    implements Mage_Reports_Model_Resource_Helper_Interface
{

    /**
     * Merge Index data
     *
     * @param string $mainTable
     * @param array $data
     * @return string
     */
    public function mergeVisitorProductIndex($mainTable, $data, $matchFields)
    {
        $pseudoUnique = array();
        if (!empty($data['visitor_id'])) {
            $pseudoUnique[] = 't1.visitor_id = t2.visitor_id';
        }
        if (!empty($data['customer_id'])) {
            $pseudoUnique[] = 't1.customer_id = t2.customer_id';
        }
        $selectPart = '';
        $matchPart  = '( ' . implode(' OR ', $pseudoUnique) . ')';
        $insertPart = '';
        $updatePart = '';
        $columnsPart = implode(',', array_keys($data));

        foreach ($data as $column => $value) {
            if ($value instanceof Zend_Db_Expr) {
                $selectPart .= sprintf('%s AS %s,', $value, $column);
                unset($data[$column]);
            } else {
                $selectPart .= sprintf(':%s AS %s,', $column, $column);
            }

            $insertPart .= sprintf('t2.%s, ', $column);

            if (!in_array($column, $matchFields)) {
                $updatePart .= sprintf('t1.%s = t2.%s, ', $column, $column);
            } else {
                $matchPart .= sprintf('AND t1.%s = t2.%s ', $column, $column);
            }
        }

        $selectPart = rtrim($selectPart, ', ');
        $updatePart = rtrim($updatePart, ', ');
        $insertPart = rtrim($insertPart, ', ');

        $sql = 'MERGE ' . $mainTable . ' t1 USING ('
            . ' SELECT ' . $selectPart . ' ) t2 ON (' . $matchPart . ' )'
            . ' WHEN MATCHED THEN '
            . ' UPDATE SET ' . $updatePart
            . ' WHEN NOT MATCHED THEN INSERT (' . $columnsPart . ')'
            . ' VALUES ( ' . $insertPart . ');';

        $stmt = $this->_getWriteAdapter()->query($sql, $data);

        $result = $stmt->rowCount();

        return $result;
    }

    /**
     * Update rating position
     *
     * @param string $type day|month|year
     * @param string $column
     * @param string $mainTable
     * @param string $aggregationTable
     * @return Mage_Sales_Model_Resource_Helper_Abstract
     */
    public function updateReportRatingPos($type, $column, $mainTable, $aggregationTable)
    {
        $adapter         = $this->_getWriteAdapter();
        $periodSubSelect = $adapter->select();
        $ratingSubSelect = $adapter->select();
        $ratingSelect    = $adapter->select();

        switch ($type) {
            case 'year':
                $periodCol = $adapter->getDateFormatSql('t.period', '%Y-01-01');
                break;
            case 'month':
                $periodCol = $adapter->getDateFormatSql('t.period', '%Y-%m-01');
                break;
            default:
                $periodCol = 't.period';
                break;
        }

        $cols = array(
            'period'            => 't.period',
            'store_id'          => 't.store_id',
            'product_id'        => 't.product_id',
            'product_name'      => new Zend_Db_expr('MAX(t.product_name)'),
            'product_price'     => new Zend_Db_expr('MAX(t.product_price)'),
            'total_qty'         => new Zend_Db_expr('SUM(t.' . $column . ')')
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
        $cols[$column] = 't.total_qty';
        $orderByColumns      = array(
            't.store_id ' . Varien_Db_Select::SQL_ASC,
            't.period ' . Varien_Db_Select::SQL_ASC,
            'total_qty ' . Varien_Db_Select::SQL_DESC
        );
        $cols['rating_pos']  = $this->prepareColumn('RANK()', 't.store_id, t.period', $orderByColumns);
        $ratingSubSelect->from(new Zend_Db_Expr(sprintf('(%s)', $periodSubSelect)), $cols);

        $cols = $columns;
        $cols['period']      = $periodCol;  // important!
        $cols[$column] = 't.' . $column;
        $cols['rating_pos']  = 't.rating_pos';
        $ratingSelect->from($ratingSubSelect, $cols);
        $sql = $ratingSelect->insertFromSelect($aggregationTable, array_keys($cols));

        $adapter->query($sql);

        return $this;
    }
}
