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
        if (!empty($data['visitor_id'])) {
            $matchFields[] = 'visitor_id';
        } else {
            $matchFields[] = 'customer_id';
        }
        $selectPart = '';
        $matchPart  = '';
        $insertPart = '';
        $updatePart = '';
        $deletePart = '';
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

        if (!empty($data['visitor_id']) && !empty($data['customer_id'])) {
            $deletePart = ' DELETE WHERE EXISTS ( SELECT 1 FROM ' . $mainTable . ' t3 WHERE '
                . 't3.store_id = t1.store_id AND t3.customer_id = t1.customer_id AND t3.product_id = t1.product_id AND t3.visitor_id != t1.visitor_id )';
        }

        $selectPart = rtrim($selectPart,', ');
        $updatePart = rtrim($updatePart,', ');
        $insertPart = rtrim($insertPart,', ');

        $sql = 'MERGE INTO ' . $mainTable . ' t1 USING ('
            . ' SELECT ' . $selectPart . ' FROM dual ) t2 ON ( 1 = 1 ' . $matchPart . ' )'
            . 'WHEN MATCHED THEN '
            . ' UPDATE SET ' . $updatePart . $deletePart
            . ' WHEN NOT MATCHED THEN INSERT (' . $columnsPart . ')'
            . ' VALUES ( ' . $insertPart . ')';

        $stmt = $this->_getWriteAdapter()->query($sql, $data);
        $result = $stmt->rowCount();
        
        return $result;
    }

}
