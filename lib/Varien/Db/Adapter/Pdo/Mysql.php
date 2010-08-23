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
 * @category   Varien
 * @package    Varien_Db
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Mysql PDO DB adapter
 */
class Varien_Db_Adapter_Pdo_Mysql extends Zend_Db_Adapter_Pdo_Mysql implements Varien_Db_Adapter_Interface
{
    const DEBUG_CONNECT         = 0;
    const DEBUG_TRANSACTION     = 1;
    const DEBUG_QUERY           = 2;

    const TIMESTAMP_FORMAT      = 'Y-m-d H:i:s';
    const DATE_FORMAT           = 'Y-m-d';

    const DDL_DESCRIBE          = 1;
    const DDL_CREATE            = 2;
    const DDL_INDEX             = 3;
    const DDL_FOREIGN_KEY       = 4;
    const DDL_CACHE_PREFIX      = 'DB_PDO_MYSQL_DDL';
    const DDL_CACHE_TAG         = 'DB_PDO_MYSQL_DDL';

    /**
     * Current Transaction Level
     *
     * @var int
     */
    protected $_transactionLevel    = 0;

    /**
     * Set attribute to connection flag
     *
     * @var bool
     */
    protected $_connectionFlagsSet  = false;

    /**
     * Tables DDL cache
     *
     * @var array
     */
    protected $_ddlCache            = array();

    /**
     * SQL bind params
     *
     * @var array
     */
    protected $_bindParams          = array();

    /**
     * Autoincrement for bind value
     *
     * @var int
     */
    protected $_bindIncrement       = 0;

    /**
     * Write SQL debug data to file
     *
     * @var bool
     */
    protected $_debug               = true;

    /**
     * Minimum query duration time to be logged
     *
     * @var float
     */
    protected $_logQueryTime        = 0.05;

    /**
     * Log all queries (ignored minimum query duration time)
     *
     * @var bool
     */
    protected $_logAllQueries       = true;

    /**
     * Add to log call stack data (backtrace)
     *
     * @var bool
     */
    protected $_logCallStack        = false;

    /**
     * Path to SQL debug data log
     *
     * @var string
     */
    protected $_debugFile           = 'var/debug/pdo_mysql.log';

    /**
     * Io File Adapter
     *
     * @var Varien_Io_File
     */
    protected $_debugIoAdapter;

    /**
     * Debug timer start value
     *
     * @var float
     */
    protected $_debugTimer          = 0;

    /**
     * Cache frontend adapter instance
     *
     * @var Zend_Cache_Core
     */
    protected $_cacheAdapter;

    /**
     * DDL cache allowing flag
     * @var bool
     */
    protected $_isDdlCacheAllowed = true;

    /**
     * MySQL column - Table DDL type pairs
     *
     * @var array
     */
    protected $_ddlColumnTypes      = array(
        Varien_Db_Ddl_Table::TYPE_BOOLEAN       => 'bool',
        Varien_Db_Ddl_Table::TYPE_SMALLINT      => 'smallint',
        Varien_Db_Ddl_Table::TYPE_INTEGER       => 'int',
        Varien_Db_Ddl_Table::TYPE_BIGINT        => 'bigint',
        Varien_Db_Ddl_Table::TYPE_FLOAT         => 'float',
        Varien_Db_Ddl_Table::TYPE_DECIMAL       => 'decimal',
        Varien_Db_Ddl_Table::TYPE_NUMERIC       => 'decimal',
        Varien_Db_Ddl_Table::TYPE_DATE          => 'date',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP     => 'timestamp',
        Varien_Db_Ddl_Table::TYPE_TEXT          => 'text',
        Varien_Db_Ddl_Table::TYPE_BLOB          => 'blob',
    );

    /**
     * Allowed interval units array
     *
     * @var array
     */
    protected $_intervalUnits = array(
        self::INTERVAL_YEAR     => 'YEAR',
        self::INTERVAL_MONTH    => 'MONTH',
        self::INTERVAL_DAY      => 'DAY',
        self::INTERVAL_HOUR     => 'HOUR',
        self::INTERVAL_MINUTE   => 'MINUTE',
        self::INTERVAL_SECOND   => 'SECOND',
    );

    /**
     * Begin new DB transaction for connection
     *
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function beginTransaction()
    {
        if ($this->_transactionLevel===0) {
            $this->_debugTimer();
            parent::beginTransaction();
            $this->_debugStat(self::DEBUG_TRANSACTION, 'BEGIN');
        }
        $this->_transactionLevel++;
        return $this;
    }

    /**
     * Commit DB transaction
     *
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function commit()
    {
        if ($this->_transactionLevel===1) {
            $this->_debugTimer();
            parent::commit();
            $this->_debugStat(self::DEBUG_TRANSACTION, 'COMMIT');
        }
        $this->_transactionLevel--;
        return $this;
    }

    /**
     * Rollback DB transaction
     *
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function rollback()
    {
        if ($this->_transactionLevel===1) {
            $this->_debugTimer();
            parent::rollback();
            $this->_debugStat(self::DEBUG_TRANSACTION, 'ROLLBACK');
        }
        $this->_transactionLevel--;
        return $this;
    }

    /**
     * Get adapter transaction level state. Return 0 if all transactions are complete
     *
     * @return int
     */
    public function getTransactionLevel()
    {
        return $this->_transactionLevel;
    }

    /**
     * Convert date to DB format
     *
     * @param   mixed $date
     * @return  string
     */
    public function convertDate($date)
    {
        return $this->formatDate($date, false);
    }

    /**
     * Convert date and time to DB format
     *
     * @param   mixed $date
     * @return  string
     */
    public function convertDateTime($datetime)
    {
        return $this->formatDate($datetime, true);
    }

    /**
     * Creates a PDO object and connects to the database.
     *
     */
    protected function _connect()
    {
        if ($this->_connection) {
            return;
        }

        if (!extension_loaded('pdo_mysql')) {
            throw new Zend_Db_Adapter_Exception('pdo_mysql extension is not installed');
        }

        if (strpos($this->_config['host'], '/')!==false) {
            $this->_config['unix_socket'] = $this->_config['host'];
            unset($this->_config['host']);
        } else if (strpos($this->_config['host'], ':')!==false) {
            list($this->_config['host'], $this->_config['port']) = explode(':', $this->_config['host']);
        }

        $this->_debugTimer();
        parent::_connect();
        $this->_debugStat(self::DEBUG_CONNECT, '');

        /** @link http://bugs.mysql.com/bug.php?id=18551 */
        $this->_connection->query("SET SQL_MODE=''");

        if (!$this->_connectionFlagsSet) {
            $this->_connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
            $this->_connection->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
            $this->_connectionFlagsSet = true;
        }
    }

    /**
     * Run RAW Query
     *
     * @param string $sql
     * @return Zend_Db_Statement_Interface
     */
    public function raw_query($sql)
    {
        do {
            $retry = false;
            $tries = 0;
            try {
                $result = $this->getConnection()->query($sql);
            } catch (PDOException $e) {
                if ($e->getMessage()=='SQLSTATE[HY000]: General error: 2013 Lost connection to MySQL server during query') {
                    $retry = true;
                } else {
                    throw $e;
                }
                $tries++;
            }
        } while ($retry && $tries<10);

        return $result;
    }

    /**
     * Run RAW query and Fetch First row
     *
     * @param string $sql
     * @param string|int $field
     * @return mixed
     */
    public function raw_fetchRow($sql, $field=null)
    {
        if (!$result = $this->raw_query($sql)) {
            return false;
        }
        if (!$row = $result->fetch(PDO::FETCH_ASSOC)) {
            return false;
        }
        if (empty($field)) {
            return $row;
        } else {
            return isset($row[$field]) ? $row[$field] : false;
        }
    }

    /**
     * Special handling for PDO query().
     * All bind parameter names must begin with ':'
     *
     * @param string|Zend_Db_Select $sql The SQL statement with placeholders.
     * @param array $bind An array of data to bind to the placeholders.
     * @return Zend_Db_Pdo_Statement
     * @throws Zend_Db_Adapter_Exception To re-throw PDOException.
     */
    public function query($sql, $bind = array())
    {
        $this->_debugTimer();
        try {
            $sql = (string)$sql;
            if (strpos($sql, ':') !== false || strpos($sql, '?') !== false) {
                $this->_bindParams = $bind;
                $sql = preg_replace_callback('#(([\'"])((\\2)|((.*?[^\\\\])\\2)))#', array($this, 'proccessBindCallback'), $sql);
                Varien_Exception::processPcreError();
                $bind = $this->_bindParams;
            }

            $result = parent::query($sql, $bind);
        }
        catch (Exception $e) {
            $this->_debugStat(self::DEBUG_QUERY, $sql, $bind);
            $this->_debugException($e);
        }
        $this->_debugStat(self::DEBUG_QUERY, $sql, $bind, $result);
        return $result;
    }

    /**
     * Callback function for prepare Query Bind RegExp
     *
     * @param array $matches
     * @return string
     */
    public function proccessBindCallback($matches)
    {
        if (isset($matches[6]) && (
            strpos($matches[6], "'") !== false ||
            strpos($matches[6], ":") !== false ||
            strpos($matches[6], "?") !== false)) {
            $bindName = ':_mage_bind_var_' . ( ++ $this->_bindIncrement );
            $this->_bindParams[$bindName] = $this->_unQuote($matches[6]);
            return ' ' . $bindName;
        }
        return $matches[0];
    }

    /**
     * Unquote raw string (use for auto-bind)
     *
     * @param string $string
     * @return string
     */
    protected function _unQuote($string)
    {
        $translate = array(
            "\\000" => "\000",
            "\\n"   => "\n",
            "\\r"   => "\r",
            "\\\\"  => "\\",
            "\'"    => "'",
            "\\\""  => "\"",
            "\\032" => "\032"
        );
        return strtr($string, $translate);
    }

