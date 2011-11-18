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

}
