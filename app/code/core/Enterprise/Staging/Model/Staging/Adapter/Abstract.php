<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

abstract class Enterprise_Staging_Model_Staging_Adapter_Abstract extends Varien_Object
{
    /**
     * Database read/write connections
     *
     * @var resource
     */
    protected $_connections = array();

    /**
     * Staging instance id
     *
     * @var mixed
     */
    protected $_staging;

    /**
     * Staging type config data
     *
     * @var mixed
     */
    protected $_config;

    /**
     * Table list, exclude from copying
     *
     * @var mixed
     */
    protected $_excludeList = array(
        'core_store',
        'core_website',
        'eav_attribute',
        'eav_attribute_set',
        'eav_entity_type',
        'cms_page',
        'cms_block',
        'catalog_product_bundle_option_value',
        'downloadable_sample_title'
    );

    /**
     * Flat type table list
     *
     * @var mixed
     */
    protected $_flatTables = array(
        'category_flat' => true,
        'product_flat'  => true
    );

    /**
     * table list
     *
     * @var mixed
     */
    protected $_tables;

    /**
     * EAV type Table models
     *
     * @var mixed
     */
    protected $_eavModels = array(
        'catalog/product'           => 'catalog',
        'catalog/category'          => 'catalog',
        'sales/order'               => 'sales',
        'sales/order_entity'        => 'sales',
        'customer/entity'           => 'customer',
        'customer/address_entity'   => 'customer',
    );

    /**
     * EAV table entities
     *
     * @var modex
     */
    protected $_eavTableTypes = array('int', 'decimal', 'varchar', 'text', 'datetime');

