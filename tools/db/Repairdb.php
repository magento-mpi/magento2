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
 * @category   Mage
 * @package    tools
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Tools_Db_Repair_Mysql4
{
    const TYPE_CORRUPTED    = 'corrupted';
    const TYPE_REFERENCE    = 'reference';

    /**
     * Corrupted Database resource
     *
     * @var resource
     */
    protected $_corrupted;

    /**
     * Reference Database resource
     *
     * @var resource
     */
    protected $_reference;

    /**
     * Config
     *
     * @var array
     */
    protected $_config = array();

    /**
     * Set connection
     *
     * @param array $config
     * @param string $type
     * @return Tools_Db_Repair
     */
    public function setConnection(array $config, $type)
    {
        if ($type == self::TYPE_CORRUPTED) {
            $connection = &$this->_corrupted;
        }
        elseif ($type == self::TYPE_REFERENCE) {
            $connection = &$this->_reference;
        }
        else {
            throw new Exception('Unknown connection type');
        }

        $required = array('hostname', 'username', 'password', 'database', 'prefix');
        foreach ($required as $field) {
            if (!array_key_exists($field, $config)) {
                throw new Exception(sprintf('Please specify %s for %s database connection', $field, $type));
            }
        }

        if (!$connection = @mysql_connect($config['hostname'], $config['username'], $config['password'], true)) {
            throw new Exception(sprintf('Error %s database connection: #%d, %s', $type, mysql_errno(), mysql_error()));
        }
        if (!@mysql_select_db($config['database'], $connection)) {
            throw new Exception(sprintf('Error %s database select database (%s): #%d, %s', $config['database'], $type, mysql_errno(), mysql_error()));
        }
        mysql_query('SET NAMES utf8', $connection);

        $this->_config[$type] = $config;

        return $this;
    }

    /**
     * Check exists connections
     *
     * @return bool
     */
    protected function _checkConnection()
    {
        if (is_null($this->_corrupted)) {
            throw new Exception(sprintf('Invalid connection for %s database', self::TYPE_CORRUPTED));
        }
        if (is_null($this->_reference)) {
            throw new Exception(sprintf('Invalid connection for %s database', self::TYPE_REFERENCE));
        }
        return true;
    }

    /**
     * Retrieve table name
     *
     * @param string $table
     * @param string $type
     * @return string
     */
    public function getTable($table, $type)
    {
        $prefix = $this->_config[$type]['prefix'];
        return $prefix . $table;
    }

    /**
     * Retrieve connection resource
     *
     * @param string $type
     * @return resource
     */
    protected function _getConnection($type)
    {
        if ($type == self::TYPE_CORRUPTED) {
            return $this->_corrupted;
        }
        elseif ($type == self::TYPE_REFERENCE) {
            return $this->_reference;
        }
        else {
            throw new Exception(sprintf('Unknown connection type "%s"', $type));
        }
    }

    /**
     * Check connection type
     *
     * @param string $type
     * @return bool
     */
    protected function _checkType($type)
    {
        if ($type == self::TYPE_CORRUPTED) {
            return true;
        }
        elseif ($type == self::TYPE_REFERENCE) {
            return true;
        }
        else {
            throw new Exception(sprintf('Unknown connection type "%s"', $type));
        }
    }

    /**
     * Check exists table
     *
     * @param string $table
     * @param string $type
     * @return bool
     */
    public function tableExists($table, $type)
    {
        $this->_checkConnection();
        $this->_checkType($type);

        $sql = $this->_quote("SHOW TABLES LIKE ?", $this->getTable($table, $type));
        $res = mysql_query($sql, $this->_getConnection($type));
        if (!mysql_fetch_row($res)) {
            return false;
        }
        return true;
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
     * Compare core_resource version
     *
     * @return array
     */
    public function compareResource()
    {
        if (!$this->tableExists('core_resource', self::TYPE_CORRUPTED)) {
            throw new Exception(sprintf('%s database is not valid Magento database', self::TYPE_CORRUPTED));
        }
        if (!$this->tableExists('core_resource', self::TYPE_REFERENCE)) {
            throw new Exception(sprintf('%s database is not valid Magento database', self::TYPE_REFERENCE));
        }

        $corrupted = $reference = array();

        $sql = "SELECT * FROM `{$this->getTable('core_resource', self::TYPE_CORRUPTED)}`";
        $res = mysql_query($sql, $this->_getConnection(self::TYPE_CORRUPTED));
        while ($row = mysql_fetch_assoc($res)) {
            $corrupted[$row['code']] = $row['version'];
        }

        $sql = "SELECT * FROM `{$this->getTable('core_resource', self::TYPE_REFERENCE)}`";
        $res = mysql_query($sql, $this->_getConnection(self::TYPE_REFERENCE));
        while ($row = mysql_fetch_assoc($res)) {
            $reference[$row['code']] = $row['version'];
        }

        $compare = array();
        foreach ($reference as $k => $v) {
            if (!isset($corrupted[$k])) {
                $compare[] = sprintf('In corrupted database module "%s" is not installed', $k);
            }
            elseif ($corrupted[$k] != $v) {
                $compare[] = sprintf('In corrupted database module "%s" has version %s (version %s installed in reference DB)', $k, $corrupted[$k], $v);
            }
        }

        return $compare;
    }

    /**
     * Check database supported InnoDb Engine
     *
     * @param string $type
     * @return bool
     */
    public function checkInnodbSupport($type)
    {
        $this->_checkConnection();
        $this->_checkType($type);

        $sql = $this->_quote("SHOW VARIABLES LIKE ?", 'have_innodb');
        $res = mysql_query($sql, $this->_getConnection($type));
        $row = mysql_fetch_assoc($res);
        if ($row && strtoupper($row['Value']) == 'YES') {
            return true;
        }
        return false;
    }

    /**
     * Apply to Database needed settings
     *
     * @param string $type
     * @return Tools_Db_Repair_Mysql4_Mysql4
     */
    public function start($type)
    {
        $this->sqlQuery('/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */', $type);
        $this->sqlQuery('/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */', $type);
        $this->sqlQuery('/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */', $type);
        $this->sqlQuery('/*!40101 SET NAMES utf8 */', $type);
        $this->sqlQuery('/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */', $type);
        $this->sqlQuery('/*!40103 SET TIME_ZONE=\'+00:00\' */', $type);
        $this->sqlQuery('/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */', $type);
        $this->sqlQuery('/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */', $type);
        $this->sqlQuery('/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE=\'NO_AUTO_VALUE_ON_ZERO\' */', $type);
        $this->sqlQuery('/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */', $type);

        return $this;
    }

    /**
     * Return old settings to database (applied in start method)
     *
     * @param string $type
     * @return Tools_Db_Repair_Mysql4
     */
    public function finish($type)
    {
        $this->sqlQuery('/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */', $type);
        $this->sqlQuery('/*!40101 SET SQL_MODE=@OLD_SQL_MODE */', $type);
        $this->sqlQuery('/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */', $type);
        $this->sqlQuery('/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */', $type);
        $this->sqlQuery('/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */', $type);
        $this->sqlQuery('/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */', $type);
        $this->sqlQuery('/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */', $type);
        $this->sqlQuery('/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */', $type);

        return $this;
    }

    /**
     * Begin transaction
     *
     * @param string $type
     * @return Tools_Db_Repair_Mysql4
     */
    public function begin($type)
    {
        $this->sqlQuery('START TRANSACTION', $type);
        return $this;
    }

    /**
     * Commit transaction
     *
     * @param string $type
     * @return Tools_Db_Repair_Mysql4
     */
    public function commit($type)
    {
        $this->sqlQuery('COMMIT', $type);
        return $this;
    }

    /**
     * Rollback transaction
     *
     * @param string $type
     * @return Tools_Db_Repair_Mysql4
     */
    public function rollback($type)
    {
        $this->sqlQuery('ROLLBACK', $type);
        return $this;
    }

    /**
     * Retrieve table properties as array
     * fields, keys, constraints, engine, charset, create
     *
     * @param string $table
     * @param string $type
     * @return array
     */
    public function getTableProperties($table, $type)
    {
        $this->_checkConnection();
        $this->_checkType($type);

        if (!$this->tableExists($table, $type)) {
            return false;
        }

        $tableName = $this->getTable($table, $type);
        $prefix    = $this->_config[$type]['prefix'];
        $tableProp = array(
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
        $res = mysql_query($sql, $this->_getConnection($type));
        while($row = mysql_fetch_row($res)) {
            $tableProp['fields'][$row[0]] = array(
                'type'      => $row[1],
                'is_null'   => strtoupper($row[3]) == 'YES' ? true : false,
                'default'   => $row[5],
                'extra'     => $row[6],
                'collation' => $row[2],
            );
        }

        // create sql
        $sql = "SHOW CREATE TABLE `{$tableName}`";
        $res = mysql_query($sql, $this->_getConnection($type));
        $row = mysql_fetch_row($res);

        $tableProp['create_sql'] = $row[1];

        // collect keys
        $regExp  = '#(PRIMARY|UNIQUE|FULLTEXT|FOREIGN)?\sKEY (`[^`]+` )?(\([^\)]+\))#';
        $matches = array();
        preg_match_all($regExp, $tableProp['create_sql'], $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            if (isset($match[1]) && $match[1] == 'PRIMARY') {
                $keyName = 'PRIMARY';
            }
            elseif (isset($match[1]) && $match[1] == 'FOREIGN') {
                continue;
            }
            else {
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

    public function getTables($type)
    {
        $this->_checkConnection();
        $this->_checkType($type);
        $prefix = $this->_config[$type]['prefix'];

        $tables = array();

        $sql = 'SHOW TABLES';
        $res = mysql_query($sql, $this->_getConnection($type));
        while ($row = mysql_fetch_row($res)) {
            $tableName = substr($row[0], strlen($prefix));
            $tables[$tableName] = $this->getTableProperties($tableName, $type);
        }

        return $tables;
    }

    /**
     * Add constraint
     *
     * @param array $config
     * @param string $type
     * @return Tools_Db_Repair_Mysql4
     */
    public function addConstraint(array $config, $type)
    {
        $this->_checkConnection();
        $this->_checkType($type);

        $required = array('fk_name', 'pri_table', 'pri_field', 'ref_table', 'ref_field', 'on_update', 'on_delete');
        foreach ($required as $field) {
            if (!array_key_exists($field, $config)) {
                throw new Exception(sprintf('Invalid required config parameter "%s" for create CONSTRAINT', $field));
            }
        }

        if ($config['on_delete'] == '' || strtoupper($config['on_delete']) == 'CASCADE'
            || strtoupper($config['on_delete']) == 'RESTRICT') {
            $sql = "DELETE `p`.* FROM `{$this->getTable($config['pri_table'], $type)}` AS `p`"
                . " LEFT JOIN `{$this->getTable($config['ref_table'], $type)}` AS `r`"
                . " ON `p`.`{$config['pri_field']}` = `r`.`{$config['ref_field']}`"
                . " WHERE `p`.`{$config['pri_field']}` IS NULL";
            $this->sqlQuery($sql, $type);
        }
        elseif (strtoupper($config['on_delete']) == 'SET NULL') {
            $sql = "UPDATE `{$this->getTable($config['pri_table'], $type)}` AS `p`"
                . " LEFT JOIN `{$this->getTable($config['ref_table'], $type)}` AS `r`"
                . " ON `p`.`{$config['pri_field']}` = `r`.`{$config['ref_field']}`"
                . " SET `p`.`{$config['pri_field']}`=NULL"
                . " WHERE `p`.`{$config['pri_field']}` IS NULL";
            $this->sqlQuery($sql, $type);
        }

        $sql = "ALTER TABLE `{$this->getTable($config['pri_table'], $type)}`"
            . " ADD CONSTRAINT `{$config['fk_name']}`"
            . " FOREIGN KEY (`{$config['pri_field']}`)"
            . " REFERENCES `{$this->getTable($config['ref_table'], $type)}`"
            . "  (`{$config['ref_field']}`)";
        if (!empty($config['on_delete'])) {
            $sql .= ' ON DELETE ' . strtoupper($config['on_delete']);
        }
        if (!empty($config['on_update'])) {
            $sql .= ' ON UPDATE ' . strtoupper($config['on_update']);
        }

        $this->sqlQuery($sql, $type);

        return $this;
    }

    /**
     * Drop Foreign Key from table
     *
     * @param string $table
     * @param string $foreignKey
     * @param string $type
     */
    public function dropConstraint($table, $foreignKey, $type)
    {
        $this->_checkConnection();
        $this->_checkType($type);

        $sql = "ALTER TABLE `{$table}` DROP FOREIGN KEY `{$foreignKey}`";
        $this->sqlQuery($sql, $type);

        return $this;
    }

    /**
     * Add column to table
     * @param string $table
     * @param string $column
     * @param array $config
     * @param string $type
     * @param string|false|null $after
     */
    public function addColumn($table, $column, array $config, $type, $after = null)
    {
        $this->_checkConnection();
        $this->_checkType($type);

        if (!$this->tableExists($table, $type)) {
            return $this;
        }

        $required = array('type', 'is_null', 'default');
        foreach ($required as $field) {
            if (!array_key_exists($field, $config)) {
                throw new Exception(sprintf('Invalid required config parameter "%s" for create COLUMN', $field));
            }
        }

        $sql = "ALTER TABLE `{$this->getTable($table, $type)}` ADD COLUMN `{$column}`"
            . " {$config['type']}"
            . ($config['is_null'] ? "" : " NOT NULL")
            . ($config['default'] ? " DEFAULT '{$config['default']}'" : "")
            . (!empty($config['extra']) ? " {$config['extra']}" : "");
        if ($after === false) {
            $sql .= " FIRST";
        }
        elseif (!is_null($after)) {
            $sql .= " AFTER `{$after}`";
        }

        $this->sqlQuery($sql, $type);

        return $this;
    }

    /**
     * Add primary|unique|fulltext|index to table
     *
     * @param string $table
     * @param array $config
     * @param string $type
     * @return Tools_Db_Repair_Mysql4
     */
    public function addKey($table, array $config, $type)
    {
        $this->_checkConnection();
        $this->_checkType($type);

        if (!$this->tableExists($table, $type)) {
            return $this;
        }

        $required = array('type', 'name', 'fields');
        foreach ($required as $field) {
            if (!array_key_exists($field, $config)) {
                throw new Exception(sprintf('Invalid required config parameter "%s" for create KEY', $field));
            }
        }

        switch (strtolower($config['type'])) {
            case 'primary':
                $condition = "PRIMARY KEY";
                break;
            case 'unique':
                $condition = "UNIQUE `{$config['name']}`";
                break;
            case 'fulltext':
                $condition = "FULLTEXT `{$config['name']}`";
                break;
            default:
                $condition = "INDEX `{$config['name']}`";
                break;
        }
        if (!is_array($config['fields'])) {
            $config['fields'] = array($config['fields']);
        }

        $sql = "ALTER TABLE `{$this->getTable($table, $type)}` ADD {$condition}"
            . " (`" . join("`,`", $config['fields']) . "`)";
        $this->sqlQuery($sql, $type);

        return $this;
    }

    /**
     * Change table storage engine
     *
     * @param string $table
     * @param string $engine
     * @param string $type
     * @return Tools_Db_Repair_Mysql4
     */
    public function changeTableEngine($table, $type, $engine)
    {
        $this->_checkConnection();
        $this->_checkType($type);

        $sql = "ALTER TABLE `{$this->getTable($table, $type)}` ENGINE={$engine}";
        $this->sqlQuery($sql, $type);

        return $this;
    }

    /**
     * Change table storage engine
     *
     * @param string $table
     * @param string $charset
     * @param string $type
     * @return Tools_Db_Repair_Mysql4
     */
    public function changeTableCharset($table, $type, $charset, $collate = null)
    {
        $this->_checkConnection();
        $this->_checkType($type);

        $sql = "ALTER TABLE `{$this->getTable($table, $type)}` DEFAULT CHARACTER SET={$charset}";
        if ($collate) {
            $sql .= " COLLATE {$collate}";
        }
        $this->sqlQuery($sql, $type);

        return $this;
    }

    /**
     * Run SQL query
     *
     * @param string $sql
     * @param string $type
     * @return resource
     */
    public function sqlQuery($sql, $type)
    {
        $this->_checkConnection();
        $this->_checkType($type);

        if (!$res = @mysql_query($sql, $this->_getConnection($type))) {
            throw new Exception(sprintf("Error #%d: %s on SQL: %s",
                mysql_errno($this->_getConnection($type)),
                mysql_error($this->_getConnection($type)),
                $sql
            ));
        }
        return $res;
    }

    /**
     * Retrieve previous key from array by key
     *
     * @param array $array
     * @param mixed $key
     * @return mixed
     */
    public function arrayPrevKey(array $array, $key)
    {
        $prev = false;
        foreach ($array as $k => $v) {
            if ($k == $key) {
                return $prev;
            }
            $prev = $k;
        }
    }

    /**
     * Retrieve next key from array by key
     *
     * @param array $array
     * @param mixed $key
     * @return mixed
     */
    public function arrayNextKey(array $array, $key)
    {
        $next = false;
        foreach ($array as $k => $v) {
            if ($next === true) {
                return $k;
            }
            if ($k == $key) {
                $next = true;
            }
        }
        return false;
    }
}

class Tools_Db_Repair_Helper
{
    protected $_images      = array(
        'error.gif'     => array(
            'base64'    => 'R0lGODlhEAAQAPeAAOxwW+psWe5zXPN8YOtuWvu9qednV/B4X+92XfWCY+JfU+hpWPF6X/N+Yfi0oOZlVvaJa+ViVfbZ0vrJvvKpn/Omkfrd1vSAYuWOg9yXiN19b8JKMeWzqPLUzvWwo9RkUsNMM+ySf/aKcvKKcs5dTPSZhPGon+qNe+yLf+OEdfGTgul9aNVfRup1XOmllva0pM1hS+FdUvq5qfCXg+y6r+BzYPrZ0+yYifTDuOa0qfjb1Pq8qOlvX+NmW+NhVOx/Z/GdkPm5puVxWOeRhfiiidFhUPPVzvWDafGlmfSMdORnXN1uVsxfSfHTzO6DbveFa8VONeuJfe2SifSsofGXhOFyWu2fleaIePLBtvmRee6qm9FhScxVO8ZaQ+dsXd1wXfezpMZVPt6Zi/ihiPCfjsNSO/ijiviGbPi1pfmMdOqHffOvpuGdjtBYQOh/Z/KAZe6gld18b/i2ofWBYvSmku16YPGom+yBbNhtVuySiOeQhPi1pu68sfezoPSEZ/////rr5wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAEAAIAALAAAAAAQABAAAAjSAAEJHEiwYEELMrI8OZLkhQ6DgCakcULHgYMKK37gKDjhDJUCZiBAILIjhBAsAy2ImFEgQYI/fxoMCHKiigSBe+60nHMBJoMDCNB8cSFwRIUxF2TCRCAAgIobeATWkeNnwE+YAAgE4GGnjcAWfd4AFWDjT4AFBrwg4SLQDZkSTQkAWWPgQYQoQ2AI1FIDjNYFMCP4UEChiBiBEpZc8VBXSh4FMShoCNNhIB8WKaagUNJDjYk4G3IUpLHlgx44VjCQKMMBohE2TKCA6JKhCcTbBQMCADs=',
            'type'      => 'img/gif'
        ),
        'success.gif'   => array(
            'base64'    => 'R0lGODlhEAAQAPeeAJDOf67cpYPOd7HLr53YknLIaPz9+7fhr7XhrnrMbW/CYW23V67Xoa/XoLTaprTZpb/juG7EYqnbl0yXPd3q2jN7MJfMhXO6XK/cpm7EYTR/MW+1WHC/V2vDSnPHZmSwTGnCSLLUsFSyNIXFdbXWsL7jtnnBZHTDZE6bQbXbqNvl2pzOjNrn2ZrUjZ3Oi/3+/VG2LSmPJCaDI5/Skb3esbnasH7BaXG5W7fhsCh5JCZ+IyZyJGbESJnNimq5UHXIaF6pSLPZpVy0PY68i2/GZK3em5bNiGy2VpbHg9bu0nDBY6fYk0iwJ+3364nEdsbnunS3WzSOMUKgMkOgMj6MOrTfrH+4aXC4WePw3pbLhqHWlVzCPKPXlnDHZW61WI24in/KcD6KOnbKavH58HbJaXy6ZHe8YLHdp9rk2Y61i/T68t7r2ovIeLTdqtns1JrHh1KgQnK5W6HXlZrKh37Hb4DMcnHEY3S3XHG+X6vTm6vSm+Px3m+1WbLbqH28ZrXfrOHu23nJayh2JD6YO5vXkX23ZnTCWVzAOsDkuYDKczuhJpjLhnTIaH2+Z1q+N4fJeYO+bZnRi2nHScfnuj6EOpDEi27FY5nQjJjMh3HJVOb044fCcm/DYf////j39wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAEAAJ4ALAAAAAAQABAAAAjdAD0JHEiwYME9RTIdcgRiCSCDniZJMsQlQAAtPkTQKPhkC4ADBAQIIICAjaIaA7HwAIAggZgCjH4EquJECgWBEjgcSFCgi6UIdkrQ+QOkksAOcgiRIZKBkwJEnTRFcjFIIIwzdTzgSKIEQicDl2wwkCGQCQYwicZ0avLVyI1GDXQIFDKjxYlHajq9yLIgjoU3YQSu+NDGBJ4Rbnoc8XLHAYovAtdM2dTHzIUrfDZACWJFg4qBJGJASoHJT5lFDwrtGFAwRBQ4c/LoQTKhAmuDLIZQySGIUho0EIMXDAgAOw==',
            'type'      => 'img/gif'
        )
    );

    /**
     * Print Header HTML
     *
     * @param string $title
     */
    public function printHtmlHeader()
    {
        echo <<<HEADER
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Magento Database Repair Tool</title>
<style type="text/css">
* {
    padding: 0px;
    margin: 0px;
} body {
    background-color: #FBFAF6;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 12px;
} .fieldset {
    background: #FBFAF6 none repeat scroll 0 0;
    border: 1px solid #BBAFA0;
    margin: 28px 0;
    padding: 22px 25px 12px;
} .fieldset legend {
    display: none;
} .fieldset .legend {
    background: #F9F3E3 none repeat scroll 0 0;
    border: 1px solid #F19900;
    color: #E76200;
    float: left;
    font-size: 1.1em;
    font-weight: bold;
    margin-top: -33px;
    padding: 0 8px;
} .fieldset label {
    color: #666666;
    font-weight: bold;
} .fieldset input {
    padding:2px;
    border:1px solid #B6B6B6;
    width: 95%;
} .corrupted {
    float: left;
    width: 49%;
} .reference {
    float: right;
    width: 49%;
} .clear {
    clear: both;
} .required {
    color: #EB340A;
} .container {
    border: 1px solid #ffffff;
    background-color: #ffffff;
    border-left: 1px solid #E4E3E0;
    border-right: 1px solid #E4E3E0;
} .container, .header, .footline, .footer {
    width: 70%;
    margin: 0 auto;
    padding: 10px;
} .container h2 {
    font-size:1.6em !important;
    font-weight:normal !important;
    margin: 0;
    padding: 0;
    text-align:left;
    text-transform:none !important;
    color: #0A263C;
    line-height: 1.2em;
    margin-bottom: 0.2em;
} .header {
    background-color: #496778;
} .header h1 {
    font-size: 19px;
    font-style: normal;
    font-weight: normal;
    color: #ffffff;
    height: 60px;
    line-height: 60px;
    padding-left: 20px;
}.page-head {
    border-bottom:1px solid #CCCCCC;
    margin:0 0 15px;
} .box {
    margin-bottom: 10px;
} .box input {
    margin-top: 3px;
} .button-set {
    border-top:1px solid #E4E4E4;
    clear:both;
    margin-top:4em;
    padding-top:8px;
    text-align:right;
} .form-button{
    background:#F18200 none repeat scroll 0 0;
    border:1px solid #DE5400;
    color:#FFFFFF;
    cursor:pointer;
    font-family:arial,sans-serif !important;
    font-size:12px !important;
    font-size-adjust:none !important;
    font-stretch:normal !important;
    font-style:normal !important;
    font-variant:normal !important;
    font-weight:bold !important;
    line-height:normal !important;
    overflow:visible;
    padding:1px 8px;
    text-align:center;
    vertical-align:middle;
    width:auto;
} p.required {
    margin-bottom:0.8em;
} input.highlight_error {
    background: #FAEBE7 none repeat scroll 0 0 !important;
    border: 1px dashed #EB340A !important;
} .messages {
    /* is a div object with messages*/
} ul.msg_error {
    list-style: none;
    border: 1px solid #F16048;
    padding: 5px;
    padding-left: 8px;
    background-color: #FAEBE7;
} ul.msg_error li {
    color: #DF280A;
    font-weight: bold;
    padding: 5px;
    padding-left: 24px;
    background-image: url({$this->getImageSrc('error.gif')});
    background-repeat: no-repeat;
    background-position: center left;
} ul.msg_success {
    list-style: none;
    border: 1px solid #3d6611;
    padding: 5px;
    padding-left: 8px;
    background-color: #eff5ea;

} ul.msg_success li {
    color: #3d6611;
    font-weight: bold;
    padding: 5px;

    background-image: url({$this->getImageSrc('success.gif')});
    background-repeat: no-repeat;
    background-position: center left;
    padding-left: 24px;
} .footline {
    height: 8px;
    background-color: #B6D1E2;
    padding-top: 2px;
    padding-bottom: 4px;
} .footer {
    height: 70px;
    background-color: #496778;
} .footer p {
    text-align: center;
    color: #ECF3F6;
    line-height: 40px;
}
</style>
</head>

<body>
<div class="header">
    <h1>Magento Database Repair Tool</h1>
</div>
HEADER;
    }

    /**
     * Print Footer HTML
     */
    public function printHtmlFooter()
    {
        $date = gmdate('Y');
        echo <<<FOOTER
<div class="footline"><br /></div>
<div class="footer"><p>Magento is a trademark of Irubin Consulting Inc. DBA Varien. Copyright © {$date} Irubin Consulting Inc.</p></div>
</body>
</html>
FOOTER;
    }

    /**
     * Print HTML form header
     * @param string $action
     */
    public function printHtmlFormHead()
    {
        echo <<<FORM
<form action="{$_SERVER['PHP_SELF']}" method="post" enctype="multipart/form-data" name="frm_db_repair" id="frm_db_repair">
FORM;
    }

    /**
     * Print HTML form footer
     */
    public function printHtmlFormFoot()
    {
        echo <<<FORM
</form>
FORM;
    }

    /**
     * Print javascript fragment on configuration step
     */
    public function printJsConfiguration()
    {
        echo <<<JAVASCRIPT
<script type="text/javascript">
var classTools = {
    has: function(objElement, strClass){
        if (objElement.className) {
            var arrList = objElement.className.split(' ');
            var strClassUpper = strClass.toUpperCase();
            for (var i=0; i<arrList.length; i++) {
                if (arrList[i].toUpperCase() == strClassUpper) {
                    return true;
                }
            }
        }
        return false;
    },
    add: function(objElement, strClass)
    {
        if (objElement.className) {
            var arrList = objElement.className.split(' ');
            var strClassUpper = strClass.toUpperCase();
            for (var i=0; i<arrList.length; i++) {
                if (arrList[i].toUpperCase() == strClassUpper) {
                    arrList.splice(i, 1);
                    i--;
                }
            }
            arrList[arrList.length] = strClass;
            objElement.className = arrList.join(' ');
        }
        else {
            objElement.className = strClass;
        }
    },
    remove: function(objElement, strClass) {
        if (objElement.className) {
            var arrList = objElement.className.split(' ');
            var strClassUpper = strClass.toUpperCase();
            for (var i=0; i<arrList.length; i++) {
                if (arrList[i].toUpperCase() == strClassUpper) {
                    arrList.splice(i, 1);
                    i--;
                }
            }
            objElement.className = arrList.join(' ');
        }
    }
};
function repairContinue()
{
    var isErrors = false;
    var inputs = document.getElementsByTagName('input');
    for(var i=0; i<inputs.length; i++) {
        if (classTools.has(inputs[i], 'check_required')) {
            if (inputs[i].value.length > 0) {
                classTools.remove(inputs[i], 'highlight_error');
                // ex remove tooltip with error if exists
            } else {
                classTools.add(inputs[i], 'highlight_error');
                // ex add tooltip with error
                isErrors = true;
            }
        }
    }
    if (!isErrors) {
        document.getElementById('button-continue').disabled = true;
        document.getElementById('frm_db_repair').submit();
        return false;
    }
    return false;
}
</script>
JAVASCRIPT;
    }

    /**
     * Print HTML container header fragment
     */
    public function printHtmlContainerHead()
    {
        echo <<<HTML
<div class="container">
HTML;
    }

    /**
     * Print HTML container footer fragment
     */
    public function printHtmlContainerFoot()
    {
        echo <<<HTML
</div>
HTML;
    }

    /**
     * Print javascript fragment on confirmation step
     */
    public function printJsConfirmation()
    {
        echo <<<JAVASCRIPT
<script type="text/javascript">
function repairContinue()
{
    document.getElementById('button-continue').disabled = true;
    document.getElementById('frm_db_repair').submit();
    return false;
}
</script>
JAVASCRIPT;
    }

    /**
     * Print messages block
     *
     * @param array|string $messages
     * @param string $type
     */
    public function printHtmlMessage($messages, $type = 'error')
    {
        if (!is_array($messages)) {
            $messages = array($messages);
        }
        echo <<<HTML
<div class="messages">
    <ul class="msg_{$type}">
HTML;
        foreach ($messages as $message) {
            $message = htmlspecialchars($message);
            echo <<<HTML
        <li>{$message}</li>
HTML;
        }
        echo <<<HTML
    </ul>
</div>
HTML;
    }

    /**
     * Print Page head block
     *
     * @param string $title
     */
    public function printHtmlPageHead($title)
    {
        $title = htmlspecialchars($title);
        echo <<<HTML
<div class="page-head">
    <h2>{$title}</h2>
</div>
HTML;
    }

    public function printHtmlConfigurationBlock()
    {
        echo <<<HTML
<div class="corrupted">
    <fieldset class="fieldset">
        <legend>Corrupted Database Connection</legend>
        <div class="legend">Corrupted Database Connection</div>
        <div class="box">
            <label for="corrupted_hostname">Host <span class="required">*</span></label><br />
            <input value="{$this->getPost('corrupted/hostname')}" type="text" name="corrupted[hostname]" id="corrupted_hostname" class="check_required" />
        </div>
        <div class="box">
            <label for="corrupted_database">Database Name <span class="required">*</span></label><br />
            <input value="{$this->getPost('corrupted/database')}" type="text" name="corrupted[database]" id="corrupted_database" class="check_required" />
        </div>
        <div class="box">
            <label for="corrupted_username">User Name<span class="required">*</span></label><br />
            <input value="{$this->getPost('corrupted/username')}" type="text" name="corrupted[username]" id="corrupted_username" class="check_required" />
        </div>
        <div class="box">
            <label for="corrupted_password">User Password </label><br />
            <input value="{$this->getPost('corrupted/password')}" type="password" name="corrupted[password]" id="corrupted_password" />
        </div>
        <div class="box">
            <label for="corrupted_prefix">Tables Prefix</label><br />
            <input value="{$this->getPost('corrupted/prefix')}" type="text" name="corrupted[prefix]" id="corrupted_prefix" />
        </div>
    </fieldset>
</div>
<div class="reference">
    <fieldset class="fieldset">
        <legend>Reference Database Connection</legend>
        <div class="legend">Reference Database Connection</div>

        <div class="box">
            <label for="reference_hostname">Host <span class="required">*</span></label><br />
            <input value="{$this->getPost('reference/hostname')}" type="text" name="reference[hostname]" id="reference_hostname" class="check_required" />
        </div>
        <div class="box">
            <label for="reference_database">Database Name <span class="required">*</span></label><br />
            <input value="{$this->getPost('reference/database')}" type="text" name="reference[database]" id="reference_database" class="check_required" />
        </div>
        <div class="box">
            <label for="reference_username">User Name<span class="required">*</span></label><br />
            <input value="{$this->getPost('reference/username')}" type="text" name="reference[username]" id="reference_username" class="check_required" />
        </div>
        <div class="box">
            <label for="reference_password">User Password </label><br />
            <input value="{$this->getPost('reference/password')}" type="password" name="reference[password]" id="reference_password" />
        </div>
        <div class="box">
            <label for="reference_prefix">Tables Prefix</label><br />
            <input value="{$this->getPost('reference/prefix')}" type="text" name="reference[prefix]" id="reference_prefix" />
        </div>
    </fieldset>
</div>
HTML;
    }

    public function printHtmlButtonSet($withRequired = false)
    {
        echo <<<HTML
<div class="button-set">
HTML;
        if ($withRequired) {
            echo <<<HTML
    <p class="required">* Required Fields</p>
HTML;
        }
        echo <<<HTML
    <button id="button-continue" class="form-button" type="submit" onclick="return repairContinue();">
        <span>Continue</span>
    </button>
</div>
HTML;
    }

    /**
     * Print HTML Fieldset header fragment
     *
     * @param string $legend
     */
    public function printHtmlFieldsetHead($legend)
    {
        $legend = htmlspecialchars($legend);
        echo <<<HTML
<fieldset class="fieldset">
    <legend>{$legend}</legend>
    <div class="legend">{$legend}</div>
HTML;
    }
    /**
     * Print HTML Fieldset footer fragment
     */
    public function printHtmlFieldsetFoot()
    {
        echo <<<HTML
</fieldset>
HTML;
    }

    /**
     * Print HTML list of events
     *
     * @param array $list
     * @param string $class the class for ul
     */
    public function printHtmlList(array $list, $class = null)
    {
        $classFragment = null;
        if ($class) {
            $classFragment = " class=\"{$class}\"";
        }
        echo "<ul{$classFragment}>";
        foreach ($list as $li) {
            $li = htmlspecialchars($li);
            echo "<li>{$li}</li>";
        }
        echo "</ul>";
    }

    public function printHtmlNote($text)
    {
        $text = str_replace("\n", "<br />", htmlspecialchars($text));
        echo <<<HTML
<p class="note">{$text}</p>
HTML;
    }

    public function printHtmlFormHidden()
    {
        echo <<<HTML
        <input type="hidden" name="post_form" value="true" />
HTML;
    }

    /**
     * Retrieve POST data
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getPost($key = null, $default = null)
    {
        if (is_null($key)) {
            return $_POST;
        }
        if (strpos($key, '/') !== false) {
            $keyArr = explode('/', $key);
            $data = $_POST;
            foreach ($keyArr as $i => $k) {
                if ($k === '') {
                    return $default;
                }
                if (is_array($data)) {
                    if (!isset($data[$k])) {
                        return $default;
                    }
                    $data = $data[$k];
                } else {
                    return $default;
                }
            }
            return $data;
        }
        if (isset($_POST[$key])) {
            return $_POST[$key];
        }
        return $default;
    }

    /**
     * Check is submit POST form
     *
     * @return bool
     */
    public function isPost()
    {
        return $this->getPost('post_form') !== null;
    }

    /**
     * Print image content
     *
     * @param string $img
     */
    public function printImageContent($img)
    {
        if (isset($this->_images[$img])) {
            $imgProp = $this->_images[$img];
            header('Content-Type: ' . $imgProp['type']);
            echo base64_decode($imgProp['base64']);
        }
        else {
            header('HTTP/1.0 404 Not Found');
        }
    }

    /**
     * Retrieve Image URL for SRC
     *
     * @param string $image
     * @return string
     */
    public function getImageSrc($image)
    {
        return "{$_SERVER['PHP_SELF']}?img={$image}";
    }
}

class Tools_Db_Repair_Action
{
    /**
     * Helper object
     *
     * @var Tools_Db_Repair_Helper
     */
    protected $_helper;

    /**
     * Repair Database Tool object
     *
     * @var Tools_Db_Repair_Mysql4
     */
    protected $_resource;

    /**
     * Session array
     *
     * @var array
     */
    protected $_session;

    /**
     * Init class
     */
    public function __construct()
    {
        session_name('mage_db_repair');
        session_start();

        $this->_helper   = new Tools_Db_Repair_Helper();
        $this->_resource = new Tools_Db_Repair_Mysql4();
        $this->_session = &$_SESSION;

        if (!isset($this->_session['step'])) {
            $this->_session['step'] = 1;
        }
    }

    /**
     * Show Configuration Page
     *
     * @return Tools_Db_Repair_Action
     */
    public function configAction()
    {
        $this->_helper->printHtmlHeader();
        $this->_helper->printHtmlFormHead();
        $this->_helper->printHtmlFormHidden();
        $this->_helper->printJsConfiguration();
        $this->_helper->printHtmlContainerHead();
        $this->_helper->printHtmlPageHead('Configuration');

        if (isset($this->_session['errors'])) {
            $this->_helper->printHtmlMessage($this->_session['errors'], 'error');
            unset($this->_session['errors']);
        }

        $this->_helper->printHtmlConfigurationBlock();
        $this->_helper->printHtmlButtonSet(true);

        $this->_helper->printHtmlContainerFoot();
        $this->_helper->printHtmlFormFoot();
        $this->_helper->printHtmlFooter();

        return $this;
    }

    /**
     * Show Confirmation Page
     *
     * @return Tools_Db_Repair_Action
     */
    public function confirmAction($compare = array())
    {
        if (!$compare) {
            $compare = $this->_resource->compareResource();
        }

        $this->_helper->printHtmlHeader();
        $this->_helper->printHtmlFormHead();
        $this->_helper->printHtmlFormHidden();
        $this->_helper->printJsConfirmation();

        $this->_helper->printHtmlContainerHead();
        $this->_helper->printHtmlPageHead('Confirmation');
        $this->_helper->printHtmlNote('Reference and Corruptet databases has one or more installed module defferent version. You are sure continue?');
        $this->_helper->printHtmlFieldsetHead('Different resource version');
        $this->_helper->printHtmlList($compare);
        $this->_helper->printHtmlFieldsetFoot();
        $this->_helper->printHtmlButtonSet(false);
        $this->_helper->printHtmlContainerFoot();

        $this->_helper->printHtmlFormFoot();
        $this->_helper->printHtmlFooter();

        return $this;
    }

    /**
     * Show Repair Database Page
     *
     * @return Tools_Db_Repair_Action
     */
    public function repairAction()
    {
        $actionList = array(
            'charset'    => array(),
            'engine'     => array(),
            'column'     => array(),
            'index'      => array(),
            'table'      => array(),
            'invalid_fk' => array(),
            'constraint' => array()
        );

        $referenceTables = $this->_resource->getTables(Tools_Db_Repair_Mysql4::TYPE_REFERENCE);
        $corruptedTables = $this->_resource->getTables(Tools_Db_Repair_Mysql4::TYPE_CORRUPTED);

        // collect action list
        foreach ($referenceTables as $table => $tableProp) {
            if (!isset($corruptedTables[$table])) {
                $actionList['table'][] = array(
                    'msg'   => sprintf('Add missing table "%s"', $table),
                    'sql'   => $tableProp['create_sql']
                );
            }
            else {
                // check charset
                if ($tableProp['charset'] != $corruptedTables[$table]['charset']) {
                    $actionList['charset'][] = array(
                        'msg'     => sprintf('Change charset on table "%s" from %s to %s',
                            $table,
                            $corruptedTables[$table]['charset'],
                            $tableProp['charset']
                        ),
                        'table'   => $table,
                        'charset' => $tableProp['charset'],
                        'collate' => $tableProp['collate']
                    );
                }

                // check storage
                if ($tableProp['engine'] != $corruptedTables[$table]['engine']) {
                    $actionList['engine'][] = array(
                        'msg'    => sprintf('Change storage engine on table "%s" from %s to %s',
                            $table,
                            $corruptedTables[$table]['engine'],
                            $tableProp['engine']
                        ),
                        'table'  => $table,
                        'engine' => $tableProp['engine']
                    );
                }

                // validate columns
                $fieldList = array_diff_key($tableProp['fields'], $corruptedTables[$table]['fields']);
                if ($fieldList) {
                    $fieldActionList = array();
                    foreach ($fieldList as $fieldKey => $fieldProp) {
                        $afterField = $this->_resource->arrayPrevKey($tableProp['fields'], $fieldKey);
                        $fieldActionList[] = array(
                            'column'    => $fieldKey,
                            'config'    => $fieldProp,
                            'after'     => $afterField
                        );
                    }

                    $actionList['column'][] = array(
                        'msg'    => sprintf('Add missing field(s) "%s" to table "%s"',
                            join(', ', array_keys($fieldList)),
                            $table
                        ),
                        'table'  => $table,
                        'action' => $fieldActionList
                    );
                }

                //validate indexes
                $keyList = array_diff_key($tableProp['keys'], $corruptedTables[$table]['keys']);
                if ($keyList) {
                    $keyActionList = array();
                    foreach ($keyList as $keyProp) {
                        $keyActionList[] = array(
                            'config' => $keyProp
                        );
                    }

                    $actionList['index'][] = array(
                        'msg'    => sprintf('Add missing index(es) "%s" to table "%s"',
                            join(', ', array_keys($keyList)),
                            $table
                        ),
                        'table'  => $table,
                        'action' => $keyActionList
                    );
                }

                foreach ($corruptedTables[$table]['constraints'] as $fk => $fkProp) {
                    if ($fkProp['ref_db']) {
                        $actionList['invalid_fk'][] = array(
                            'msg'    => sprintf('Remove invalid foreign key(s) "%s" from table "%s"',
                                join(', ', array_keys($constraintList)),
                                $table
                            ),
                            'table'      => $table,
                            'constraint' => $fkProp['fk_name']
                        );
                        unset($corruptedTables[$table]['constraints'][$fk]);
                    }
                }

                // validate foreign keys
                $constraintList = array_diff_key($tableProp['constraints'], $corruptedTables[$table]['constraints']);
                if ($constraintList) {
                    $constraintActionList = array();
                    foreach ($constraintList as $constraintConfig) {
                        $constraintActionList[] = array(
                            'config'    => $constraintConfig
                        );
                    }

                    $actionList['constraint'][] = array(
                        'msg'    => sprintf('Add missing foreign key(s) "%s" to table "%s"',
                            join(', ', array_keys($constraintList)),
                            $table
                        ),
                        'table'  => $table,
                        'action' => $constraintActionList
                    );
                }
            }
        }

        $error   = array();
        $success = array();

        $type = Tools_Db_Repair_Mysql4::TYPE_CORRUPTED;

        $this->_resource->start($type);

        foreach ($actionList['charset'] as $actionProp) {
            $this->_resource->begin($type);
            try {
                $this->_resource->changeTableCharset($actionProp['table'], $type, $actionProp['charset'], $actionProp['collate']);
                $this->_resource->commit($type);
                $success[] = $actionProp['msg'];
            }
            catch (Exception $e) {
                $this->_resource->rollback($type);
                $error[] = $e->getMessage();
            }
        }
        foreach ($actionList['engine'] as $actionProp) {
            $this->_resource->begin($type);
            try {
                $this->_resource->changeTableEngine($actionProp['table'], $type, $actionProp['engine']);
                $this->_resource->commit($type);
                $success[] = $actionProp['msg'];
            }
            catch (Exception $e) {
                $this->_resource->rollback($type);
                $error[] = $e->getMessage();
            }
        }

        foreach ($actionList['column'] as $actionProp) {
            $this->_resource->begin($type);
            try {
                foreach ($actionProp['action'] as $action) {
                    $this->_resource->addColumn($actionProp['table'], $action['column'], $action['config'], $type, $action['after']);
                }
                $this->_resource->commit($type);
                $success[] = $actionProp['msg'];
            }
            catch (Exception $e) {
                $this->_resource->rollback($type);
                $error[] = $e->getMessage();
            }
        }

        foreach ($actionList['index'] as $actionProp) {
            $this->_resource->begin($type);
            try {
                foreach ($actionProp['action'] as $action) {
                    $this->_resource->addKey($actionProp['table'], $action['config'], $type);
                }
                $this->_resource->commit($type);
                $success[] = $actionProp['msg'];
            }
            catch (Exception $e) {
                $this->_resource->rollback($type);
                $error[] = $e->getMessage();
            }
        }

        foreach ($actionList['table'] as $actionProp) {
            $this->_resource->begin($type);
            try {
                $this->_resource->sqlQuery($actionProp['sql'], $type);
                $this->_resource->commit($type);
                $success[] = $actionProp['msg'];
            }
            catch (Exception $e) {
                $this->_resource->rollback($type);
                $error[] = $e->getMessage();
            }
        }

        foreach ($actionList['invalid_fk'] as $actionProp) {
            $this->_resource->begin($type);
            try {
                $this->_resource->dropConstraint($actionProp['table'], $actionProp['constraint'], $type);
                $this->_resource->commit($type);
                $success[] = $actionProp['msg'];
            }
            catch (Exception $e) {
                $this->_resource->rollback($type);
                $error[] = $e->getMessage();
            }
        }

        foreach ($actionList['constraint'] as $actionProp) {
            $this->_resource->begin($type);
            try {
                foreach ($actionProp['action'] as $action) {
                    $this->_resource->addConstraint($action['config'], $type);
                }
                $this->_resource->commit($type);
                $success[] = $actionProp['msg'];
            }
            catch (Exception $e) {
                $this->_resource->rollback($type);
                $error[] = $e->getMessage();
            }
        }

        $this->_resource->finish($type);

        $this->_helper->printHtmlHeader();

        $this->_helper->printHtmlContainerHead();
        $this->_helper->printHtmlPageHead('Repair Corrupted Database');
        if (!$error) {
            $this->_helper->printHtmlMessage('Repair finished successfully', 'success');
        } else {
            $this->_helper->printHtmlMessage($error, 'error');
        }
        if ($success) {
            $this->_helper->printHtmlFieldsetHead('Repair Log');
            $this->_helper->printHtmlList($success);
            $this->_helper->printHtmlFieldsetFoot();
        }
        elseif (!$error) {
            $this->_helper->printHtmlNote('Corrupted Database don\'t need changes');
        }
        $this->_helper->printHtmlContainerFoot();

        $this->_helper->printHtmlFooter();

        $this->_session = array();

        return $this;
    }

    /**
     * Images
     *
     * @return Tools_Db_Repair_Action
     */
    public function imageAction()
    {
        $this->_helper->printImageContent($_GET['img']);

        return $this;
    }

    /**
     * Run action
     *
     * @return Tools_Db_Repair_Action
     */
    public function run()
    {
        if (isset($_GET['img'])) {
            return $this->imageAction();
        }

        if ($this->_session['step'] == 1) {
            if ($this->_helper->isPost()) {
                try {
                    $this->_resource->setConnection($this->_helper->getPost('corrupted', array()), Tools_Db_Repair_Mysql4::TYPE_CORRUPTED);
                    $this->_resource->setConnection($this->_helper->getPost('reference', array()), Tools_Db_Repair_Mysql4::TYPE_REFERENCE);
                    if (!$this->_resource->checkInnodbSupport(Tools_Db_Repair_Mysql4::TYPE_CORRUPTED)) {
                        throw new Exception('Corrupted database not supported InnoDB storage');
                    }

                    $this->_session['db_config_corrupted'] = $this->_helper->getPost('corrupted', array());
                    $this->_session['db_config_reference'] = $this->_helper->getPost('reference', array());

                    $compare = $this->_resource->compareResource();

                    if ($compare) {
                        $this->_session['step'] = 2;
                        return $this->confirmAction();
                    }
                    else {
                        $this->_session['step'] = 3;
                        header('Location: ' . $_SERVER['PHP_SELF']);
                        return $this;
                    }
                }
                catch (Exception $e) {
                    $this->_session['errors'] = array($e->getMessage());
                    $this->configAction();
                    return $this;
                }
            }
            return $this->configAction();
        }
        elseif ($this->_session['step'] == 2) {
            try {
                $this->_resource->setConnection($this->_session['db_config_corrupted'], Tools_Db_Repair_Mysql4::TYPE_CORRUPTED);
                $this->_resource->setConnection($this->_session['db_config_reference'], Tools_Db_Repair_Mysql4::TYPE_REFERENCE);
            }
            catch (Exception $e) {
                $this->_session['step'] = 1;
                header('Location: ' . $_SERVER['PHP_SELF']);
                return $this;
            }
            if ($this->_helper->isPost()) {
                $this->_session['step'] = 3;
                header('Location: ' . $_SERVER['PHP_SELF']);
                return $this;
            }
            else {
                return $this->confirmAction();
            }
        }
        elseif ($this->_session['step'] == 3) {
            try {
                $this->_resource->setConnection($this->_session['db_config_corrupted'], Tools_Db_Repair_Mysql4::TYPE_CORRUPTED);
                $this->_resource->setConnection($this->_session['db_config_reference'], Tools_Db_Repair_Mysql4::TYPE_REFERENCE);
            }
            catch (Exception $e) {
                $this->_session['step'] = 1;
                header('Location: ' . $_SERVER['PHP_SELF']);
                return $this;
            }
            return $this->repairAction();
        }
        return $this;
    }
}

@set_time_limit(0);

$repairDb = new Tools_Db_Repair_Action();
$repairDb->run();
