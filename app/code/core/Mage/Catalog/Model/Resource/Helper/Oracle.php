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
     * Oracle column - Table DDL type pairs
     *
     * @var array
     */
    protected $_ddlColumnTypes      = array(
        Varien_Db_Ddl_Table::TYPE_BOOLEAN       => 'SMALLINT',
        Varien_Db_Ddl_Table::TYPE_SMALLINT      => 'SMALLINT',
        Varien_Db_Ddl_Table::TYPE_INTEGER       => 'INTEGER',
        Varien_Db_Ddl_Table::TYPE_BIGINT        => 'NUMBER',
        Varien_Db_Ddl_Table::TYPE_FLOAT         => 'FLOAT',
        Varien_Db_Ddl_Table::TYPE_DECIMAL       => 'NUMBER',
        Varien_Db_Ddl_Table::TYPE_NUMERIC       => 'NUMBER',
        Varien_Db_Ddl_Table::TYPE_DATE          => 'DATE',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP     => 'TIMESTAMP',
        Varien_Db_Ddl_Table::TYPE_TEXT          => 'VARCHAR2',
        Varien_Db_Ddl_Table::TYPE_BLOB          => 'CLOB',
    );

    /**
     * Returns columns for select
     *
     * @param string $tableAlias
     * @return array
     */
    public function attributeSelectFields($tableAlias)
    {
        return array(
            'value_id',
            'entity_type_id',
            'attribute_id',
            'store_id',
            'entity_id',
            'value' => $this->castField($tableAlias . '.value')
        );
    }

    /**
     * Returns DDL type by column type in database
     *
     * @param string $columnType
     * @return string
     */
    public function getDdlTypeByColumnType($columnType)
    {
        if($result = array_search($columnType, $this->_ddlColumnTypes)) {
            return $result;
        } else {
            return Varien_Db_Ddl_Table::TYPE_TIMESTAMP;
        }
    }

    /**
     * Compare Flat style with Describe style columns
     * If column a different - return false
     *
     * @param array $column
     * @param array $describe
     * @return bool
     */
    public function compareIndexColumnProperties($column, $describe)
    {
        $type       = $column['type'];
        $length     = null;
        $precision  = null;
        $scale      = null;

        $matches = array();

        //match data type
        if ($describe['DATA_TYPE'] == $this->_ddlColumnTypes[$type]) {
            $type = $describe['DATA_TYPE'];
        } elseif ($type == Varien_Db_Ddl_Table::TYPE_SMALLINT
                && $describe['DATA_TYPE'] == $this->_ddlColumnTypes[Varien_Db_Ddl_Table::TYPE_INTEGER])  {
            $type = $describe['DATA_TYPE'];
        } else {
            return false;
        }
        //match length
        if ($length == null) {
            $length = $describe['LENGTH'];
        } elseif ($length != $describe['LENGTH']) {
            return false;
        }
        //match default value
        if($column['default'] != false && trim($describe['DEFAULT']) != $this->_getReadAdapter()->quote($column['default'])) {
            return false;
        }

        return ((bool)$describe['NULLABLE'] == (bool)$column['nullable'])
          //  && ((bool)$describe['UNSIGNED'] == (bool)$column['unsigned'])
            && ($describe['SCALE'] == $scale)
            && ($describe['PRECISION'] == $precision);
    }
}