    /**
     * event state code
     *
     * @var string
     */
    protected $_eventStateCode;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->_resource = Mage::getSingleton('core/resource');
        $this->_read     = $this->_resource->getConnection('staging_read');
        $this->_write    = $this->_resource->getConnection('staging_write');
    }

    /**
     * Set event state code attribute
     *
     * @param string $code
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    public function setEventStateCode($code)
    {
        $this->_eventStateCode = $code;

        return $this;
    }

    /**
     * Retrieve event state code
     *
     * @return string
     */
    public function getEventStateCode()
    {
        return $this->_eventStateCode;
    }

    /**
     * Create item method
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @return Enterprise_Staging_Model_Staging_Adapter_Abstract
     */
    public function create(Enterprise_Staging_Model_Staging $staging)
    {
        return $this;
    }

    /**
     * Update item method
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @return Enterprise_Staging_Model_Staging_Adapter_Abstract
     */
    public function update(Enterprise_Staging_Model_Staging $staging)
    {
        return $this;
    }

    /**
     * Make staging content merge
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @return Enterprise_Staging_Model_Staging_Adapter_Abstract
     */
    public function merge(Enterprise_Staging_Model_Staging $staging)
    {
        return $this;
    }

    /**
     * Make staging content rollback
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @return Enterprise_Staging_Model_Staging_Adapter_Abstract
     */
    public function rollback(Enterprise_Staging_Model_Staging $staging)
    {
        return $this;
    }

    /**
     * Staging content check
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @return Enterprise_Staging_Model_Staging_Adapter_Abstract
     */
    public function check(Enterprise_Staging_Model_Staging $staging)
    {
        return $this;
    }

    /**
     * Staging Content repair
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @return Enterprise_Staging_Model_Staging_Adapter_Abstract
     */
    public function repair(Enterprise_Staging_Model_Staging $staging)
    {
        return $this;
    }

    /**
     * Staging content copy
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @return Enterprise_Staging_Model_Staging_Adapter_Abstract
     */
    public function copy(Enterprise_Staging_Model_Staging $staging)
    {
        return $this;
    }

    /**
     * Create Staging content backup
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @return Enterprise_Staging_Model_Staging_Adapter_Abstract
     */
    public function backup(Enterprise_Staging_Model_Staging $staging)
    {
        return $this;
    }

    /**
     * Specify staging instance
     *
     * @param   mixed $staging
     * @return  Enterprise_Staging_Model_Staging_Adapter_Abstract
     */
    public function setStaging($staging)
    {
        $this->_staging = $staging;

        return $this;
    }

    /**
     * Retrieve staging object
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @return Enterprise_Staging_Model_Staging
     */
    public function getStaging()
    {
        if (is_object($this->_staging)) {
            return $this->_staging;
        }
        /* TODO try to set staging_id instead whole staging object */
        $_staging = Mage::registry('staging');
        if ( ($_staging && is_null($this->_staging)) || ($_staging->getId() == (int) $this->_staging)) {
            return $_staging;
        } else {
            if (is_int($this->_staging)) {
                $this->_staging = Mage::getModel('enterprise_staging/staging')->load($this->_staging);
            } else {
                $this->_staging = false;
            }
        }

        return $this->_staging;
    }

    /**
     * Specify item xml config
     *
     * @param   Varien_Simplexml_Config $config
     * @return  Enterprise_Staging_Model_Staging_Adapter_Abstract
     */
    public function setConfig($config)
    {
        $this->_config = $config;

        return $this;
    }

    /**
     * Retrieve item xml config
     *
     * @return Varien_Simplexml_Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Get connection by name or type
     *
     * @param   string $connectionName
     * @return  Zend_Db_Adapter_Abstract
     */
    public function getConnection($connectionName = 'enterprise_staging', $scope = 'read')
    {
        $connectionName = $connectionName . '_' .$scope;

        if (isset($this->_connections[$connectionName])) {
            return $this->_connections[$connectionName];
        }

        try {
            $this->_connections[$connectionName] = Mage::getSingleton('core/resource')->getConnection($connectionName);
        } catch (Exception $e) {
            throw new Enterprise_Staging_Exception($e);
        }

        return $this->_connections[$connectionName];
    }

    /**
     * Create table
     *
     * @param string $model
     * @param array $srcTableDescription
     * @param string $targetTable
     * @return Enterprise_Staging_Model_Staging_Adapter_Abstract
     */
    public function createTable($model, $srcTableDescription, $targetTable)
    {
        $connection = $this->getConnection();

        $srcTableDescription['table_name'] = $targetTable;

        $sql = $this->_getCreateSql($srcTableDescription, null);

        $connection->query($sql);

        return $this;
    }

    /**
     * Check table for existing and create it if not
     *
     * @param string    $model
     * @param array     $srcTableDesc
     * @param string    $targetTable
     * @param string    $prefix
     * @return Enterprise_Staging_Model_Staging_Adapter_Abstract
     */
    protected function _checkCreateTable($model, $srcTableDesc, $targetTable, $prefix)
    {
        $targetTableDesc = $this->getTableProperties($targetTable);
        if (!$targetTableDesc) {
            $srcTableDesc['table_name'] = $targetTable;
            if (!empty($srcTableDesc['constraints'])) {
                foreach($srcTableDesc['constraints'] AS $constraint => $data) {
                    $srcTableDesc['constraints'][$constraint]['fk_name'] = $prefix . $data['fk_name'];
                }
            }
            $sql = $this->_getCreateSql($srcTableDesc);

            $this->getConnection()->query($sql);
        }
        return $this;
    }

    /**
     * get sql to create new tables
     *
     * @param string $model
     * @param mixed $tableDescription
     * @return string
     */
    protected function _getCreateSql($tableDescription)
    {
        $_sql = "CREATE TABLE IF NOT EXISTS `{$tableDescription['table_name']}`\n";

        $rows = array();
        if (!empty($tableDescription['fields'])) {
            foreach ($tableDescription['fields'] as $field) {
                $rows[] = $this->_getFieldSql($field);
            }
        }

        foreach ($tableDescription['keys'] as $key) {
            $rows[] = $this->_getKeySql($key);
        }
        foreach ($tableDescription['constraints'] as $key) {
            $rows[] = $this->_getConstraintSql($key);
        }
        $rows = implode(",\n", $rows);
        $_sql .= " ({$rows})";

        if (!empty($tableDescription['engine'])) {
            $_sql .= " ENGINE={$tableDescription['engine']}";
        }
        if (!empty($tableDescription['charset'])) {
            $_sql .= " DEFAULT CHARSET={$tableDescription['charset']}";
        }
        if (!empty($tableDescription['collate'])) {
            $_sql .= " COLLATE={$tableDescription['collate']}";
        }

        return $_sql;
    }

    /**
     * get sql fields list
     *
     * @param mixed $field
     * @return string
     */
    protected function _getFieldSql($field)
    {
        $_fieldSql = "`{$field['name']}` {$field['type']} {$field['extra']}";

        switch ((boolean) $field['is_null']) {
            case true:
                $_fieldSql .= "";
                break;
            case false:
                $_fieldSql .= " NOT NULL";
                break;
        }

        switch ($field['default']) {
            case null:
                $_fieldSql .= "";
                break;
            case 'CURRENT_TIMESTAMP':
                $_fieldSql .= " DEFAULT {$field['default']}";
                break;
            default:
                $_fieldSql .= " DEFAULT '{$field['default']}'";
                break;
        }

        return $_fieldSql;
    }

    /**
     * get sql keys list
     *
     * @param mixed $key
     * @return string
     */
    protected function _getKeySql($key)
    {
        $_keySql = "";
        switch ((string) $key['type']) {
            case 'INDEX':
                $_keySql .= " KEY";
                $_keySql .= " `{$key['name']}`";
                break;
            case 'PRIMARY' :
                $_keySql .= " {$key['type']} KEY";
                break;
            default:
                $_keySql .= " {$key['type']} KEY";
                $_keySql .= " `{$key['name']}`";
                break;
        }


        $fields = array();
        foreach ($key['fields'] as $field) {
            $fields[] = "`{$field}`";
        }
        $fields = implode(',',$fields);
        $_keySql .= "($fields)";
        return $_keySql;
    }

    /**
     * get sql FOREIGN KEY list
     *
     * @param mixed $key
     * @return string
     */
    protected function _getConstraintSql($key)
    {
        $targetRefTable = $this->getStagingTableName('enterprise_staging', $key['ref_table']);

        if ($targetRefTable) {
            $_refTable = "`$targetRefTable`";
        } else {
            $_refTable = "";
            if ($key['ref_db']) {
                $_refTable .= "`{$key['ref_db']}`.";
            }
            $_refTable .= "`{$key['ref_table']}`";
        }

        $onDelete = "";
        if ($key['on_delete']) {
            $onDelete .= "ON DELETE {$key['on_delete']}";
        }

        $onUpdate = "";
        if ($key['on_update']) {
            $onUpdate .= "ON UPDATE {$key['on_update']}";
        }

        if ($this->getStaging()) {
            $prefix = strtoupper($this->getStaging()->getTablePrefix());
        } else {
            $prefix = 'S_';
        }

        $_keySql = " CONSTRAINT `{$prefix}{$key['fk_name']}` FOREIGN KEY (`{$key['pri_field']}`) REFERENCES {$_refTable} (`{$key['ref_field']}`) {$onDelete} {$onUpdate}";

        return $_keySql;
    }

    /**
     * Return Staging table name with all prefixes
     *
     * @param string $model
     * @param string $table
     * @param string $internalPrefix
     * @param bool $ignoreIsStaging
     * @return string
     */
    public function getStagingTableName($model, $table, $internalPrefix = '', $ignoreIsStaging = false)
    {
        if (!$ignoreIsStaging) {
            if (!$this->isStagingItem($model, $table)) {
                return $table;
            }
        }

        $tablePrefix = Enterprise_Staging_Model_Staging_Config::getTablePrefix($this->getStaging(), $internalPrefix);

        return $tablePrefix . $table;
    }

    /**
     * Retrieve table properties as array
     * fields, keys, constraints, engine, charset, create
     *
     * @param string $table
     * @param string $model
     * @param bool   $strongRestrict
     * @return array
     */
    public function getTableProperties($table, $model = 'enterprise_staging', $strongRestrict = false)
    {
        if (!$this->tableExists($model, $table)) {
            if ($strongRestrict) {
                throw new Enterprise_Staging_Exception(Mage::helper('enterprise_staging')
                    ->__('Staging Table %s doesn\'t exists',$table));
            }
            return false;
        }

        $connection = $this->getConnection($model);

        $tableName = $this->getTable($table, $model);
        if (isset($this->_config[$model]['prefix'])) {
            $prefix = $this->_config[$model]['prefix'];
        } else {
            $prefix = '';
        }

        $tableProp = array(
            'table_name'  => $tableName,
            'fields'      => array(),
            'keys'        => array(),
            'constraints' => array(),
            'engine'      => 'MYISAM',
            'charset'     => 'utf8',
            'collate'     => null,
            'create_sql'  => null
        );

        // collect fields
        $sql = "SHOW FULL COLUMNS FROM `{$tableName}`";
        $result = $connection->fetchAll($sql);

        foreach($result as $row) {
            $tableProp['fields'][$row["Field"]] = array(
                'name'      => $row["Field"],
                'type'      => $row["Type"],
                'collation' => $row["Collation"],
                'is_null'   => strtoupper($row["Null"]) == 'YES' ? true : false,
                'key'       => $row["Key"],
                'default'   => $row["Default"],
                'extra'     => $row["Extra"],
                'privileges'=> $row["Privileges"]
            );
        }

        // create sql
        $sql = "SHOW CREATE TABLE `{$tableName}`";
        $result = $connection->fetchRow($sql);

        $tableProp['create_sql'] = $result["Create Table"];

        // collect keys
        $regExp  = '#(PRIMARY|UNIQUE|FULLTEXT|FOREIGN)?\sKEY\s+(`[^`]+` )?(\([^\)]+\))#';
        $matches = array();
        preg_match_all($regExp, $tableProp['create_sql'], $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            if (isset($match[1]) && $match[1] == 'PRIMARY') {
                $keyName = 'PRIMARY';
            } elseif (isset($match[1]) && $match[1] == 'FOREIGN') {
                continue;
            } else {
                $keyName = substr($match[2], 1, -2);
            }
            $fields = $fieldsMatches = array();
            preg_match_all("#`([^`]+)`#", $match[3], $fieldsMatches, PREG_SET_ORDER);
            foreach ($fieldsMatches as $field) {
                $fields[] = $field[1];
            }

            $tableProp['keys'][strtoupper($keyName)] = array(
                'type'   => !empty($match[1]) ? $match[1] : 'INDEX',
                'name'   => $keyName,
                'fields' => $fields
            );
        }

        // collect CONSTRAINT
        $regExp  = '#,\s+CONSTRAINT `([^`]*)` FOREIGN KEY \(`([^`]*)`\) '
            . 'REFERENCES (`[^`]*\.)?`([^`]*)` \(`([^`]*)`\)'
            . '( ON DELETE (RESTRICT|CASCADE|SET NULL|NO ACTION))?'
            . '( ON UPDATE (RESTRICT|CASCADE|SET NULL|NO ACTION))?#';
        $matches = array();
        preg_match_all($regExp, $tableProp['create_sql'], $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $tableProp['constraints'][strtoupper($match[1])] = array(
                'fk_name'   => strtoupper($match[1]),
                'ref_db'    => isset($match[3]) ? $match[3] : null,
                'pri_table' => $table,
                'pri_field' => $match[2],
                'ref_table' => substr($match[4], strlen($prefix)),
                'ref_field' => $match[5],
                'on_delete' => isset($match[6]) ? $match[7] : '',
                'on_update' => isset($match[8]) ? $match[9] : ''
            );
        }

        // engine
        $regExp = "#(ENGINE|TYPE)="
            . "(MEMORY|HEAP|INNODB|MYISAM|ISAM|BLACKHOLE|BDB|BERKELEYDB|MRG_MYISAM|ARCHIVE|CSV|EXAMPLE)"
            . "#i";
        $match  = array();
        if (preg_match($regExp, $tableProp['create_sql'], $match)) {
            $tableProp['engine'] = strtoupper($match[2]);
        }

        //charset
        $regExp = "#DEFAULT CHARSET=([a-z0-9]+)( COLLATE=([a-z0-9_]+))?#i";
        $match  = array();
        if (preg_match($regExp, $tableProp['create_sql'], $match)) {
            $tableProp['charset'] = strtolower($match[1]);
            if (isset($match[3])) {
                $tableProp['collate'] = $match[3];
            }
        }

        return $tableProp;
    }

    /**
     * Check exists table
     *
     * @param string $table
     * @param string $entity
     * @return bool
     */
    public function tableExists($model, $table)
    {
        $connection = $this->getConnection($model);
        $sql = $this->_quote("SHOW TABLES LIKE ?", $this->getTable($table, $model));
        $stmt = $connection->query($sql);
        if (!$stmt->fetch()) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Simple quote SQL statement
     * supported ? or %[type] sprintf format
     *
     * @param string $statement
     * @param array $bind
     * @return string
     */
    protected function _quote($statement, $bind = array())
    {
        $statement = str_replace('?', '%s', $statement);
        if (!is_array($bind)) {
            $bind = array($bind);
        }
        foreach ($bind as $k => $v) {
            if (is_numeric($v)) {
                $bind[$k] = $v;
            }
            elseif (is_null($v)) {
                $bind[$k] = 'NULL';
            }
            else {
                $bind[$k] = "'" . mysql_escape_string($v) . "'";
            }
        }
        return vsprintf($statement, $bind);
    }

    /**
     * Get table name for the entity
     *
     * @param string $entityName
     */
    public function getTable($entityName, $entity)
    {
        /* FIXME getTable() method doesn't needed yet */
        return $entityName;

        if (isset($this->_tables[$entityName])) {
            return $this->_tables[$entityName];
        }
        if (strpos($entityName, '/')) {
            $this->_tables[$entityName] = $this->_resource->getTableName($entityName);
        } elseif (!empty($this->_resourceModel)) {
            $this->_tables[$entityName] = $this->_resource->getTableName(
                $this->_resourceModel.'/'.$entityName);
        } else {
            $this->_tables[$entityName] = $entityName;
        }
        return $this->_tables[$entityName];
    }

/**
     * Get table name for the entity
     *
     * @param string $entityName
     */
    public function getTableName($entityName)
    {
        if (isset($this->_tables[$entityName])) {
            return $this->_tables[$entityName];
        }
        if (strpos($entityName, '/')) {
            $this->_tables[$entityName] = $this->_resource->getTableName($entityName);
        } elseif (!empty($this->_resourceModel)) {
            $this->_tables[$entityName] = $this->_resource->getTableName(
                $this->_resourceModel.'/'.$entityName);
        } else {
            $this->_tables[$entityName] = $entityName;
        }
        return $this->_tables[$entityName];
    }

    /**
     * Begin transaction
     *
     * @param string $type
     * @return Enterprise_Staging_Model_Mysql4_Config
     */
    public function beginTransaction($type)
    {
        $this->getConnection($type)->query('START TRANSACTION');
        return $this;
    }

    /**
     * Commit transaction
     *
     * @param string $type
     * @return Enterprise_Staging_Model_Mysql4_Config
     */
    public function commitTransaction($type)
    {
        $this->getConnection($type)->query('COMMIT');
        return $this;
    }

    /**
     * Rollback transaction
     *
     * @param string $type
     * @return Enterprise_Staging_Model_Mysql4_Config
     */
    public function rollbackTransaction($type)
    {
        $this->getConnection($type)->query('ROLLBACK');
        return $this;
    }

    /**
     * Check if item in staging
     *
     * @param string $model
     * @param string $table
     * @return bool
     */
    public function isStagingItem($model, $table)
    {
        if (in_array($table, $this->_excludeList)) {
            return false;
        }

        $stagingItems   = Enterprise_Staging_Model_Staging_Config::getStagingItems();
        $stagingItem    = $stagingItems->{$model};

        if (!(int)$stagingItem->use_table_prefix) {
            return false;
        }

        if (empty($table)) {
            return true;
        } else {
            $tables = (array) $stagingItem->entities;
            if (!empty($tables)) {
                if (array_key_exists($table, $tables)) {
                    return true;
                }
            } else {
                return true;
            }
        }

        return false;
    }
}
