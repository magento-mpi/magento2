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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Resource helper class for Oracle Varien DB Adapter
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Resource_Helper_Oracle extends Mage_Core_Model_Resource_Helper_Abstract
{

 protected $_delimiters = array(
     '>', '<', '=<', '<=', '=', '!=', ' IN ', ' NOT IN ', ' BETWEEN ', ' NOT BETWEEN ',
     ' BEGINS WITH ', ' CONTAINS ', ' NOT CONTAINS ', ' IS NULL ', ' IS NOT NULL ', ' LIKE ', ' NOT LIKE ');

    /**
     * Returns analytic expression for database column
     *
     * @param string $columnType
     * @return string
     */
    public function getAnalyticColumn($column, $group)
    {
        return new Zend_Db_Expr($column . ' OVER ( PARTITION BY ' . implode(', ', $group) . ')');
    }

//    public function isAggregatedFunction($value)
//    {
//        return preg_match('/(^|\s)(SUM|MIN|MAX|AVG|COUNT)\s*\(/i', )
//    }


    public function preapareColumnsList(Varien_Db_Select $select, $groupByCond = null)
    {
        if (!count($select->getPart(Zend_Db_Select::COLUMNS)) || !count($select->getPart(Zend_Db_Select::FROM))) {
            return null;
        }
        $tables = $select->getPart(Zend_Db_Select::FROM);
        $preaparedColumns = array();
        $columns = $select->getPart(Zend_Db_Select::COLUMNS);
        foreach ($select->getPart(Zend_Db_Select::COLUMNS) as $columnEntry) {
            list($correlationName, $column, $alias) = $columnEntry;
            if($column instanceof Zend_Db_Expr) {
                if (!is_null($alias)) {
                    if(!is_null($groupByCond)) {
                        $partitionByCond = ($groupByCond == '') ? '' : "PARTITION BY " . $groupByCond;
                        if (preg_match('/(^|[^a-zA-Z_])(SUM|MIN|MAX|AVG|COUNT)\s*\(/i', $column)) {
                            $column = $column . " OVER ( {$partitionByCond})";
                        }
                    }
                    $preaparedColumns[strtoupper($alias)] = array(null, $column, $alias);
                } else {
                    throw new Zend_Db_Exception("Cann't preapare expresion without alias");
                }
            } else {
                if ($column == Zend_Db_Select::SQL_WILDCARD) {
                    foreach(array_keys($this->_getReadAdapter()->describeTable($tables[$correlationName]['tableName'])) as $col) {
                        $preaparedColumns[strtoupper($col)] = array($correlationName, $col, null);
                    }
                } else {
                    $preaparedColumns[strtoupper(!is_null($alias) ? $alias : $column)] = array(
                        $correlationName, $column, $alias);
                }
            }
        }
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->setPart(Zend_Db_Select::COLUMNS, array_values($preaparedColumns));
        return $select;
    }    
    /*
     * Render Sql using Windows(Analytic) functions
     *
     * @param Varien_Db_Select $select
     * @return select
     */
    public function getWindSql(Varien_Db_Select $select)
    {
        $i = 0;
        $winSelect = clone $select;

        echo "<pre>";
        $orderCondition = null;
        $groupByCondition = null;
        $havingByCondition = null;

        // redo order
        if ($winSelect->getPart(Zend_Db_Select::ORDER)) {
            $order = array();
            foreach ($winSelect->getPart(Zend_Db_Select::ORDER) as $term) {
                if (is_array($term)) {
                    if (!is_numeric($term[0])) {
                        $order[] = $this->quoteIdentifier($term[0], true) . ' ' . $term[1];
                    } else {
                        throw new Zend_Db_Exception("Cann't use field number as order field");
                    }
                } else {
                    if (!is_numeric($term)) {
                        $order[] = $this->quoteIdentifier($term, true);
                    } else {
                        throw new Zend_Db_Exception("Cann't use field number as order field");
                    }
                }
            $orderCondition = implode(', ', $order);

            }
        }

        // redo Group
        $group = array();
       // var_dump($winSelect->getPart(Zend_Db_Select::GROUP));
        if ($winSelect->getPart(Zend_Db_Select::GROUP)) {
            $group = array();
            foreach ($winSelect->getPart(Zend_Db_Select::GROUP) as $term) {
                $group[] = $this->quoteIdentifier($term, true);
            }
            $groupByCondition = implode(', ', $group);
        }
        $this->preapareColumnsList($winSelect, is_null($groupByCondition) ? '' : $groupByCondition);
        // redo Having 
//        $having = array();
//        if ($winSelect->getPart(Zend_Db_Select::HAVING)) {
//            foreach ($winSelect->getPart(Zend_Db_Select::HAVING) as $term) {
//                foreach ($this->_delimiters as $delimiter) {
//                    $tmpCond = strtoupper($term);
//                    $result  = explode($delimiter, $tmpCond);
//                    if (is_array($result) && count($result) > 1) {
//                        $term   = str_replace(strtolower($result[0]), '%s ', $term);
//                        break;
//                    }
//                }
//                $having["varien_super_param_{$i}"] = $term;
//            }
//        }
            $winSelect->reset(Zend_Db_Select::GROUP);
            $winSelect->columns(array("varien_group_rank" =>
                new Zend_Db_Expr(sprintf("RANK() OVER (%s ORDER BY rownum)",
                    is_null($groupByCondition) ? '' : 'PARTITION BY ' . $groupByCondition ))));

        // redo Order 
        if ($orderCondition) {
            $winSelect->reset(Zend_Db_Select::ORDER);
            $winSelect->columns(array("varien_order_condition" =>
                new Zend_Db_Expr(sprintf("RANK() OVER (ORDER BY %s)", $orderCondition)))
            );
        }
        //ECHO var_dump($winSelect->assemble());
        //die();
        ECHO sprintf("SELECT varien_wind_table.* FROM (%s) varien_wind_table %s",
            $winSelect->assemble(),
            !empty($groupByCondition) ? "" : "WHERE varien_wind_table.varien_group_rank = 1"
        );
        die();
    }    
}