    /**
     * Executes a SQL statement(s)
     *
     * @param string $sql
     * @throws Zend_Db_Exception
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function multiQuery($sql)
    {
        return $this->multi_query($sql);
    }

    /**
     * Run Multi Query
     *
     * @param string $sql
     * @return array
     */
    public function multi_query($sql)
    {
        ##$result = $this->raw_query($sql);

        #$this->beginTransaction();
        try {
            $stmts = $this->_splitMultiQuery($sql);
            $result = array();
            foreach ($stmts as $stmt) {
                $result[] = $this->raw_query($stmt);
            }
            #$this->commit();
        } catch (Exception $e) {
            #$this->rollback();
            throw $e;
        }

        $this->resetDdlCache();

        return $result;
    }

    /**
     * Split multi statement query
     *
     * @param $sql string
     * @return array
     */
    protected function _splitMultiQuery($sql)
    {
        $parts = preg_split('#(;|\'|"|\\\\|//|--|\n|/\*|\*/)#', $sql, null, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        $q = false;
        $c = false;
        $stmts = array();
        $s = '';

        foreach ($parts as $i=>$part) {
            // strings
            if (($part==="'" || $part==='"') && ($i===0 || $parts[$i-1]!=='\\')) {
                if ($q===false) {
                    $q = $part;
                } else if ($q===$part) {
                    $q = false;
                }
            }

            // single line comments
            if (($part==='//' || $part==='--') && ($i===0 || $parts[$i-1]==="\n")) {
                $c = $part;
            } else if ($part==="\n" && ($c==='//' || $c==='--')) {
                $c = false;
            }

            // multi line comments
            if ($part==='/*' && $c===false) {
                $c = '/*';
            } else if ($part==='*/' && $c==='/*') {
                $c = false;
            }

            // statements
            if ($part===';' && $q===false && $c===false) {
                if (trim($s)!=='') {
                    $stmts[] = trim($s);
                    $s = '';
                }
            } else {
                $s .= $part;
            }
        }
        if (trim($s)!=='') {
            $stmts[] = trim($s);
        }

        return $stmts;
    }

    /**
     * Drop the Foreign Key from table
     *
     * @param string $tableName
     * @param string $fkName
     * @param string $shemaName
     * @return Varien_Db_Adapter_Interface
     */
    public function dropForeignKey($tableName, $fkName, $schemaName = null)
    {
        $foreignKeys = $this->getForeignKeys($tableName, $schemaName);
        if (isset($foreignKeys[strtoupper($fkName)])) {
            $sql = sprintf('ALTER TABLE %s DROP FOREIGN KEY %s',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
            $this->quoteIdentifier($foreignKeys[strtoupper($fkName)]['FK_NAME']));
            $this->resetDdlCache($tableName, $schemaName);
            $this->raw_query($sql);
        }
        return $this;
    }

    /**
     * Delete index from a table if it exists
     *
     * @deprecated since 1.4.0.1
     * @param string $tableName
     * @param string $keyName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Interface
     */
    public function dropKey($tableName, $keyName, $schemaName = null)
    {
        return $this->dropIndex($tableName, $keyName, $schemaName);
    }

    /**
     * Prepare table before add constraint foreign key
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $refTableName
     * @param string $refColumnName
     * @param string $onDelete
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function purgeOrphanRecords($tableName, $columnName, $refTableName, $refColumnName, $onDelete = 'cascade')
    {
        if (strtoupper($onDelete) == 'CASCADE'
            || strtoupper($onDelete) == 'RESTRICT') {
            $sql = "DELETE `p`.* FROM `{$tableName}` AS `p`"
                . " LEFT JOIN `{$refTableName}` AS `r`"
                . " ON `p`.`{$columnName}` = `r`.`{$refColumnName}`"
                . " WHERE `r`.`{$refColumnName}` IS NULL";
            $this->raw_query($sql);
        }
        else if (strtoupper($onDelete) == 'SET NULL') {
            $sql = "UPDATE `{$tableName}` AS `p`"
                . " LEFT JOIN `{$refTableName}` AS `r`"
                . " ON `p`.`{$columnName}` = `r`.`{$refColumnName}`"
                . " SET `p`.`{$columnName}`=NULL"
                . " WHERE `r`.`{$refColumnName}` IS NULL";
            $this->raw_query($sql);
        }

        return $this;
    }

    /**
     * Add foreign key to table. If FK with same name exist - it will be deleted
     *
     * @deprecated since 1.4.0.1
     * @param string $fkName foreign key name
     * @param string $tableName main table name
     * @param string $keyName main table field name
     * @param string $refTableName refered table name
     * @param string $refKeyName refered table field name
     * @param string $onUpdate on update statement
     * @param string $onDelete on delete statement
     * @param bool $purge
     * @return mixed
     */
    public function addConstraint($fkName, $tableName, $columnName,
        $refTableName, $refColumnName, $onDelete = 'cascade', $onUpdate = 'cascade', $purge = false)
    {
        return $this->addForeignKey($fkName, $tableName, $columnName, $refTableName, $refColumnName,
            $onDelete, $onUpdate, $purge);
    }

    /**
     * Check does table column exist
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $schemaName
     * @return boolean
     */
    public function tableColumnExists($tableName, $columnName, $schemaName = null)
    {
        $describe = $this->describeTable($tableName, $schemaName);
        foreach ($describe as $column) {
            if ($column['COLUMN_NAME'] == $columnName) {
                return true;
            }
        }
        return false;
    }

    /**
     * Add new column to table
     *
     * @param   string $tableName
     * @param   string $columnName
     * @param   string|array $definition
     * @param   string $schemaName
     * @return  int|boolean
     */
    public function addColumn($tableName, $columnName, $definition, $schemaName = null)
    {
        if ($this->tableColumnExists($tableName, $columnName, $schemaName)) {
            return true;
        }

        if (is_array($definition)) {
            $definition = $this->_getColumnDefinition($definition);
        }

        $sql = sprintf('ALTER TABLE %s ADD COLUMN %s %s',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
            $this->quoteIdentifier($columnName),
            $definition
        );

        $result = $this->raw_query($sql);
        $this->resetDdlCache($tableName, $schemaName);

        return $result;
    }

    /**
     * Delete table column
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $shemaName
     * @return bool
     */
    public function dropColumn($tableName, $columnName, $schemaName = null)
    {
        if (!$this->tableColumnExists($tableName, $columnName, $schemaName)) {
            return true;
        }

        $alterDrop = array();

        $foreignKeys = $this->getForeignKeys($tableName, $schemaName);
        foreach ($foreignKeys as $fkProp) {
            if ($fkProp['COLUMN_NAME'] == $columnName) {
                $alterDrop[] = sprintf('DROP FOREIGN KEY %s', $this->quoteIdentifier($fkProp['FK_NAME']));
            }
        }

        $alterDrop[] = sprintf('DROP COLUMN %s', $this->quoteIdentifier($columnName));
        $sql = sprintf('ALTER TABLE %s %s',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
            join(', ', $alterDrop));

        $this->resetDdlCache($tableName, $schemaName);
        return $this->raw_query($sql);
    }

    /**
     * Change the column name and definition
     *
     * For change definition of column - use modifyColumn

     *
     * @param string $tableName
     * @param string $oldColumnName
     * @param string $newColumnName
     * @param array|string $definition
     * @param boolean $flushData        flush table statistic
     * @param string $schemaName
     * @return Varien_Db_Adapter_Interface



     */
    public function changeColumn($tableName, $oldColumnName, $newColumnName, $definition, $flushData = false,
        $schemaName = null)
    {
        if (!$this->tableColumnExists($tableName, $oldColumnName, $schemaName)) {
            throw new Exception(sprintf('Column "%s" does not exists on table "%s"', $oldColumnName, $tableName));
        }

        if (is_array($definition)) {
            $definition = $this->_getColumnDefinition($definition);
        }

        $sql = sprintf('ALTER TABLE %s CHANGE COLUMN %s %s %s',
            $this->quoteIdentifier($tableName),
            $this->quoteIdentifier($oldColumnName),
            $this->quoteIdentifier($newColumnName),
            $definition);

        $result = $this->raw_query($sql);
        if ($flushData) {

            $this->showTableStatus($tableName, $schemaName);
        }

        $this->resetDdlCache($tableName, $schemaName);
        return $result;
    }

    /**
     * Modify the column definition
     *
     * @param string $tableName
     * @param string $columnName
     * @param array|string $definition
     * @param boolean $flushData
     * @param string $schemaName
     * @return Varien_Db_Adapter_Interface
     */
    public function modifyColumn($tableName, $columnName, $definition, $flushData = false, $schemaName = null)
    {
        if (!$this->tableColumnExists($tableName, $columnName, $schemaName)) {
            throw new Exception(sprintf('Column "%s" does not exists on table "%s"', $columnName, $tableName));
        }

        if (is_array($definition)) {
            $definition = $this->_getColumnDefinition($definition);
        }

        $sql = sprintf('ALTER TABLE %s MODIFY COLUMN %s %s',
            $this->quoteIdentifier($tableName),
            $this->quoteIdentifier($columnName),
            $definition);
        $result = $this->raw_query($sql);
        if ($flushData) {

            $this->showTableStatus($tableName, $schemaName);
        }

        $this->resetDdlCache($tableName, $schemaName);
        return $result;
    }

    /**
     * Show table status
     *
     * @param string $tableName
     * @param string $schemaName
     * @return array|false
     */
    public function showTableStatus($tableName, $schemaName = null)
    {
        $fromDbName = null;
        if (!is_null($schemaName)) {
            $fromDbName = sprintf(' FROM %s', $this->quoteIdentifier($schemaName));
        }
        $query = sprintf('SHOW TABLE STATUS%s LIKE %s', $fromDbName, $this->quote($tableName));

        return $this->raw_fetchRow($query);
    }

    /**
     * Retrieve table index key list
     *
     * @deprecated use getIndexList(
     * @param string $tableName
     * @param string $schemaName
     * @return array
     */
    public function getKeyList($tableName, $schemaName = null)
    {
        $keyList   = array();
        $indexList = $this->getIndexList($tableName, $schemaName);

        foreach ($indexList as $indexProp) {
            $keyList[$indexProp['KEY_NAME']] = $indexProp['COLUMNS_LIST'];
        }

        return $keyList;
    }

    /**
     * Retrieve Create Table SQL
     *
     * @param string $tableName
     * @param string $schemaName
     * @return string
     */
    public function getCreateTable($tableName, $schemaName = null)
    {
        $cacheKey = $this->_getTableName($tableName, $schemaName);
        $ddl = $this->loadDdlCache($cacheKey, self::DDL_CREATE);
        if ($ddl === false) {
            $sql = sprintf('SHOW CREATE TABLE %s', $this->quoteIdentifier($tableName));
            $ddl = $this->raw_fetchRow($sql, 'Create Table');
            $this->saveDdlCache($cacheKey, self::DDL_CREATE, $ddl);
        }
        return $ddl;
    }

    /**
     * Retrieve the foreign keys descriptions for a table.
     *
     * The return value is an associative array keyed by the UPPERCASE foreign key,
     * as returned by the RDBMS.
     *
     * The value of each array element is an associative array
     * with the following keys:
     *
     * FK_NAME          => string; original foreign key name
     * SCHEMA_NAME      => string; name of database or schema
     * TABLE_NAME       => string;
     * COLUMN_NAME      => string; column name
     * REF_SCHEMA_NAME  => string; name of reference database or schema
     * REF_TABLE_NAME   => string; reference table name
     * REF_COLUMN_NAME  => string; reference column name
     * ON_DELETE        => string; action type on delete row
     * ON_UPDATE        => string; action type on update row
     *
     * @param string $tableName
     * @param string $schemaName
     * @return array
     */
    public function getForeignKeys($tableName, $schemaName = null)
    {
        $cacheKey = $this->_getTableName($tableName, $schemaName);
        $ddl = $this->loadDdlCache($cacheKey, self::DDL_FOREIGN_KEY);
        if ($ddl === false) {
            $ddl = array();
            $createSql = $this->getCreateTable($tableName, $schemaName);

            // collect CONSTRAINT
            $regExp  = '#,\s+CONSTRAINT `([^`]*)` FOREIGN KEY \(`([^`]*)`\) '
                . 'REFERENCES (`[^`]*\.)?`([^`]*)` \(`([^`]*)`\)'
                . '( ON DELETE (RESTRICT|CASCADE|SET NULL|NO ACTION))?'
                . '( ON UPDATE (RESTRICT|CASCADE|SET NULL|NO ACTION))?#';
            $matches = array();
            preg_match_all($regExp, $createSql, $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                $ddl[strtoupper($match[1])] = array(
                    'FK_NAME'           => $match[1],
                    'SCHEMA_NAME'       => $schemaName,
                    'TABLE_NAME'        => $tableName,
                    'COLUMN_NAME'       => $match[2],
                    'REF_SHEMA_NAME'    => isset($match[3]) ? $match[3] : $schemaName,
                    'REF_TABLE_NAME'    => $match[4],
                    'REF_COLUMN_NAME'   => $match[5],
                    'ON_DELETE'         => isset($match[6]) ? $match[7] : '',
                    'ON_UPDATE'         => isset($match[8]) ? $match[9] : ''
                );
            }

            $this->saveDdlCache($cacheKey, self::DDL_FOREIGN_KEY, $ddl);
        }

        return $ddl;
    }

    /**
     * Retrieve table index information
     *
     * The return value is an associative array keyed by the UPPERCASE index key,
     * as returned by the RDBMS.
     *
     * The value of each array element is an associative array
     * with the following keys:
     *
     * SCHEMA_NAME      => string; name of database or schema
     * TABLE_NAME       => string; name of the table
     * KEY_NAME         => string; the original index name
     * COLUMNS_LIST     => array; array of index column names
     * INDEX_TYPE       => string; create index type
     * INDEX_METHOD     => string; index method using
     * type             => string; see INDEX_TYPE
     * fields           => array; see COLUMNS_LIST
     *
     * @param string $tableName
     * @param string $schemaName
     * @return array
     */
    public function getIndexList($tableName, $schemaName = null)
    {
        $cacheKey = $this->_getTableName($tableName, $schemaName);
        $ddl = $this->loadDdlCache($cacheKey, self::DDL_INDEX);
        if ($ddl === false) {
            $ddl = array();

            $sql = sprintf('SHOW INDEX FROM %s',
                $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)));
            foreach ($this->fetchAll($sql) as $row) {
                $fieldKeyName   = 'Key_name';
                $fieldNonUnique = 'Non_unique';
                $fieldColumn    = 'Column_name';
                $fieldIndexType = 'Index_type';

                if ($row[$fieldKeyName] == 'PRIMARY') {
                    $indexType  = 'primary';
                }
                else if ($row[$fieldNonUnique] == 0) {
                    $indexType  = 'unique';
                }
                else if ($row[$fieldIndexType] == 'FULLTEXT') {
                    $indexType  = 'fulltext';
                }
                else {
                    $indexType  = 'index';
                }

                $upperKeyName = strtoupper($row[$fieldKeyName]);
                if (isset($ddl[$upperKeyName])) {
                    $ddl[$upperKeyName]['fields'][] = $row[$fieldColumn]; // for compatible
                    $ddl[$upperKeyName]['COLUMNS_LIST'][] = $row[$fieldColumn];
                }
                else {
                    $ddl[$upperKeyName] = array(
                        'SCHEMA_NAME'   => $schemaName,
                        'TABLE_NAME'    => $tableName,
                        'KEY_NAME'      => $row[$fieldKeyName],
                        'COLUMNS_LIST'  => array($row[$fieldColumn]),
                        'INDEX_TYPE'    => strtoupper($indexType),
                        'INDEX_METHOD'  => $row[$fieldIndexType],
                        'type'          => $indexType, // for compatible
                        'fields'        => array($row[$fieldColumn]) // for compatible
                    );
                }
            }
            $this->saveDdlCache($cacheKey, self::DDL_INDEX, $ddl);
        }
        return $ddl;
    }

    /**
     * Add Index Key
     *
     * @param string $tableName
     * @param string $indexName
     * @param string|array $fields
     * @param string $indexType
     * @param string $schemaName
     * @return Varien_Db_Adapter_Pdo_Mysql

     */
    public function addKey($tableName, $indexName, $fields, $indexType = 'index', $schemaName = null)
    {
        return $this->addIndex($tableName, $indexName, $fields, $indexType, $schemaName);
    }

    /**
     * Remove duplicate entry for create key
     *
     * @param string $table
     * @param array $fields
     * @param array $ids
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    protected function _removeDuplicateEntry($table, $fields, $ids)
    {
        $where = array();
        $i = 0;
        foreach ($fields as $field) {
            $where[] = $this->quoteInto($field . '=?', $ids[$i]);
            $i ++;
        }

        if (!$where) {
            return $this;
        }
        $whereCond = join(' AND ', $where);
        $sql = sprintf('SELECT COUNT(*) as `cnt` FROM `%s` WHERE %s', $table, $whereCond);

        $cnt = $this->raw_fetchRow($sql, 'cnt');
        if ($cnt > 1) {
            $sql = sprintf('DELETE FROM `%s` WHERE %s LIMIT %d',
                $table,
                $whereCond,
                $cnt - 1
            );
            $this->raw_query($sql);
        }

        return $this;
    }

    /**
     * Creates and returns a new Zend_Db_Select object for this adapter.
     *
     * @return Varien_Db_Select
     */
    public function select()
    {
        return new Varien_Db_Select($this);
    }

    /**
     * Start debug timer
     *
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    protected function _debugTimer()
    {
        if ($this->_debug) {
            $this->_debugTimer = microtime(true);
        }
        return $this;
    }

    /**
     * Logging debug information
     *
     * @param int $type
     * @param string $sql
     * @param array $bind
     * @param Zend_Db_Statement_Pdo $result
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    protected function _debugStat($type, $sql, $bind = array(), $result = null)
    {
        if (!$this->_debug) {
            return $this;
        }

        $code = '## ' . getmypid() . ' ## ';
        $nl   = "\n";
        $time = sprintf('%.4f', microtime(true) - $this->_debugTimer);

        if (!$this->_logAllQueries && $time < $this->_logQueryTime) {
            return $this;
        }
        switch ($type) {
            case self::DEBUG_CONNECT:
                $code .= 'CONNECT' . $nl;
                break;
            case self::DEBUG_TRANSACTION:
                $code .= 'TRANSACTION ' . $sql . $nl;
                break;
            case self::DEBUG_QUERY:
                $code .= 'QUERY' . $nl;
                $code .= 'SQL: ' . $sql . $nl;
                if ($bind) {
                    $code .= 'BIND: ' . var_export($bind, true) . $nl;
                }
                if ($result instanceof Zend_Db_Statement_Pdo) {
                    $code .= 'AFF: ' . $result->rowCount() . $nl;
                }
                break;
        }
        $code .= 'TIME: ' . $time . $nl;

        if ($this->_logCallStack) {
            $code .= 'TRACE: ' . Varien_Debug::backtrace(true, false) . $nl;
        }

        $code .= $nl;

        $this->_debugWriteToFile($code);

        return $this;
    }

    /**
     * Write exception and thow
     *
     * @param Exception $e
     * @throws Exception
     */
    protected function _debugException(Exception $e)
    {
        if (!$this->_debug) {
            throw $e;
        }

        $nl   = "\n";
        $code = 'EXCEPTION ' . $nl . $e . $nl . $nl;
        $this->_debugWriteToFile($code);

        throw $e;
    }

    /**
     * Debug write to file process
     *
     * @param string $str
     */
    protected function _debugWriteToFile($str)
    {
        if (!$this->_debugIoAdapter) {
            $this->_debugIoAdapter = new Varien_Io_File();
            $dir = $this->_debugIoAdapter->dirname($this->_debugFile);
            $this->_debugIoAdapter->checkAndCreateFolder($dir);
            $this->_debugIoAdapter->open(array('path' => $dir));
            $this->_debugFile = basename($this->_debugFile);
        }

        $this->_debugIoAdapter->streamOpen($this->_debugFile, 'a');
        $this->_debugIoAdapter->streamLock();
        $this->_debugIoAdapter->streamWrite($str);
        $this->_debugIoAdapter->streamUnlock();
        $this->_debugIoAdapter->streamClose();
    }

    /**
     * Quotes a value and places into a piece of text at a placeholder.
     *
     * Method revrited for handle empty arrays in value param
     *
     * @param string  $text  The text with a placeholder.
     * @param mixed   $value The value to quote.
     * @param string  $type  OPTIONAL SQL datatype
     * @param integer $count OPTIONAL count of placeholders to replace
     * @return string An SQL-safe quoted value placed into the orignal text.
     */
    public function quoteInto($text, $value, $type = null, $count = null)
    {
        if (is_array($value) && empty($value)) {
            $value = new Zend_Db_Expr('NULL');
        }
        return parent::quoteInto($text, $value, $type, $count);
    }

    /**
     * Retrieve ddl cache name
     *
     * @param string $tableName
     * @param string $schemaName
     */
    protected function _getTableName($tableName, $schemaName = null)
    {
        return ($schemaName ? $schemaName . '.' : '') . $tableName;
    }

    /**
     * Retrieve Id for cache
     *
     * @param string $tableKey
     * @param int $ddlType
     * @return string
     */
    protected function _getCacheId($tableKey, $ddlType)
    {
        return sprintf('%s_%s_%s', self::DDL_CACHE_PREFIX, $tableKey, $ddlType);
    }

    /**
     * Load DDL data from cache
     * Return false if cache does not exists
     *
     * @param string $tableCacheKey the table cache key
     * @param int $ddlType          the DDL constant
     * @return string|array|int|false
     */
    public function loadDdlCache($tableCacheKey, $ddlType)
    {
        if (!$this->_isDdlCacheAllowed) {
            return false;
        }
        if (isset($this->_ddlCache[$ddlType][$tableCacheKey])) {
            return $this->_ddlCache[$ddlType][$tableCacheKey];
        }

        if ($this->_cacheAdapter instanceof Zend_Cache_Core) {
            $cacheId = $this->_getCacheId($tableCacheKey, $ddlType);
            $data = $this->_cacheAdapter->load($cacheId);
            if ($data !== false) {
                $data = unserialize($data);
                $this->_ddlCache[$ddlType][$tableCacheKey] = $data;
            }
            return $data;
        }

        return false;
    }

    /**
     * Save DDL data into cache
     *
     * @param string $tableCacheKey
     * @param int $ddlType
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function saveDdlCache($tableCacheKey, $ddlType, $data)
    {
        if (!$this->_isDdlCacheAllowed) {
            return $this;
        }
        $this->_ddlCache[$ddlType][$tableCacheKey] = $data;

        if ($this->_cacheAdapter instanceof Zend_Cache_Core) {
            $cacheId = $this->_getCacheId($tableCacheKey, $ddlType);
            $data = serialize($data);
            $this->_cacheAdapter->save($data, $cacheId, array(self::DDL_CACHE_TAG));
        }

        return $this;
    }

    /**
     * Reset cached DDL data from cache
     * if table name is null - reset all cached DDL data
     *
     * @param string $tableName
     * @param string $schemaName OPTIONAL
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function resetDdlCache($tableName = null, $schemaName = null)
    {
        if (!$this->_isDdlCacheAllowed) {
            return $this;
        }
        if (is_null($tableName)) {
            $this->_ddlCache = array();
            if ($this->_cacheAdapter instanceof Zend_Cache_Core) {
                $this->_cacheAdapter->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array(self::DDL_CACHE_TAG));
            }
        } else {
            $cacheKey = $this->_getTableName($tableName, $schemaName);

            $ddlTypes = array(self::DDL_DESCRIBE, self::DDL_CREATE, self::DDL_INDEX, self::DDL_FOREIGN_KEY);
            foreach ($ddlTypes as $ddlType) {
                unset($this->_ddlCache[$ddlType][$cacheKey]);
            }

            if ($this->_cacheAdapter instanceof Zend_Cache_Core) {
                foreach ($ddlTypes as $ddlType) {
                    $cacheId = $this->_getCacheId($cacheKey, $ddlType);
                    $this->_cacheAdapter->remove($cacheId);
                }
            }
        }

        return $this;
    }

    /**
     * Disallow DDL caching
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function disallowDdlCache()
    {
        $this->_isDdlCacheAllowed = false;
        return $this;
    }

    /**
     * Allow DDL caching
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function allowDdlCache()
    {
        $this->_isDdlCacheAllowed = true;
        return $this;
    }

    /**
     * Returns the column descriptions for a table.
     *
     * The return value is an associative array keyed by the column name,
     * as returned by the RDBMS.
     *
     * The value of each array element is an associative array
     * with the following keys:
     *
     * SCHEMA_NAME      => string; name of database or schema
     * TABLE_NAME       => string;
     * COLUMN_NAME      => string; column name
     * COLUMN_POSITION  => number; ordinal position of column in table
     * DATA_TYPE        => string; SQL datatype name of column
     * DEFAULT          => string; default expression of column, null if none
     * NULLABLE         => boolean; true if column can have nulls
     * LENGTH           => number; length of CHAR/VARCHAR
     * SCALE            => number; scale of NUMERIC/DECIMAL
     * PRECISION        => number; precision of NUMERIC/DECIMAL
     * UNSIGNED         => boolean; unsigned property of an integer type
     * PRIMARY          => boolean; true if column is part of the primary key
     * PRIMARY_POSITION => integer; position of column in primary key
     * IDENTITY         => integer; true if column is auto-generated with unique values
     *
     * @param string $tableName
     * @param string $schemaName OPTIONAL
     * @return array
     */
    public function describeTable($tableName, $schemaName = null)
    {
        $cacheKey = $this->_getTableName($tableName, $schemaName);
        $ddl = $this->loadDdlCache($cacheKey, self::DDL_DESCRIBE);
        if ($ddl === false) {
            $ddl = parent::describeTable($tableName, $schemaName);
            $this->saveDdlCache($cacheKey, self::DDL_DESCRIBE, $ddl);
        }

        return $ddl;
    }

    /**
     * Truncate table
     *
     * @deprecated since 1.4.0.1
     * @param string $tableName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function truncate($tableName, $schemaName = null)
    {
        return $this->truncateTable($tableName, $schemaName);
    }

    /**
     * Change table storage engine
     *
     * @param string $tableName
     * @param string $engine
     * @param string $schemaName
     * @return mixed
     */
    public function changeTableEngine($tableName, $engine, $schemaName = null)
    {
        $sql = sprintf('ALTER TABLE %s ENGINE=%s',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
            $engine);
        return $this->raw_query($sql);
    }

    /**
     * Inserts a table row with specified data
     * Special for Zero values to identity column
     *
     * @param string $table
     * @param array $bind
     * @return int The number of affected rows.
     */
    public function insertForce($table, array $bind)
    {
        $this->raw_query("SET @OLD_INSERT_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO'");
        $result = $this->insert($table, $bind);
        $this->raw_query("SET SQL_MODE=IFNULL(@OLD_INSERT_SQL_MODE,'')");
        return $result;
    }

    /**
     * Inserts a table row with specified data.
     *
     * @param mixed $table The table to insert data into.
     * @param array $data Column-value pairs or array of column-value pairs.
     * @param arrat $fields update fields pairs or values
     * @return int The number of affected rows.
     */
    public function insertOnDuplicate($table, array $data, array $fields = array())
    {
        // extract and quote col names from the array keys
        $row    = reset($data); // get first elemnt from data array
        $bind   = array(); // SQL bind array
        $cols   = array();
        $values = array();

        if (is_array($row)) { // Array of column-value pairs
            $cols = array_keys($row);
            foreach ($data as $row) {
                $line = array();
                if (array_diff($cols, array_keys($row))) {
                    throw new Varien_Exception('Invalid data for insert');
                }
                foreach ($row as $val) {
                    if ($val instanceof Zend_Db_Expr) {
                        $line[] = $val->__toString();
                    } else {
                        $line[] = '?';
                        $bind[] = $val;
                    }
                }
                $values[] = sprintf('(%s)', join(',', $line));
            }
            unset($row);
        } else { // Column-value pairs
            $cols = array_keys($data);
            $line = array();
            foreach ($data as $val) {
                if ($val instanceof Zend_Db_Expr) {
                    $line[] = $val->__toString();
                } else {
                    $line[] = '?';
                    $bind[] = $val;
                }
            }
            $values[] = sprintf('(%s)', join(',', $line));
        }

        $updateFields = array();
        if (empty($fields)) {
            $fields = $cols;
        }

        // quote column names
        $cols = array_map(array($this, 'quoteIdentifier'), $cols);

        // prepare ON DUPLICATE KEY conditions
        foreach ($fields as $k => $v) {
            $field = $value = null;
            if (!is_numeric($k)) {
                $field = $this->quoteIdentifier($k);
                if ($v instanceof Zend_Db_Expr) {
                    $value = $v->__toString();
                } else if (is_string($v)) {
                    $value = 'VALUES('.$this->quoteIdentifier($v).')';
                } else if (is_numeric($v)) {
                    $value = $this->quoteInto('?', $v);
                }
            } else if (is_string($v)) {
                $field = $this->quoteIdentifier($v);
                $value = 'VALUES('.$field.')';
            }

            if ($field && $value) {
                $updateFields[] = "{$field}={$value}";
            }
        }

        // build the statement
        $sql = "INSERT INTO "
             . $this->quoteIdentifier($table, true)
             . ' (' . implode(', ', $cols) . ') '
             . 'VALUES ' . implode(', ', $values);
        if ($updateFields) {
            $sql .= " ON DUPLICATE KEY UPDATE " . join(', ', $updateFields);
        }

        // execute the statement and return the number of affected rows
        $stmt = $this->query($sql, array_values($bind));
        $result = $stmt->rowCount();
        return $result;
    }

    /**
     * Inserts a table multiply rows with specified data.
     *
     * @param mixed $table The table to insert data into.
     * @param array $data Column-value pairs or array of Column-value pairs.
     * @return int The number of affected rows.
     */
    public function insertMultiple($table, array $data)
    {
        $row = reset($data);
        // support insert syntaxes
        if (!is_array($row)) {
            return $this->insert($table, $data);
        }

        // validate data array
        $cols = array_keys($row);
        $insertArray = array();
        foreach ($data as $row) {
            $line = array();
            if (array_diff($cols, array_keys($row))) {
                throw new Varien_Exception('Invalid data for insert');
            }
            foreach ($cols as $field) {
                $line[] = $row[$field];
            }
            $insertArray[] = $line;
        }
        unset($row);

        return $this->insertArray($table, $cols, $insertArray);
    }

    /**
     * Insert array to table based on columns definition
     *
     * @param   string $table
     * @param   array $columns
     * @param   array $data
     * @return  int
     */
    public function insertArray($table, array $columns, array $data)
    {
        $vals = array();
        $bind = array();
        $columnsCount = count($columns);
        foreach ($data as $row) {
            if ($columnsCount != count($row)) {
                throw new Varien_Exception('Invalid data for insert');
            }
            $line = array();
            if ($columnsCount == 1) {
                if ($row instanceof Zend_Db_Expr) {
                    $line = $row->__toString();
                } else {
                    $line = '?';
                    $bind[] = $row;
                }
                $vals[] = sprintf('(%s)', $line);
            } else {
                foreach ($row as $value) {
                    if ($value instanceof Zend_Db_Expr) {
                        $line[] = $value->__toString();
                    }
                    else {
                        $line[] = '?';
                        $bind[] = $value;
                    }
                }
                $vals[] = sprintf('(%s)', join(',', $line));
            }
        }

        // build the statement
        $columns = array_map(array($this, 'quoteIdentifier'), $columns);
        $sql = sprintf("INSERT INTO %s (%s) VALUES%s",
            $this->quoteIdentifier($table, true),
            implode(',', $columns), implode(', ', $vals));

        // execute the statement and return the number of affected rows
        $stmt = $this->query($sql, $bind);
        $result = $stmt->rowCount();
        return $result;
    }

    /**
     * Set cache adapter
     *
     * @param Zend_Cache_Backend_Interface $adapter
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function setCacheAdapter($adapter)
    {
        $this->_cacheAdapter = $adapter;
        return $this;
    }

    /**
     * Return new DDL Table object
     *
     * @param string $tableName the table name
     * @param string $schemaName the database/shema name
     * @return Varien_Db_Ddl_Table
     */
    public function newTable($tableName = null, $schemaName = null)
    {
        $table = new Varien_Db_Ddl_Table();
        if (!is_null($tableName)) {
            $table->setName($tableName);
        }
        if (!is_null($schemaName)) {
            $table->setSchema($schemaName);
        }
        return $table;
    }

    /**
     * Create table
     *
     * @param Varien_Db_Ddl_Table $table
     * @throws Zend_Db_Exception
     * @return Zend_Db_Pdo_Statement
     */
    public function createTable(Varien_Db_Ddl_Table $table)
    {
        $sqlFragment    = array_merge(
            $this->_getColumnsDefinition($table),
            $this->_getIndexesDefinition($table),
            $this->_getForeignKeysDefinition($table)
        );
        $tableOptions   = $this->_getOptionsDefination($table);

        $sql = sprintf("CREATE TABLE %s (\n%s\n) %s",
            $this->quoteIdentifier($table->getName()),
            implode(",\n", $sqlFragment),
            implode(" ", $tableOptions));

        return $this->query($sql);
    }

    /**
     * Retrieve columns and primary keys definition array for create table
     *
     * @param Varien_Db_Ddl_Table $table
     * @return array
     */
    protected function _getColumnsDefinition(Varien_Db_Ddl_Table $table)
    {
        $definition = array();
        $primary    = array();
        $columns    = $table->getColumns();
        if (empty($columns)) {
            throw new Zend_Db_Exception('Table columns are not defined');
        }

        foreach ($columns as $columnData) {
            $columnDefination = $this->_getColumnDefinition($columnData);
            if ($columnData['PRIMARY']) {
                $primary[$columnData['COLUMN_NAME']] = $columnData['PRIMARY_POSITION'];
            }

            $definition[] = sprintf('  %s %s',
                $this->quoteIdentifier($columnData['COLUMN_NAME']),
                $columnDefination
            );
        }

        // PRIMARY KEY
        if (!empty($primary)) {
            asort($primary, SORT_NUMERIC);
            $primary = array_map(array($this, 'quoteIdentifier'), array_keys($primary));
            $definition[] = sprintf('  PRIMARY KEY (%s)', join(', ', $primary));
        }

        return $definition;
    }

    /**
     * Retrieve table indexes definition array for create table
     *
     * @param Varien_Db_Ddl_Table $table
     * @return array
     */
    protected function _getIndexesDefinition(Varien_Db_Ddl_Table $table)
    {
        $definition = array();
        $indexes    = $table->getIndexes();
        if (!empty($indexes)) {
            foreach ($indexes as $indexData) {
                if ($indexData['UNIQUE']) {
                    $indexType = 'UNIQUE';
                } else if (!empty($indexData['TYPE'])) {
                    $indexType = $indexData['TYPE'];
                } else {
                    $indexType = 'KEY';
                }

                $columns = array();
                foreach ($indexData['COLUMNS'] as $columnData) {
                    $column = $this->quoteIdentifier($columnData['NAME']);
                    if (!empty($columnData['SIZE'])) {
                        $column .= sprintf('(%d)', $columnData['SIZE']);
                    }
                    $columns[] = $column;
                }
                $definition[] = sprintf('  %s %s (%s)',
                    $indexType,
                    $this->quoteIdentifier($indexData['INDEX_NAME']),
                    join(', ', $columns)
                );
            }
        }

        return $definition;
    }

    /**
     * Retrieve table foreign keys definition array for create table
     *
     * @param Varien_Db_Ddl_Table $table
     * @return array
     */
    protected function _getForeignKeysDefinition(Varien_Db_Ddl_Table $table)
    {
        $definition = array();
        $relations  = $table->getForeignKeys();

        if (!empty($relations)) {
            foreach ($relations as $fkData) {
                switch ($fkData['ON_DELETE']) {
                    case Varien_Db_Ddl_Table::ACTION_CASCADE:
                    case Varien_Db_Ddl_Table::ACTION_RESTRICT:
                    case Varien_Db_Ddl_Table::ACTION_SET_NULL:
                        $onDelete = $fkData['ON_DELETE'];
                        break;
                    default:
                        $onDelete = Varien_Db_Ddl_Table::ACTION_NO_ACTION;
                }

                switch ($fkData['ON_UPDATE']) {
                    case Varien_Db_Ddl_Table::ACTION_CASCADE:
                    case Varien_Db_Ddl_Table::ACTION_RESTRICT:
                    case Varien_Db_Ddl_Table::ACTION_SET_NULL:
                        $onUpdate = $fkData['ON_UPDATE'];
                        break;
                    default:
                        $onUpdate = Varien_Db_Ddl_Table::ACTION_NO_ACTION;
                }

                $definition[] = sprintf('  CONSTRAINT %s FOREIGN KEY (%s) REFERENCES %s (%s) ON DELETE %s ON UPDATE %s',
                    $this->quoteIdentifier($fkData['FK_NAME']),
                    $this->quoteIdentifier($fkData['COLUMN_NAME']),
                    $this->quoteIdentifier($fkData['REF_TABLE_NAME']),
                    $this->quoteIdentifier($fkData['REF_COLUMN_NAME']),
                    $onDelete,
                    $onUpdate
                );
            }
        }

        return $definition;
    }

    /**
     * Retrieve table options definition array for create table
     *
     * @param Varien_Db_Ddl_Table $table
     * @return array
     */
    protected function _getOptionsDefination(Varien_Db_Ddl_Table $table)
    {
        $definition = array();
        $comment    = $table->getComment();
        if (empty($comment)) {
            throw new Varien_Db_Exception('Comment for table is required and must be defined');
        }
        $definition[] = $this->quoteInto('COMMENT=?', $comment);

        $tableProps = array(
            'type'              => 'ENGINE=%s',
            'checksum'          => 'CHECKSUM=%d',
            'auto_increment'    => 'AUTO_INCREMENT=%d',
            'avg_row_length'    => 'AVG_ROW_LENGTH=%d',
            'max_rows'          => 'MAX_ROWS=%d',
            'min_rows'          => 'MIN_ROWS=%d',
            'delay_key_write'   => 'DELAY_KEY_WRITE=%d',
            'row_format'        => 'row_format=%s',
            'charset'           => 'charset=%s',
            'collate'           => 'COLLATE=%s'
        );
        foreach ($tableProps as $key => $mask) {
            $v = $table->getOption($key);
            if (!is_null($v)) {
                $definition[] = sprintf($mask, $v);
            }
        }

        return $definition;
    }
    /**
     * Retrieve column definition fragment
     *
     * @param array $options
     * @param string $ddlType Table DDL Column type constant
     * @throws Varien_Exception
     * @return string
     */
    protected function _getColumnDefinition($options, $ddlType = null)
    {
        // convert keys to uppercase
        foreach ($options as $k => $v) {
            unset($options[$k]);
            $options[strtoupper($k)] = $v;
        }

        $cType      = null;
        $cUnsigned  = false;
        $cNullable  = true;
        $cDefault   = false;
        $cIdentity  = false;

        // detect and validate column type
        if (is_null($ddlType) && isset($options['TYPE'])) {
            $ddlType = $options['TYPE'];
        } else if (is_null($ddlType) && isset($options['COLUMN_TYPE'])) {
            $ddlType = $options['COLUMN_TYPE'];
        }

        if (empty($ddlType) || !isset($this->_ddlColumnTypes[$ddlType])) {
            throw new Varien_Exception('Invalid column defination data');
        }

        // column size
        $cType = $this->_ddlColumnTypes[$ddlType];
        switch ($ddlType) {
            case Varien_Db_Ddl_Table::TYPE_SMALLINT:
            case Varien_Db_Ddl_Table::TYPE_INTEGER:
            case Varien_Db_Ddl_Table::TYPE_BIGINT:
                if (!empty($options['UNSIGNED'])) {
                    $cUnsigned = true;
                }
                break;
            case Varien_Db_Ddl_Table::TYPE_DECIMAL:
            case Varien_Db_Ddl_Table::TYPE_NUMERIC:
                $precision  = 10;
                $scale      = 0;
                $match      = array();
                if (!empty($options['LENGTH']) && preg_match('#^\(?(\d+),(\d+)\)?$#', $options['LENGTH'], $match)) {
                    $precision  = $match[1];
                    $scale      = $match[2];
                } else {
                    if (isset($options['SCALE']) && is_numeric($options['SCALE'])) {
                        $scale = $options['SCALE'];
                    }
                    if (isset($options['PRECISION']) && is_numeric($options['PRECISION'])) {
                        $precision = $options['PRECISION'];
                    }
                }
                $cType .= sprintf('(%d,%d)', $precision, $scale);
                break;
            case Varien_Db_Ddl_Table::TYPE_TEXT:
            case Varien_Db_Ddl_Table::TYPE_BLOB:
                if (empty($options['LENGTH'])) {
                    $options['LENGTH'] = Varien_Db_Ddl_Table::DEFAULT_TEXT_SIZE;
                }
                if ($options['LENGTH'] <= 255) {
                    $cType = $ddlType == Varien_Db_Ddl_Table::TYPE_TEXT ? 'varchar' : 'varbinary';
                    $cType = sprintf('%s(%d)', $cType, $options['LENGTH']);
                } else if ($options['LENGTH'] > 255 && $options['LENGTH'] <= 65536) {
                    $cType = $ddlType == Varien_Db_Ddl_Table::TYPE_TEXT ? 'text' : 'blob';
                } else if ($options['LENGTH'] > 65536 && $options['LENGTH'] <= 16777216) {
                    $cType = $ddlType == Varien_Db_Ddl_Table::TYPE_TEXT ? 'mediumtext' : 'mediumblob';
                } else {
                    $cType = $ddlType == Varien_Db_Ddl_Table::TYPE_TEXT ? 'longtext' : 'longtext';
                }
                break;
        }

        if (array_key_exists('DEFAULT', $options)) {
            $cDefault = $options['DEFAULT'];
        }
        if (array_key_exists('NULLABLE', $options)) {
            $cNullable = (bool)$options['NULLABLE'];
        }
        if (!empty($options['IDENTITY']) || !empty($options['AUTO_INCREMENT'])) {
            $cIdentity = true;
        }

        // prepare default value string
        if ($ddlType == Varien_Db_Ddl_Table::TYPE_TIMESTAMP) {
            if (is_null($cDefault)) {
                $cDefault = new Zend_Db_Expr('NULL');
            } else if ($cDefault == Varien_Db_Ddl_Table::TIMESTAMP_INIT) {
                $cDefault = new Zend_Db_Expr('CURRENT_TIMESTAMP');
            } else if ($cDefault == Varien_Db_Ddl_Table::TIMESTAMP_UPDATE) {
                $cDefault = new Zend_Db_Expr('0 ON UPDATE CURRENT_TIMESTAMP');
            } else if ($cDefault == Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE) {
                $cDefault = new Zend_Db_Expr('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
            }
        } else if (is_null($cDefault) && $cNullable) {
            $cDefault = new Zend_Db_Expr('NULL');
        }

        if (empty($options['COMMENT'])) {
            //throw new Varien_Db_Exception('The column description is required and must be defined');
            $options['COMMENT'] = '';
        }

        return sprintf('%s%s%s%s%s COMMENT %s',
            $cType,
            $cUnsigned ? ' UNSIGNED' : '',
            $cNullable ? ' NULL' : ' NOT NULL',
            $cDefault !== false ? $this->quoteInto(' default ?', $cDefault) : '',
            $cIdentity ? ' auto_increment' : '',
            $this->quote($options['COMMENT'])
        );
    }

    /**
     * Drop table from database
     *
     * @param string $tableName
     * @param string $schemaName
     * @return boolean
     */
    public function dropTable($tableName, $schemaName = null)
    {
        $query = sprintf('DROP TABLE IF EXISTS %s',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)));
        $this->query($query);

        return true;
    }

    /**
     * Truncate a table
     *
     * @param string $tableName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Interface
     */
    public function truncateTable($tableName, $schemaName = null)
    {
        if (!$this->isTableExists($tableName, $schemaName)) {
            throw new Varien_Exception(sprintf('Table "%s" is not exists', $tableName));
        }

        $query = sprintf('TRUNCATE TABLE %s',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)));
        $this->query($query);

        return $this;
    }

    /**
     * Check is a table exists
     *
     * @param string $tableName
     * @param string $schemaName
     * @return boolean
     */
    public function isTableExists($tableName, $schemaName = null)
    {
        return $this->showTableStatus($tableName, $schemaName) !== false;
    }

    /**
     * Rename table
     *
     * @param string $oldTableName
     * @param string $newTableName
     * @param string $schemaName
     * @return boolean
     */
    public function renameTable($oldTableName, $newTableName, $schemaName = null)
    {
        if (!$this->isTableExists($oldTableName, $schemaName)) {
            throw new Zend_Db_Exception(sprintf('Table "%s" is not exists', $oldTableName));
        }
        if ($this->isTableExists($newTableName, $schemaName)) {
            throw new Zend_Db_Exception(sprintf('Table "%s" already exists', $newTableName));
        }

        $oldTable = $this->_getTableName($oldTableName, $schemaName);
        $newTable = $this->_getTableName($newTableName, $schemaName);

        $query = sprintf('ALTER TABLE %s RENAME TO %s', $oldTable, $newTable);
        $this->query($query);

        $this->resetDdlCache($oldTableName, $schemaName);

        return true;
    }

    /**
     * Add new index to table name
     *
     * @param string $tableName
     * @param string $indexName
     * @param string|array $fields  the table column name or array of ones
     * @param string $indexType     the index type
     * @param string $schemaName
     * @return Varien_Db_Adapter_Interface
     */
    public function addIndex($tableName, $indexName, $fields,
        $indexType = Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX, $schemaName = null)
    {
        $columns = $this->describeTable($tableName, $schemaName);
        $keyList = $this->getIndexList($tableName, $schemaName);

        $query = sprintf('ALTER TABLE %s', $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)));
        if (isset($keyList[strtoupper($indexName)])) {
            if ($keyList[strtoupper($indexName)] == self::INDEX_TYPE_PRIMARY) {
                $query .= ' DROP PRIMARY KEY,';
            } else {
                $query .= sprintf(' DROP INDEX %s,', $this->quoteIdentifier($indexName));
            }
        }

        if (!is_array($fields)) {
            $fields = array($fields);
        }

        $fieldSql = array();
        foreach ($fields as $field) {
            if (!isset($columns[$field])) {
                $msg = sprintf('There is no field "%s" that you are trying to create an index on "%s"',
                    $field, $tableName);
                throw new Exception($msg);
            }
            $fieldSql[] = $this->quoteIdentifier($field);
        }
        $fieldSql = join(',', $fieldSql);

        switch (strtolower($indexType)) {
            case Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY:
                $condition = 'PRIMARY KEY';
                break;
            case Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE:
                $condition = 'UNIQUE ' . $this->quoteIdentifier($indexName);
                break;
            case Varien_Db_Adapter_Interface::INDEX_TYPE_FULLTEXT:
                $condition = 'FULLTEXT ' . $this->quoteIdentifier($indexName);
                break;
            default:
                $condition = 'INDEX ' . $this->quoteIdentifier($indexName);
                break;
        }

        $query .= sprintf(' ADD %s (%s)', $condition, $fieldSql);

        $cycle = true;
        while ($cycle === true) {
            try {
                $result = $this->raw_query($query);
                $cycle  = false;
            }
            catch (PDOException $e) {
                if (in_array(strtolower($indexType), array('primary', 'unique'))) {
                    $match = array();
                    if (preg_match('#SQLSTATE\[23000\]: [^:]+: 1062[^\']+\'([\d-\.]+)\'#', $e->getMessage(), $match)) {
                        $ids = explode('-', $match[1]);
                        $this->_removeDuplicateEntry($tableName, $fields, $ids);
                        continue;
                    }
                }
                throw $e;
            }
            catch (Exception $e) {
                throw $e;
            }
        }

        $this->resetDdlCache($tableName, $schemaName);

        return $result;
    }

    /**
     * Drop the index from table
     *
     * @param string $tableName
     * @param string $keyName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Interface
     */
    public function dropIndex($tableName, $keyName, $schemaName = null)
    {
        $indexList = $this->getIndexList($tableName, $schemaName);
        $keyName = strtoupper($keyName);
        if (!isset($indexList[$keyName])) {
            return $this;
        }

        if ($keyName == 'PRIMARY') {
            $cond = 'DROP PRIMARY KEY';
        } else {
            $cond = sprintf('DROP KEY %s', $this->quoteIdentifier($indexList[$keyName]['KEY_NAME']));
        }
        $sql = sprintf('ALTER TABLE %s %s',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
            $cond);

        $this->resetDdlCache($tableName, $schemaName);
        return $this->raw_query($sql);
    }

    /**
     * Retrieve Foreign Key name
     * @param string $fkName
     * @return string
     */
    protected function _getForeignKeyName($fkName)
    {
        if (substr($fkName, 0, 3) != 'FK_') {
            $fkName = 'FK_' . $fkName;
        }
        return $fkName;
    }

    /**
     * Add new Foreign Key to table
     * If Foreign Key with same name is exist - it will be deleted
     *
     * @param string $fkName
     * @param string $tableName
     * @param string $columnName
     * @param string $refTableName
     * @param string $refColumnName
     * @param string $onDelete
     * @param string $onUpdate
     * @param boolean $purge            trying remove invalid data
     * @param string $schemaName
     * @param string $refSchemaName
     * @return Varien_Db_Adapter_Interface
     */
    public function addForeignKey($fkName, $tableName, $columnName, $refTableName, $refColumnName,
        $onDelete = Varien_Db_Adapter_Interface::FK_ACTION_CASCADE,
        $onUpdate = Varien_Db_Adapter_Interface::FK_ACTION_CASCADE,
        $purge = false, $schemaName = null, $refSchemaName = null)
    {
        $fkName = $this->_getForeignKeyName($fkName);

        $this->dropForeignKey($tableName, $fkName, $schemaName);

        if ($purge) {
            $this->purgeOrphanRecords($tableName, $columnName, $refTableName, $refColumnName, $onDelete);
        }

        $query = sprintf('ALTER TABLE %s ADD CONSTRAINT %s FOREIGN KEY (%s) REFERENCES %s (%s)',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
            $this->quoteIdentifier($fkName),
            $this->quoteIdentifier($columnName),
            $this->quoteIdentifier($this->_getTableName($refTableName, $refSchemaName)),
            $this->quoteIdentifier($refColumnName)
        );

        if (!is_null($onDelete)) {
            $query .= ' ON DELETE ' . strtoupper($onDelete);
        }
        if (!is_null($onUpdate)) {
            $query .= ' ON UPDATE ' . strtoupper($onUpdate);
        }

        $this->resetDdlCache($tableName);
        return $this->raw_query($query);
    }

    /**
     * Format Date to internal database date format
     *
     * @param int|string|Zend_Date $date
     * @param boolean $includeTime
     * @return string
     */
    public function formatDate($date, $includeTime = true)
    {
        $date = Varien_Date::formatDate($date, $includeTime);

        if (is_null($date)) {
            return new Zend_Db_Expr('NULL');
        }

        return $date;
    }

    /**
     * Run additional environment before setup
     *
     * @return Varien_Db_Adapter_Interface
     */
    public function startSetup()
    {
        $this->raw_query("SET SQL_MODE=''");
        $this->raw_query("SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0");
        $this->raw_query("SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO'");

        return $this;
    }

    /**
     * Run additional environment after setup
     *
     * @return Varien_Db_Adapter_Interface
     */
    public function endSetup()
    {
        $this->raw_query("SET SQL_MODE=IFNULL(@OLD_SQL_MODE,'')");
        $this->raw_query("SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS=0, 0, 1)");

        return $this;
    }

    /**
     * Build SQL statement for condition
     *
     * If $condition integer or string - exact value will be filtered
     *
     * If $condition is array is - one of the following structures is expected:
     * - array("from" => $fromValue, "to" => $toValue)
     * - array("eq" => $equalValue)
     * - array("neq" => $notEqualValue)
     * - array("like" => $likeValue)
     * - array("in" => array($inValues))
     * - array("nin" => array($notInValues))
     * - array("notnull" => $valueIsNotNull)
     * - array("null" => $valueIsNull)
     * - array("gt" => $greaterValue)
     * - array("lt" => $lessValue)
     * - array("gteq" => $greaterOrEqualValue)
     * - array("lteq" => $lessOrEqualValue)
     * - array("finset" => $valueInSet)
     * - array("regexp" => $regularExpression)
     *
     * If non matched - sequential array is expected and OR conditions
     * will be built using above mentioned structure
     *
     * @param string|array $fieldName
     * @param integer|string|array $condition
     * @return string
     */
    public function prepareSqlCondition($fieldName, $condition)
    {
        $query = '';

        if (is_array($condition) && isset($condition['field_expr'])) {
            $fieldName = str_replace('#?', $this->quoteIdentifier($fieldName), $condition['field_expr']);
        }
        if (is_array($condition)) {
            if (isset($condition['from']) || isset($condition['to'])) {
                if (isset($condition['from'])) {
                    if (empty($condition['date'])) {
                        if (empty($condition['datetime'])) {
                            $from = $condition['from'];
                        } else {
                            $from = $this->convertDateTime($condition['from']);
                        }
                    } else {
                        $from = $this->convertDate($condition['from']);
                    }
                    $query .= $this->quoteInto("{$fieldName} >= ?", $from);
                }
                if (isset($condition['to'])) {
                    $query .= empty($query) ? '' : ' AND ';

                    if (empty($condition['date'])) {
                        if (empty($condition['datetime'])) {
                            $to = $condition['to'];
                        } else {
                            $to = $this->convertDateTime($condition['to']);
                        }
                    } else {
                        $to = $this->convertDate($condition['to']);
                    }

                    $query .= $this->quoteInto("{$fieldName} <= ?", $to);
                }
            } else if (isset($condition['eq'])) {
                $query = $this->quoteInto("{$fieldName} = ?", $condition['eq']);
            } else if (isset($condition['neq'])) {
                $query = $this->quoteInto("{$fieldName} != ?", $condition['neq']);
            } else if (isset($condition['like'])) {
                $query = $this->quoteInto("{$fieldName} LIKE ?", $condition['like']);
            } else if (isset($condition['nlike'])) {
                $query = $this->quoteInto("{$fieldName} NOT LIKE ?", $condition['nlike']);
            } else if (isset($condition['in'])) {
                $query = $this->quoteInto("{$fieldName} IN(?)", $condition['in']);
            } else if (isset($condition['nin'])) {
                $query = $this->quoteInto("{$fieldName} NOT IN(?)", $condition['nin']);
            } else if (isset($condition['is'])) {
                $query = $this->quoteInto("{$fieldName} IS ?", $condition['is']);
            } else if (isset($condition['notnull'])) {
                $query = "$fieldName IS NOT NULL";
            } else if (isset($condition['null'])) {
                $query = "$fieldName IS NULL";
            } else if (isset($condition['gt'])) {
                $query = $this->quoteInto("{$fieldName} > ?", $condition['gt']);
            } else if (isset($condition['lt'])) {
                $query = $this->quoteInto("{$fieldName} < ?", $condition['lt']);
            } else if (isset($condition['gteq'])) {
                $query = $this->quoteInto("{$fieldName} >= ?", $condition['gteq']);
            } else if (isset($condition['lteq'])) {
                $query = $this->quoteInto("{$fieldName} <= ?", $condition['lteq']);
            } else if (isset($condition['finset'])) {
                $query = $this->quoteInto("FIND_IN_SET(?, {$fieldName})", $condition['finset']);
            } else if (isset($condition['regexp'])) {
                $query = $this->quoteInto("{$fieldName} REGEXP ?)", $condition['regexp']);
            } else {
                $queries = array();
                foreach ($condition as $orCondition) {
                    $queries[] = sprintf('(%s)', $this->prepareSqlCondition($fieldName, $orCondition));
                }

                $query = sprintf('(%s)', join(' OR ', $queries));
            }
        } else {
            $query = $this->quoteInto("{$fieldName} = ?", (string)$condition);
        }

        return $query;
    }

    /**
     * Prepare value for save in column
     * Return converted to column data type value
     *
     * @param array $column     the column describe array
     * @param mixed $value
     * @return mixed
     */
    public function prepareColumnValue(array $column, $value)
    {
        if ($value instanceof Zend_Db_Expr) {
            return $value;
        }

        // return original value if invalid column describe data
        if (!isset($column['DATA_TYPE'])) {
            return $value;
        }

        // return null
        if (is_null($value) && $column['NULLABLE']) {
            return null;
        }

        switch ($column['DATA_TYPE']) {
            case 'smallint':
            case 'int':
            case 'bigint':
                $value = (int)$value;
                break;

            case 'decimal':
                $precision  = 10;
                $scale      = 0;
                if (isset($column['SCALE'])) {
                    $scale = $column['SCALE'];
                }
                if (isset($column['PRECISION'])) {
                    $precision = $column['PRECISION'];
                }
                $format = sprintf('%%%d.%dF', $precision - $scale, $scale);
                $value  = (float)sprintf($format, $value);
                break;

            case 'float':
                $value  = (float)sprintf('%F', $value);
                break;

            case 'date':
                $value  = $this->formatDate($value, false);
                break;
            case 'timestamp':
                $value  = $this->formatDate($value);
                break;

            case 'varchar':
            case 'mediumtext':
            case 'text':
            case 'longtext':
                $value  = (string)$value;
                if ($column['NULLABLE'] && $value == '') {
                    $value = null;
                }
                break;
        }

        return $value;
    }

    /**
     * Generate fragment of SQL, that check condition and return true or false value
     *
     * @param string $condition     expression
     * @param string $true          true value
     * @param string $false         false value
     */
    public function getCheckSql($condition, $true, $false)
    {
        return new Zend_Db_Expr("IF({$condition}, {$true}, {$false})");
    }

    /**
     * Generate fragment of SQL, that combine together (concatenate) the results from data array
     * All arguments in data must be quoted
     *
     * @param array $data
     * @param string $separator concatenate with separator
     * @return Zend_Db_Expr
     */
    public function getConcatSql(array $data, $separator = null)
    {
        $format = empty($separator) ? 'CONCAT(%s)' : "CONCAT_WS('{$separator}', %s)";
        return new Zend_Db_Expr(sprintf($format, join(', ', $data)));
    }

    /**
     * Generate fragment of SQL that returns length of character string
     * The string argument must be quoted
     *
     * @param string $string
     * @return Zend_Db_Expr
     */
    public function getLengthSql($string)
    {
        return new Zend_Db_Expr(sprintf('LENGTH(%s)', $string));
    }

    /**
     * Generate fragment of SQL, that compare with two or more arguments, and returns the smallest
     * (minimum-valued) argument
     * All arguments in data must be quoted
     *
     * @param array $data
     * @return Zend_Db_Expr
     */
    public function getLeastSql(array $data)
    {
        return new Zend_Db_Expr(sprintf('LEAST(%s)', join(', ', $data)));
    }

    /**
     * Generate fragment of SQL, that compare with two or more arguments, and returns the largest
     * (maximum-valued) argument
     * All arguments in data must be quoted
     *
     * @param array $data
     * @return Zend_Db_Expr
     */
    public function getGreatestSql(array $data)
    {
        return new Zend_Db_Expr(sprintf('GREATEST(%s)', join(', ', $data)));
    }

    /**
     * Get Interval Unit SQL fragment
     *
     * @param int $interval
     * @param string $unit
     * @return string
     */
    protected function _getIntervalUnitSql($interval, $unit)
    {
        if (!isset($this->_intervalUnits[$unit])) {
            throw new Varien_Db_Exception(sprintf('Undefined interval unit "%s" specified', $unit));
        }

        return sprintf('INTERVAL %d %s', $interval, $this->_intervalUnits[$unit]);
    }

    /**
     * Add time values (intervals) to a date value
     *
     * @see INTERVAL_* constants for $unit
     *
     * @param Zend_Db_Expr|string $date   quoted field name or SQL statement
     * @param int $interval
     * @param string $unit
     * @return Zend_Db_Expr
     */
    public function getDateAddSql($date, $interval, $unit)
    {
        $expr = sprintf('DATE_ADD(%s, %s)', $date, $this->_getIntervalUnitSql($interval, $unit));
        return new Zend_Db_Expr($expr);
    }

    /**
     * Subtract time values (intervals) to a date value
     *
     * @see INTERVAL_* constants for $expr
     *
     * @param Zend_Db_Expr|string $date   quoted field name or SQL statement
     * @param int|string $interval
     * @param string $unit
     * @return Zend_Db_Expr
     */
    public function getDateSubSql($date, $interval, $unit)
    {
        $expr = sprintf('DATE_SUB(%s, %s)', $date, $this->_getIntervalUnitSql($interval, $unit));
        return new Zend_Db_Expr($expr);
    }

    /**
     * Format date as specified
     *
     * Supported format Specifier
     *
     * %H   Hour (00..23)
     * %i   Minutes, numeric (00..59)
     * %s   Seconds (00..59)
     * %d   Day of the month, numeric (00..31)
     * %m   Month, numeric (00..12)
     * %Y   Year, numeric, four digits
     *
     * @param Zend_Db_Expr|string $date   quoted field name or SQL statement
     * @param string $format
     * @return Zend_Db_Expr
     */
    public function getDateFormatSql($date, $format)
    {
        $expr = sprintf("DATE_FORMAT(%s, '%s')", $date, $format);
        return new Zend_Db_Expr($expr);
    }

    /**
     * Extract the date part of a date or datetime expression
     *
     * @param Zend_Db_Expr|string $date   quoted field name or SQL statement
     * @return Zend_Db_Expr
     */
    public function getDatePartSql($date)
    {
        return new Zend_Db_Expr(sprintf('DATE(%s)', $date));
    }

    /**
     * Extract part of a date
     *
     * @see INTERVAL_* constants for $unit
     *
     * @param Zend_Db_Expr|string $date   quoted field name or SQL statement
     * @param string $unit
     * @return Zend_Db_Expr
     */
    public function getDateExtractSql($date, $unit)
    {
        if (!isset($this->_intervalUnits[$unit])) {
            throw new Varien_Db_Exception(sprintf('Undefined interval unit "%s" specified', $unit));
        }

        $expr = sprintf('EXTRACT(%s FROM %s)', $this->_intervalUnits[$unit], $date);
        return new Zend_Db_Expr($expr);
    }

    /**
     * Retrieve valid table name
     * Check table name length and allowed symbols
     *
     * @param string $tableName
     * @return string
     */
    public function getTableName($tableName)
    {
        if (strlen($tableName) > 64) {
            $shortName = Varien_Db_Helper::shortName($tableName);
            if (strlen($shortName) > 64) {
                $tableName = 'table_' . md5($shortName);
            } else {
                $tableName = $shortName;
            }
        }

        return $tableName;
    }

    /**
     * Retrieve valid index name
     * Check index name length and allowed symbols
     *
     * @param string $tableName
     * @param string|array $fields  the columns list
     * @param boolean $isUnique
     * @return string
     */
    public function getIndexName($tableName, $fields, $isUnique = false)
    {
        if (is_array($fields)) {
            $fields = join('-', $fields);
        }

        $hash = sprintf('%s_%s', ($isUnique ? 'unq' : 'idx'), $fields);

        if (strlen($hash) > 64) {
            $short = Varien_Db_Helper::shortName($hash);
            if (strlen($short) > 64) {
                $hash = sprintf('%s_%s', ($isUnique ? 'unq' : 'idx'), md5($hash));
            } else {
                $hash = $short;
            }
        }

        return strtoupper($hash);
    }

    /**
     * Retrieve valid foreign key name
     * Check foreign key name length and allowed symbols
     *
     * @param string $priTableName
     * @param string $priColumnName
     * @param string $refTableName
     * @param string $refColumnName
     * @return string
     */
    public function getForeignKeyName($priTableName, $priColumnName, $refTableName, $refColumnName)
    {
        $hash = sprintf('fk_%s_%s_%s_%s', $priTableName, $priColumnName, $refTableName, $refColumnName);
        if (strlen($hash) > 64) {
            $short = Varien_Db_Helper::shortName($hash);
            if (strlen($short) > 64) {
                $hash = sprintf('fk_%s', md5($hash));
            } else {
                $hash = $short;
            }
        }

        return strtoupper($hash);
    }

    /**
     * Stop updating indexes
     *
     * @param string $tableName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Interface
     */
    public function disableTableKeys($tableName, $schemaName = null)
    {
        $tableName = $this->_getTableName($tableName, $schemaName);
        $query = sprintf('ALTER TABLE %s DISABLE KEYS', $this->quoteIdentifier($tableName));

        $this->query($query);

        return $this;
    }

    /**
     * Re-create missing indexes
     *
     * @param string $tableName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Interface
     */
    public function enableTableKeys($tableName, $schemaName = null)
    {
        $tableName = $this->_getTableName($tableName, $schemaName);
        $query = sprintf('ALTER TABLE %s ENABLE KEYS', $this->quoteIdentifier($tableName));

        $this->query($query);

        return $this;
    }

    /**
     * Get insert from Select object query
     *
     * @param Varien_Db_Select $select
     * @param string $table     insert into table
     * @param array $fields
     * @param int $mode
     * @return string
     */
    public function insertFromSelect(Varien_Db_Select $select, $table, array $fields = array(), $mode = false)
    {
        $query = 'INSERT';
        if ($mode == self::INSERT_IGNORE) {
            $query .= ' IGNORE';
        }
        $query = sprintf('%s INTO %s', $query, $this->quoteIdentifier($table));
        if ($fields) {
            $columns = array_map(array($this, 'quoteIdentifier'), $fields);
            $query = sprintf('%s (%s)', $query, join(', ', $columns));
        }

        $query = sprintf('%s %s', $query, $select->assemble());

        if ($mode == self::INSERT_ON_DUPLICATE) {
            if (!$fields) {
                $describe = $this->describeTable($table);
                foreach ($describe as $column) {
                    if ($column['PRIMARY'] === false) {
                        $fields[] = $column['COLUMN_NAME'];
                    }
                }
            }
            $update = array();
            foreach ($fields as $field) {
                $update[] = sprintf('%1$s = VALUES(%1$s)', $this->quoteIdentifier($field));
            }

            if ($update) {
                $query = sprintf('%s ON DUPLICATE KEY UPDATE %s', $query, join(', ', $update));
            }
        }

        return $query;
    }

    /**
     * Get update table query using select object for join and update
     *
     * @param Varien_Db_Select $select
     * @param string|array $table
     * @return string
     */
    public function updateFromSelect(Varien_Db_Select $select, $table)
    {
        if (!is_array($table)) {
            $table = array($table => $table);
        }

        // get table name and alias
        $keys       = array_keys($table);
        $tableAlias = $keys[0];
        $tableName  = $table[$keys[0]];

        $query = sprintf('UPDATE %s', $this->quoteTableAs($tableName, $tableAlias));

        // render JOIN conditions (FROM Part)
        $joinConds  = array();
        foreach ($select->getPart(Zend_Db_Select::FROM) as $correlationName => $joinProp) {
            if ($joinProp['joinType'] == Zend_Db_Select::FROM) {
                $joinType = strtoupper(Zend_Db_Select::INNER_JOIN);
            } else {
                $joinType = strtoupper($joinProp['joinType']);
            }
            $joinTable = '';
            if ($joinProp['schema'] !== null) {
                $joinTable = sprintf('%s.', $this->quoteIdentifier($joinProp['schema']));
            }
            $joinTable .= $this->quoteTableAs($joinProp['tableName'], $correlationName);

            $join = sprintf(' %s %s', $joinType, $joinTable);

            if (!empty($joinProp['joinCondition'])) {
                $join = sprintf('%s ON %s', $join, $joinProp['joinCondition']);
            }

            $joinConds[] = $join;
        }

        if ($joinConds) {
            $query = sprintf("%s\n%s", $query, implode("\n", $joinConds));
        }

        // render UPDATE SET
        $columns = array();
        foreach ($select->getPart(Zend_Db_Select::COLUMNS) as $columnEntry) {
            list($correlationName, $column, $alias) = $columnEntry;
            if (empty($alias)) {
                $alias = $column;
            }
            if (!$column instanceof Zend_Db_Expr && !empty($correlationName)) {
                $column = $this->quoteIdentifier(array($correlationName, $column));
            }
            $columns[] = sprintf('%s = %s', $this->quoteIdentifier(array($tableAlias, $alias)), $column);
        }

        if (!$columns) {
            throw new Varien_Db_Exception('The columns for UPDATE statement are not defined');
        }

        $query = sprintf("%s\nSET %s", $query, implode(', ', $columns));

        // render WHERE
        $wherePart = $select->getPart(Zend_Db_Select::WHERE);
        if ($wherePart) {
            $query = sprintf("%s\nWHERE %s", $query, implode(' ', $wherePart));
        }

        return $query;
    }

    /**
     * Get delete from select object query
     *
     * @param Varien_Db_Select $select
     * @param string $table the table name or alias used in select
     * @return string|int
     */
    public function deleteFromSelect(Varien_Db_Select $select, $table)
    {
        $select = clone $select;
        $select->reset(Zend_Db_Select::DISTINCT);
        $select->reset(Zend_Db_Select::COLUMNS);

        $query = sprintf('DELETE %s %s', $this->quoteIdentifier($table), $select->assemble());

        return $query;
    }

    /**
     *
     */
    public function getTablesChecksum($tableNames, $schemaName = null)
    {
        return array();
    }
}
