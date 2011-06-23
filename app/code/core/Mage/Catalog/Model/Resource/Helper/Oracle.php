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
 * Eav Mssql resource helper model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Helper_Oracle extends Mage_Eav_Model_Resource_Helper_Oracle
{

    /**
     * Returns columns for select
     *
     * @param string $tableAlias
     * @param string $eavType
     * @return array
     */
    public function attributeSelectFields($tableAlias, $eavType)
    {
        return array(
            'value_id',
            'entity_type_id',
            'attribute_id',
            'store_id',
            'entity_id',
            'value' => $this->prepareEavAttributeValue($tableAlias . '.value', $eavType)
        );
    }

    /**
     * Compare intended column definition with actual column as described from Database
     * Returns whether descriptions are equal.
     *
     * @param array $intendedDdl
     * @param array $describedCol
     * @return bool
     */
    public function compareIndexColumnProperties($intendedDdl, $describedCol)
    {
        $intendedDdl = array_change_key_case($intendedDdl, CASE_LOWER);

        // Format described column to column information
        $adapter = $this->_getReadAdapter();
        $describedInfo = $adapter->getColumnCreateByDescribe($describedCol);

        // Compare fields
        // a) Types
        $typeIntended = strtolower($this->_ddlColumnTypes[$intendedDdl['type']]);
        $typeDescribed = strtolower($this->_ddlColumnTypes[$describedInfo['type']]);
        if ($typeIntended != $typeDescribed) {
            // Describe table mistakes and shows that 'smallint' column is really an integer
            if (($typeIntended == 'smallint')
                && ($typeDescribed == Varien_Db_Ddl_Table::TYPE_INTEGER)
                ) {
                // Types matched, do nothing
            } else {
                // Types not matched
                return false;
            }
        }

        // b) Primary
        $intendedPrimary = isset($intendedDdl['primary']) ? (bool) $intendedDdl['primary'] : false;
        $describedPrimary = isset($describedInfo['options']['primary'])
            ? (bool) $describedInfo['options']['primary'] : false;
        if ($intendedPrimary != $describedPrimary) {
            return false;
        }

        // c) Length
        if (($typeDescribed == Varien_Db_Ddl_Table::TYPE_NUMERIC)
            || ($typeDescribed == Varien_Db_Ddl_Table::TYPE_TEXT)
            || ($typeDescribed == Varien_Db_Ddl_Table::TYPE_BLOB)
            || ($typeDescribed == Varien_Db_Ddl_Table::TYPE_VARBINARY)) {

            $lengthIntended = (string) $intendedDdl['length'];
            $lengthDescribed = (string) $describedInfo['length'];
            if ($typeDescribed == Varien_Db_Ddl_Table::TYPE_NUMERIC) {
                if (isset($describedInfo['options']['scale']) && isset($describedInfo['options']['precision'])) {
                    $lengthIntended = explode(',', $lengthIntended);
                    foreach ($lengthIntended as &$param) {
                        $param = (int) $param;
                    }
                    unset($param);
                    $lengthDescribed = array(
                        (int) $describedInfo['options']['precision'],
                        (int) $describedInfo['options']['scale']
                    );
                }
            }
            if ($lengthDescribed !== $lengthIntended) {
                    return false;
            }
        }

        // d) Nullable
        $nullableIntended = (bool) $intendedDdl['nullable'];
        if (isset($describedInfo['options']['nullable'])) {
            $nullableDescribed = (bool) $describedInfo['options']['nullable'];
        } else {
            $nullableDescribed = true;
        }
        if ($nullableIntended != $nullableDescribed) {
            return false;
        }

        // e) Default value
        if (!$intendedPrimary) {
            $defaultIntended = $intendedDdl['default'];
            if ($defaultIntended === null) {
                if (!$nullableIntended) {
                    $defaultIntended = false;
                }
            }
            if (($defaultIntended !== null) && ($defaultIntended !== false)) {
                $defaultIntended = (string) $defaultIntended;
            }
            if ($defaultIntended === '') {
                $defaultIntended = null;
            }

            if (isset($describedInfo['options']['default'])) {
                $defaultDescribed = $describedInfo['options']['default'];
            } else {
                if (!$nullableDescribed) {
                    $defaultDescribed = false;
                } else {
                    $defaultDescribed = null;
                }
            }
            if (($defaultDescribed !== null) && ($defaultDescribed !== false)) {
                $defaultDescribed = (string) $defaultDescribed;
            }
            if ($defaultDescribed === '') {
                $defaultDescribed = null;
            }

            if ($defaultIntended !== $defaultDescribed) {
                return false;
            }
        }
        return true;
    }

    /**
     * Getting condition isNull(f1,f2) IS NOT Null
     *
     * @param string $field1
     * @param string $field2
     * @return string
     */
    public function getIsNullNotNullCondition($field1, $field2)
    {
        return sprintf('%s IS NOT NULL', $this->_getReadAdapter()->getIfNullSql($field1, $field2));
    }
}
