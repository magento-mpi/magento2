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
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

    public function __construct()
    {
        $this->_read  = Mage::getSingleton('core/resource')->getConnection('staging_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('staging_write');

        $this->_resource = Mage::getResourceSingleton('enterprise_staging/resource');
    }

    abstract public function create(Enterprise_Staging_Model_Staging $staging);

    abstract public function merge(Enterprise_Staging_Model_Staging $staging);

    abstract public function rollback(Enterprise_Staging_Model_Staging $staging);

    abstract public function check(Enterprise_Staging_Model_Staging $staging);

    abstract public function repair(Enterprise_Staging_Model_Staging $staging);

    abstract public function copy(Enterprise_Staging_Model_Staging $staging);

    abstract public function backup(Enterprise_Staging_Model_Staging $staging);



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
        if ($_staging && $_staging->getId() == (int) $this->_staging) {
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
     * Get connection by name or type
     *
     * @param   string $connectionName
     * @return  Zend_Db_Adapter_Abstract
     */
    public function getConnection($connectionName, $scope = 'read')
    {
        $connectionName = $connectionName . '_' .$scope;

        if (isset($this->_connections[$connectionName])) {
            return $this->_connections[$connectionName];
        }

        try {
            $this->_connections[$connectionName] = Mage::getSingleton('core/resource')->getConnection($connectionName);
        } catch (Enterprise_Staging_Exception $e) {
            die('Enterprise_Staging_Model_Mysql4_Config::_getConnection()');
        }

        return $this->_connections[$connectionName];
    }

    /**
     * Retrieve table properties as array
     * fields, keys, constraints, engine, charset, create
     *
     * @param string $item
     * @param string $table
     * @return array
     */
    public function getTableProperties($item, $table)
    {
        if (!$this->tableExists($item, $table)) {
            return false;
        }

        $connection = $this->getConnection($item);

        $tableName = $this->getTable($table, $item);
        $prefix    = $this->_config[$item]['prefix'];
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
        $regExp  = '#(PRIMARY|UNIQUE|FULLTEXT|FOREIGN)?\sKEY (`[^`]+` )?(\([^\)]+\))#';
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
    public function tableExists($item, $table)
    {
        $connection = $this->getConnection($item);
        $sql = $this->_quote("SHOW TABLES LIKE ?", $this->getTable($table, $item));

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
}