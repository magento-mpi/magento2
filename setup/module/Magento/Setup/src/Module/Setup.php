<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Module;

class Setup extends \Magento\Framework\Module\Setup\Base
{
    /**
     * Retrieve 32bit UNIQUE HASH for a Table index
     *
     * @param string $tableName
     * @param array|string $fields
     * @param string $indexType
     * @return string
     */
    public function getIdxName($tableName, $fields, $indexType = '')
    {
        return $this->getConnection()->getIndexName($tableName, $fields, $indexType);
    }

    /**
     * Retrieve 32bit UNIQUE HASH for a Table foreign key
     *
     * @param string $priTableName  the target table name
     * @param string $priColumnName the target table column name
     * @param string $refTableName  the reference table name
     * @param string $refColumnName the reference table column name
     * @return string
     */
    public function getFkName($priTableName, $priColumnName, $refTableName, $refColumnName)
    {
        return $this->getConnection()->getForeignKeyName($priTableName, $priColumnName, $refTableName, $refColumnName);
    }

    /**
     * Add configuration data to core_config_data table
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    public function addConfigData($key, $value)
    {
        $this->getConnection()->insertOnDuplicate(
            $this->getTable('core_config_data'),
            array(
                'path'  => $key,
                'value' => $value
            )
        );
    }
}
