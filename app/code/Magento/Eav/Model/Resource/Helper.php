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
namespace Magento\Eav\Model\Resource;

class Helper extends \Magento\Core\Model\Resource\Helper
{
    /**
     * Mysql column - Table DDL type pairs
     *
     * @var array
     */
    protected $_ddlColumnTypes = array(
        \Magento\DB\Ddl\Table::TYPE_BOOLEAN       => 'bool',
        \Magento\DB\Ddl\Table::TYPE_SMALLINT      => 'smallint',
        \Magento\DB\Ddl\Table::TYPE_INTEGER       => 'int',
        \Magento\DB\Ddl\Table::TYPE_BIGINT        => 'bigint',
        \Magento\DB\Ddl\Table::TYPE_FLOAT         => 'float',
        \Magento\DB\Ddl\Table::TYPE_DECIMAL       => 'decimal',
        \Magento\DB\Ddl\Table::TYPE_NUMERIC       => 'decimal',
        \Magento\DB\Ddl\Table::TYPE_DATE          => 'date',
        \Magento\DB\Ddl\Table::TYPE_TIMESTAMP     => 'timestamp',
        \Magento\DB\Ddl\Table::TYPE_DATETIME      => 'datetime',
        \Magento\DB\Ddl\Table::TYPE_TEXT          => 'text',
        \Magento\DB\Ddl\Table::TYPE_BLOB          => 'blob',
        \Magento\DB\Ddl\Table::TYPE_VARBINARY     => 'blob'
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
