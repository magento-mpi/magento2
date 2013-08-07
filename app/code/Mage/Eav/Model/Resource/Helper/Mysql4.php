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
 * Eav Mysql resource helper model
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Resource_Helper_Mysql4 extends Mage_Core_Model_Resource_Helper_Mysql4
{
    /**
     * Mysql column - Table DDL type pairs
     *
     * @var array
     */
    protected $_ddlColumnTypes      = array(
        Magento_DB_Ddl_Table::TYPE_BOOLEAN       => 'bool',
        Magento_DB_Ddl_Table::TYPE_SMALLINT      => 'smallint',
        Magento_DB_Ddl_Table::TYPE_INTEGER       => 'int',
        Magento_DB_Ddl_Table::TYPE_BIGINT        => 'bigint',
        Magento_DB_Ddl_Table::TYPE_FLOAT         => 'float',
        Magento_DB_Ddl_Table::TYPE_DECIMAL       => 'decimal',
        Magento_DB_Ddl_Table::TYPE_NUMERIC       => 'decimal',
        Magento_DB_Ddl_Table::TYPE_DATE          => 'date',
        Magento_DB_Ddl_Table::TYPE_TIMESTAMP     => 'timestamp',
        Magento_DB_Ddl_Table::TYPE_DATETIME      => 'datetime',
        Magento_DB_Ddl_Table::TYPE_TEXT          => 'text',
        Magento_DB_Ddl_Table::TYPE_BLOB          => 'blob',
        Magento_DB_Ddl_Table::TYPE_VARBINARY     => 'blob'
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
        return '*';
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
            case 'char':
            case 'varchar':
                $columnType = 'text';
                break;
            case 'tinyint':
                $columnType = 'smallint';
                break;
        }

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
        return $value;
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
        return new Zend_Db_Expr("CAST($expression AS SIGNED)");
    }
}
