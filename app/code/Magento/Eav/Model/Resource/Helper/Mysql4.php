<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Eav Mysql resource helper model
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Eav_Model_Resource_Helper_Mysql4 extends Magento_Core_Model_Resource_Helper_Mysql4
{
    /**
     * Mysql column - Table DDL type pairs
     *
     * @var array
     */
    protected $_ddlColumnTypes = array(
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
            default:
                break;
        }

        return array_search($columnType, $this->_ddlColumnTypes);
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
        foreach ($selects as $selectGroup) {
            $mainGroup = array_merge($mainGroup, $selectGroup);
        }
        return array($mainGroup);
    }
}
