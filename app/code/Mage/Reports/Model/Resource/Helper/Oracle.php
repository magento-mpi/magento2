<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reports Oracle resource helper model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Resource_Helper_Oracle extends Mage_Core_Model_Resource_Helper_Oracle
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
            $pseudoUnique['visitor_id'] = 't1.visitor_id = t2.visitor_id';
        }
        if (!empty($data['customer_id'])) {
            $pseudoUnique['customer_id'] = 't1.customer_id = t2.customer_id';
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
                if (count($pseudoUnique) == 1 && isset($pseudoUnique[$column])) {
                    continue;
                }
                $updatePart .= sprintf('t1.%s = t2.%s, ', $column, $column);
            } else {
                $matchPart .= sprintf('AND t1.%s = t2.%s ', $column, $column);
            }
        }

        $selectPart = rtrim($selectPart, ', ');
        $updatePart = rtrim($updatePart, ', ');
        $insertPart = rtrim($insertPart, ', ');

        $sql = 'MERGE INTO ' . $mainTable . ' t1 USING ('
            . ' SELECT ' . $selectPart . ' FROM dual ) t2 ON ( ' . $matchPart . ' )'
            . ' WHEN MATCHED THEN '
            . ' UPDATE SET ' . $updatePart
            . ' WHEN NOT MATCHED THEN INSERT (' . $columnsPart . ')'
            . ' VALUES ( ' . $insertPart . ')';

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
     * @return Mage_Reports_Model_Resource_Helper_Oracle
     */
    public function updateReportRatingPos($type, $column, $mainTable, $aggregationTable) {
        $adapter         = $this->_getWriteAdapter();
        $periodSubSelect = $adapter->select();
        $ratingSubSelect = $adapter->select();
        $ratingSelect    = $adapter->select();


        switch ($type) {
            case 'year':
                $postfix =$adapter->quote('-01-01');
                $periodCol = $adapter->getDateFormatSql('tt.period', '%Y');
                break;
            case 'month':
                $postfix = $adapter->quote('-01');
                $periodCol = $adapter->getDateFormatSql('tt.period', '%Y-%m');
                break;
            default:
                $postfix = false;
                $periodCol = 'tt.period';
                break;
        }

        // SubLevel 2
        $periodSubSelect->from(array('tt' => $mainTable), array(
                'period'            => $periodCol,
                'store_id'          => 'tt.store_id',
                'product_id'        => 'tt.product_id',
                'product_name'      => new Zend_Db_Expr('MAX(tt.product_name)'),
                'product_price'     => new Zend_Db_Expr('MAX(tt.product_price)'),
                'total_qty'         => new Zend_Db_Expr('SUM(tt.' . $column . ')')
        ))->group(array('tt.store_id', $periodCol, 'tt.product_id'));

        // SubLevel 1
        $orderByColumns      = array(
            'tr.store_id ' . Magento_DB_Select::SQL_ASC,
            'tr.period ' . Magento_DB_Select::SQL_ASC,
            'tr.total_qty ' . Magento_DB_Select::SQL_DESC
        );
        $ratingSubSelect->from(array('tr' => new Zend_Db_Expr(sprintf('(%s)', $periodSubSelect))), array(
            'period'        => 'tr.period',
            'store_id'      => 'tr.store_id',
            'product_id'    => 'tr.product_id',
            'product_name'  => 'tr.product_name',
            'product_price' => 'tr.product_price',
            $column         => 'tr.total_qty',
            'rating_pos'    => $this->prepareColumn('RANK()', 'tr.store_id, tr.period', $orderByColumns)
        ));
        // Top level
        $cols = array(
            'period'        => ($postfix == false) ? 't.period' : $adapter->getConcatSql(array('t.period', $postfix)),
            'store_id'      => 't.store_id',
            'product_id'    => 't.product_id',
            'product_name'  => 't.product_name',
            'product_price' => 't.product_price',
            $column         => 't.' . $column,
            'rating_pos'    => 't.rating_pos'
        );
        $ratingSelect->from(array('t' => new Zend_Db_Expr(sprintf('(%s)', $ratingSubSelect))), $cols);
        $sql = $ratingSelect->insertFromSelect($aggregationTable, array_keys($cols));

        $adapter->query($sql);

        return $this;
    }
}
