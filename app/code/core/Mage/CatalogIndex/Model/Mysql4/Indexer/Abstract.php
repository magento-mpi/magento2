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
 * @category   Mage
 * @package    Mage_CatalogIndex
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Indexer resource model abstraction
 *
 * @author Sasha Boyko <alex.boyko@varien.com>
 */
class Mage_CatalogIndex_Model_Mysql4_Indexer_Abstract extends Mage_Core_Model_Mysql4_Abstract
{
    public function saveIndex($data)
    {
        return $this->saveIndices(array($data));
    }

    public function saveIndices(array $data)
    {
        $this->_executeReplace($data);
    }

    protected function _executeReplace($data)
    {
        $this->beginTransaction();
        try {
            foreach ($data as $row) {
                $conditions = implode (' AND ', $this->_getReplaceCondition($row));

                $this->_getWriteAdapter()->delete($this->getMainTable(), $conditions);
                $this->_getWriteAdapter()->insert($this->getMainTable(), $row);
            }
            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }

        return $this;
    }

    protected function _getReplaceCondition($data)
    {
        $conditions = array();

        if (isset($data[$this->_entityIdFieldName]))
            $conditions[] = $this->_getWriteAdapter()->quoteInto("{$this->_entityIdFieldName} = ?", $data[$this->_entityIdFieldName]);

        if (isset($data[$this->_storeIdFieldName]))
            $conditions[] = $this->_getWriteAdapter()->quoteInto("{$this->_storeIdFieldName} = ?", $data[$this->_storeIdFieldName]);

        if (isset($data[$this->_attributeIdFieldName]))
            $conditions[] = $this->_getWriteAdapter()->quoteInto("{$this->_attributeIdFieldName} = ?", $data[$this->_attributeIdFieldName]);

        return $conditions;
    }

    protected function _construct()
    {
        return parent::_construct();
    }

    public function loadAttributeCodesByCondition($conditions)
    {
        $table = $this->getTable('eav/attribute');
        $select = $this->_getReadAdapter()->select();
        $select->from($table, 'attribute_code');
        $select->distinct(true);

        foreach ($conditions as $k=>$condition) {
            if (is_array($condition)) {
                if ($k == 'or') {
                    $function = 'where';
                    foreach ($condition as $field=>$value) {
                        $select->$function("{$field} = ?", $value);

                        $function = 'orWhere';
                    }
                } else {
                    $select->where("{$k} in (?)", $condition);
                }
            } else {
                $select->where("{$k} = ?", $condition);
            }
        }

        return $this->_getReadAdapter()->fetchCol($select);
    }
}