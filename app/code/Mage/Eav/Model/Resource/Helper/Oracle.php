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
 * Eav Oracle resource helper model
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Resource_Helper_Oracle extends Mage_Core_Model_Resource_Helper_Oracle
{
    /**
     * Oracle column - Table DDL type pairs
     *
     * @var array
     */
    protected $_ddlColumnTypes      = array(
        Magento_DB_Ddl_Table::TYPE_BOOLEAN       => 'SMALLINT',
        Magento_DB_Ddl_Table::TYPE_SMALLINT      => 'SMALLINT',
        Magento_DB_Ddl_Table::TYPE_INTEGER       => 'INTEGER',
        Magento_DB_Ddl_Table::TYPE_BIGINT        => 'NUMBER',
        Magento_DB_Ddl_Table::TYPE_FLOAT         => 'FLOAT',
        Magento_DB_Ddl_Table::TYPE_DECIMAL       => 'NUMBER',
        Magento_DB_Ddl_Table::TYPE_NUMERIC       => 'NUMBER',
        Magento_DB_Ddl_Table::TYPE_DATE          => 'DATE',
        Magento_DB_Ddl_Table::TYPE_TIMESTAMP     => 'TIMESTAMP',
        Magento_DB_Ddl_Table::TYPE_DATETIME      => 'TIMESTAMP',
        Magento_DB_Ddl_Table::TYPE_TEXT          => 'VARCHAR2',
        Magento_DB_Ddl_Table::TYPE_BLOB          => 'CLOB',
        Magento_DB_Ddl_Table::TYPE_VARBINARY     => 'BLOB',
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
        switch ($columnType) {
            case 'int':
                $columnType = 'INTEGER';
                break;
            case 'varchar':
                $columnType = 'VARCHAR2';
                break;
        }

        if ($result = array_search($columnType, $this->_ddlColumnTypes)) {
            return $result;
        } else {
            return Magento_DB_Ddl_Table::TYPE_TIMESTAMP;
        }
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
        return $this->castField($value);
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
        foreach ($selects as $eavType => $selectGroup) {
            $mainGroup = array_merge($mainGroup, $selectGroup);
        }
        return array($mainGroup);
    }

    /**
     * Retrieve 'cast to int' expression
     *
     * @param string|Zend_Db_Expr $expression
     * @return Zend_Db_Expr
     */
    public function getCastToIntExpression($expression)
    {
        return new Zend_Db_Expr("CAST($expression AS INT)");
    }
}
