<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright   {copyright}
 * @license     {license_link}
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
        Magento_DB_Ddl_Table::TYPE_BOOLEAN       => 'bit',
        Magento_DB_Ddl_Table::TYPE_SMALLINT      => 'smallint',
        Magento_DB_Ddl_Table::TYPE_INTEGER       => 'int',
        Magento_DB_Ddl_Table::TYPE_BIGINT        => 'bigint',
        Magento_DB_Ddl_Table::TYPE_FLOAT         => 'float',
        Magento_DB_Ddl_Table::TYPE_DECIMAL       => 'decimal',
        Magento_DB_Ddl_Table::TYPE_NUMERIC       => 'decimal',
        Magento_DB_Ddl_Table::TYPE_DATE          => 'datetime',
        Magento_DB_Ddl_Table::TYPE_TIMESTAMP     => 'datetime',
        Magento_DB_Ddl_Table::TYPE_DATETIME      => 'datetime',
        Magento_DB_Ddl_Table::TYPE_TEXT          => 'text',
        Magento_DB_Ddl_Table::TYPE_BLOB          => 'text',
        Magento_DB_Ddl_Table::TYPE_VARBINARY     => 'varbinary'
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

    /**
     * Retrieve 'cast to int' expression
     *
     * @param string|Zend_Db_Expr $expression
     * @return Zend_Db_Expr
     */
    public function getCastToIntExpression($expression)
    {
        return $this->castField($expression, 'INT');
    }
}
