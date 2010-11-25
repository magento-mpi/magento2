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
 * Enterprise SalesArchive Mysql resource helper model
 *
 * @category    Enterprise
 * @package     Enterprise_SalesArchive
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_SalesArchive_Model_Resource_Helper_Mysql4 extends Mage_Core_Model_Resource_Helper_Mysql4
{
    /**
     * Change columns position
     *
     * @param string $table
     * @param string $column
     * @param boolean $after
     * @param boolean $first
     * @return Enterprise_SalesArchive_Model_Resource_Helper_Mysql4
     */
    public function changeColumnPosition($table, $column, $after = false, $first = false)
    {
        if ($after && $first) {
            if (is_string($after)) {
                $first = false;
            } else {
                $after = false;
            }
        } elseif (!$after && !$first) {
            // If no new position specified
            return $this;
        }

        if (!$this->_getWriteAdapter()->isTableExists($table)) {
            Mage::throwException(Mage::helper('enterprise_salesarchive')->__('Table not found'));
        }

        $columns = array();
        $description = $this->_getWriteAdapter()->describeTable($table);
        foreach ($description as $column) {
            $columns[$column['COLUMN_NAME']] = $column['DATA_TYPE'];
        }

        if (!isset($columns[$column])) {
            Mage::throwException(Mage::helper('enterprise_salesarchive')->__('Column not found'));
        } elseif ($after && !isset($columns[$after])) {
            Mage::throwException(Mage::helper('enterprise_salesarchive')->__('Positioning column not found'));
        }

        if ($after) {
            $sql = sprintf(
                'ALTER TABLE %s MODIFY COLUMN %s %s AFTER %s',
                $this->getConnection()->quoteIdentifier($table),
                $this->getConnection()->quoteIdentifier($column),
                $columns[$column],
                $this->getConnection()->quoteIdentifier($after)
            );
        } else {
            $sql = sprintf(
                'ALTER TABLE %s MODIFY COLUMN %s %s FIRST',
                $this->getConnection()->quoteIdentifier($table),
                $this->getConnection()->quoteIdentifier($column),
                $columns[$column]
            );
        }

        $this->getConnection()->query($sql);
        return $this;
    }
}
