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
 * @package     Mage_Eav
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Eav Mssql resource helper model
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Resource_Helper_Mssql extends Mage_Core_Model_Resource_Helper_Mssql
{
    /**
     * Mssql column - Table DDL type pairs
     *
     * @var array
     */
    protected $_ddlColumnTypes      = array(
        Varien_Db_Ddl_Table::TYPE_BOOLEAN       => 'bit',
        Varien_Db_Ddl_Table::TYPE_SMALLINT      => 'smallint',
        Varien_Db_Ddl_Table::TYPE_INTEGER       => 'int',
        Varien_Db_Ddl_Table::TYPE_BIGINT        => 'bigint',
        Varien_Db_Ddl_Table::TYPE_FLOAT         => 'float',
        Varien_Db_Ddl_Table::TYPE_DECIMAL       => 'decimal',
        Varien_Db_Ddl_Table::TYPE_NUMERIC       => 'decimal',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP     => 'datetime',
        Varien_Db_Ddl_Table::TYPE_DATE          => 'datetime',
        Varien_Db_Ddl_Table::TYPE_TEXT          => 'text',
        Varien_Db_Ddl_Table::TYPE_BLOB          => 'text',
    );
    
    /**
     * Returns columns for select
     *
     * @param string $tableAlias
     * @param string $eavType
     * @return string|array
     */
    public function attributeSelectFields($tableAlias, $eavType)
    {
        return array(
            'value_id',
            'entity_type_id',
            'attribute_id',
            'entity_id',
            'value' => $this->prepareEavAttributeValue($tableAlias . '.value', $eavType)
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
        $columnType = ($columnType == 'varchar') ? 'text' : $columnType;
        return array_search($columnType, $this->_ddlColumnTypes);
    }

    /**
     * Prepares value fields for unions depend on type
     *
     * @param string $value
     * @param string $eavType
     * @return Zend_Db_Expr
     */
    public function prepareEavAttributeValue($value, $eavType)
    {
        switch ($eavType) {
            case 'text' : $value = new Zend_Db_Expr($value);
                break;
            case 'datetime' : $value = $this->convertField($value);
                break;
            default : $value = $this->castField($value);
        }
        return $value;

    }

    /**
     * Returns expression for converting data into a new data type.
     *
     * @param string $field Field name
     * @param string $type OPTIONAL Field type
     * @param int $style style format
     * @return Zend_Db_Expr
     */
    public function convertField($field, $type = 'VARCHAR(19)', $style = 120)
    {
        $expression = sprintf('CONVERT(%s, %s, %d)', $type, $this->_getReadAdapter()->quoteIdentifier($field), $style);
        return new Zend_Db_Expr($expression);
    }

    /**
     * Groups selects to separate unions depend on type
     *
     * @param array $selects
     * @return array
     */
    public function getLoadAttributesSelectGroups($selects)
    {
        $mainGroup  = array();
        $textGroup = array();
        foreach ($selects as $eavType => $selectGroup) {
            if ($eavType == 'text') {
                $textGroup = array_merge($textGroup, $selectGroup);
            } else {
                $mainGroup = array_merge($mainGroup, $selectGroup);
            }
        }
        return array($mainGroup,$textGroup);
    }
}
