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
 * @category    Varien
 * @package     Varien_DB
 * @copyright   Copyright (c) 2010 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Varien DB Adapter for MS SQL
 *
 * @property PDO $_connection
 * @method PDO $getConnection()
 *
 * @category    Varien
 * @package     Varien_DB
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Db_Adapter_Pdo_Mssql extends Zend_Db_Adapter_Pdo_Mssql
    implements Varien_Db_Adapter_Interface
{
    const DEBUG_CONNECT             = 0;
    const DEBUG_TRANSACTION         = 1;
    const DEBUG_QUERY               = 2;

    const TIMESTAMP_FORMAT          = 'Y-m-d H:i:s';
    const DATE_FORMAT               = 'Y-m-d';

    const DDL_DESCRIBE              = 1;
    const DDL_CREATE                = 2;
    const DDL_INDEX                 = 3;
    const DDL_FOREIGN_KEY           = 4;
    const DDL_CACHE_PREFIX          = 'DB_PDO_MSSQL_DDL';
    const DDL_CACHE_TAG             = 'DB_PDO_MSSQL_DDL';

    const TRIGGER_CASCADE_UPD       = 'on_update';
    const TRIGGER_CASCADE_DEL       = 'on_delete';

    const EXTPROP_COMMENT_TABLE     = 'TABLE_COMMENT';
    const EXTPROP_COMMENT_COLUMN    = 'COLUMN_COMMENT';
    const EXTPROP_COMMENT_FK_UPDATE = 'FOREIGN_KEY_UPDATE_ACTION';
    const EXTPROP_COMMENT_FK_DELETE = 'FOREIGN_KEY_DELETE_ACTION';
    const LENGTH_TABLE_NAME         = 128;
    const LENGTH_INDEX_NAME         = 128;
    const LENGTH_FOREIGN_NAME       = 128;

    // Capacity of varchar and varbinary types
    const VAR_LIMIT             = 8000;
    const VARMAX_LIMIT          = 2147483647;

    /**
     * Default class name for a DB statement.
     *
     * @var string
     */
    protected $_defaultStmtClass = 'Varien_Db_Statement_Pdo_Mssql';

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
     * SQL bind params. Used temporarily by regexp callback.
     *
     * @var array
     */
    protected $_bindParams          = array();

    /**
     * Autoincrement for bind value. Used by regexp callback.
     *
     * @var int
     */
    protected $_bindIncrement       = 0;

    /**
     * Write SQL debug data to file
     *
     * @var bool
     */
    protected $_debug               = false;

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
    protected $_debugFile           = 'var/debug/pdo_mssql.log';

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
    protected $_isDdlCacheAllowed   = true;

    /**
     * Mssql Database Reserved Words
     * All words in upper case
     *
     * @var array
     */
    protected $_reservedWords       = array('ABSOLUTE', 'ACTION', 'ADA', 'ADD', 'ADMIN', 'AFTER', 'AGGREGATE', 'ALIAS',
        'ALL', 'ALLOCATE', 'ALTER', 'AND', 'ANY', 'ARE', 'ARRAY', 'AS', 'ASC', 'ASSERTION', 'AT', 'AUTHORIZATION',
        'AVG', 'BACKUP', 'BEFORE', 'BEGIN', 'BETWEEN', 'BINARY', 'BIT', 'BIT_LENGTH', 'BLOB', 'BOOLEAN', 'BOTH',
        'BREADTH', 'BREAK', 'BROWSE', 'BULK', 'BY', 'CALL', 'CASCADE', 'CASCADED', 'CASE', 'CAST', 'CATALOG', 'CHAR',
        'CHARACTER', 'CHARACTER_LENGTH', 'CHAR_LENGTH', 'CHECK', 'CHECKPOINT', 'CLASS', 'CLOB', 'CLOSE', 'CLUSTERED',
        'COALESCE', 'COLLATE', 'COLLATION', 'COLUMN', 'COMMIT', 'COMPLETION', 'COMPUTE', 'CONNECT', 'CONNECTION',
        'CONSTRAINT', 'CONSTRAINTS', 'CONSTRUCTOR', 'CONTAINS', 'CONTAINSTABLE', 'CONTINUE', 'CONVERT', 'CORRESPONDING',
        'COUNT', 'CREATE', 'CROSS', 'CUBE', 'CURRENT', 'CURRENT_DATE', 'CURRENT_PATH', 'CURRENT_ROLE', 'CURRENT_TIME',
        'CURRENT_TIMESTAMP', 'CURRENT_USER', 'CURSOR', 'CYCLE', 'DATA', 'DATABASE', 'DATE', 'DATETIME', 'DAY', 'DBCC',
        'DEALLOCATE', 'DEC', 'DECIMAL', 'DECLARE', 'DEFAULT', 'DEFERRABLE', 'DEFERRED', 'DELETE', 'DENY', 'DEPTH',
        'DEREF', 'DESC', 'DESCRIBE', 'DESCRIPTOR', 'DESTROY', 'DESTRUCTOR', 'DETERMINISTIC', 'DIAGNOSTICS',
        'DICTIONARY', 'DISCONNECT', 'DISK', 'DISTINCT', 'DISTRIBUTED', 'DOMAIN', 'DOUBLE', 'DROP', 'DUMMY', 'DUMP',
        'DYNAMIC', 'EACH', 'ELSE', 'END', 'END-EXEC', 'EQUALS', 'ERRLVL', 'ESCAPE', 'EVERY', 'EXCEPT', 'EXCEPTION',
        'EXEC', 'EXECUTE', 'EXISTS', 'EXIT', 'EXTERNAL', 'EXTRACT', 'FALSE', 'FETCH', 'FILE', 'FILLFACTOR', 'FIRST',
        'FLOAT', 'FOR', 'FOREIGN', 'FORTRAN', 'FOUND', 'FREE', 'FREETEXT', 'FREETEXTTABLE', 'FROM', 'FULL', 'FUNCTION',
        'GENERAL', 'GET', 'GLOBAL', 'GO', 'GOTO', 'GRANT', 'GROUP', 'GROUPING', 'HAVING', 'HOLDLOCK', 'HOST', 'HOUR',
        'IDENTITY', 'IDENTITYCOL', 'IDENTITY_INSERT', 'IF', 'IGNORE', 'IMAGE', 'IMMEDIATE', 'IN', 'INCLUDE', 'INDEX',
        'INDICATOR', 'INITIALIZE', 'INITIALLY', 'INNER', 'INOUT', 'INPUT', 'INSENSITIVE', 'INSERT', 'INT', 'INTEGER',
        'INTERSECT', 'INTERVAL', 'INTO', 'IS', 'ISOLATION', 'ITERATE', 'JOIN', 'KEY', 'KILL', 'LANGUAGE', 'LARGE',
        'LAST', 'LATERAL', 'LEADING', 'LEFT', 'LESS', 'LEVEL', 'LIKE', 'LIMIT', 'LINENO', 'LOAD', 'LOCAL', 'LOCALTIME',
        'LOCALTIMESTAMP', 'LOCATOR', 'LOWER', 'MAP', 'MATCH', 'MAX', 'MIN', 'MINUTE', 'MODIFIES', 'MODIFY', 'MODULE',
        'MONEY', 'MONTH', 'NAMES', 'NATIONAL', 'NATURAL', 'NCHAR', 'NCLOB', 'NEW', 'NEXT', 'NO', 'NOCHECK',
        'NONCLUSTERED', 'NONE', 'NOT', 'NTEXT', 'NULL', 'NULLIF', 'NUMERIC', 'NVARCHAR', 'OBJECT', 'OCTET_LENGTH', 'OF',
        'OFF', 'OFFSETS', 'OLD', 'ON', 'ONLY', 'OPEN', 'OPENDATASOURCE', 'OPENQUERY', 'OPENROWSET', 'OPENXML',
        'OPERATION', 'OPTION', 'OR', 'ORDER', 'ORDINALITY', 'OUT', 'OUTER', 'OUTPUT', 'OVER', 'OVERLAPS', 'PAD',
        'PARAMETER', 'PARAMETERS', 'PARTIAL', 'PASCAL', 'PATH', 'PERCENT', 'PERIOD', 'PLAN', 'POSITION', 'POSTFIX', 'PRECISION',
        'PREFIX', 'PREORDER', 'PREPARE', 'PRESERVE', 'PRIMARY', 'PRINT', 'PRIOR', 'PRIVILEGES', 'PROC', 'PROCEDURE',
        'PUBLIC', 'RAISERROR', 'READ', 'READS', 'READTEXT', 'REAL', 'RANGE', 'RECONFIGURE', 'RECURSIVE', 'REF', 'REFERENCES',
        'REFERENCING', 'RELATIVE', 'REPLICATION', 'RESTORE', 'RESTRICT', 'RESULT', 'RETURN', 'RETURNS', 'REVOKE',
        'RIGHT', 'ROLE', 'ROLLBACK', 'ROLLUP', 'ROUTINE', 'ROW', 'ROWCOUNT', 'ROWGUIDCOL', 'ROWS', 'RULE', 'SAVE',
        'SAVEPOINT', 'SCHEMA', 'SCOPE', 'SCROLL', 'SEARCH', 'SECOND', 'SECTION', 'SELECT', 'SEQUENCE', 'SESSION',
        'SESSION_USER', 'SET', 'SETS', 'SETUSER', 'SHUTDOWN', 'SIZE', 'SMALLDATETIME', 'SMALLINT', 'SMALLMONEY', 'SOME',
        'SPACE', 'SPECIFIC', 'SPECIFICTYPE', 'SQL', 'SQLCA', 'SQLCODE', 'SQLERROR', 'SQLEXCEPTION', 'SQLSTATE',
        'SQLWARNING', 'START', 'STATE', 'STATEMENT', 'STATIC', 'STATISTICS', 'STRUCTURE', 'SUBSTRING', 'SUM',
        'SYSTEM_USER', 'TABLE', 'TEMPORARY', 'TERMINATE', 'TEXT', 'TEXTSIZE', 'THAN', 'THEN', 'TIME', 'TIMESTAMP',
        'TIMEZONE_HOUR', 'TIMEZONE_MINUTE', 'TINYINT', 'TO', 'TOP', 'TRAILING', 'TRAN', 'TRANSACTION', 'TRANSLATE',
        'TRANSLATION', 'TREAT', 'TRIGGER', 'TRIM', 'TRUE', 'TRUNCATE', 'TSEQUAL', 'UNDER', 'UNION', 'UNIQUE',
        'UNIQUEIDENTIFIER', 'UNKNOWN', 'UNNEST', 'UPDATE', 'UPDATETEXT', 'UPPER', 'USAGE', 'USE', 'USER', 'USING',
        'VALUE', 'VALUES', 'VARBINARY', 'VARCHAR', 'VARIABLE', 'VARYING', 'VIEW', 'WAITFOR', 'WHEN', 'WHENEVER',
        'WHERE', 'WHILE', 'WITH', 'WITHOUT', 'WORK', 'WRITE', 'WRITETEXT', 'YEAR', 'ZONE');

    /**
     * Mssql column - Table DDL type pairs
     *
     * @var array
     */
    protected $_ddlColumnTypes      = array(
        Varien_Db_Ddl_Table::TYPE_BOOLEAN       => 'bit',
        Varien_Db_Ddl_Table::TYPE_SMALLINT      => 'smallint',
        Varien_Db_Ddl_Table::TYPE_INTEGER       => 'int',
        Varien_Db_Ddl_Table::TYPE_BIGINT        => 'bigint',
        Varien_Db_Ddl_Table::TYPE_FLOAT         => 'float',
        Varien_Db_Ddl_Table::TYPE_DECIMAL       => 'decimal',
        Varien_Db_Ddl_Table::TYPE_NUMERIC       => 'decimal',
        Varien_Db_Ddl_Table::TYPE_DATE          => 'date',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP     => 'datetime',
        Varien_Db_Ddl_Table::TYPE_TEXT          => 'text',
        Varien_Db_Ddl_Table::TYPE_BLOB          => 'text',
        Varien_Db_Ddl_Table::TYPE_VARBINARY     => 'varbinary'
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
     * Creates a PDO DSN for the adapter from $this->_config settings.
     *
     * @return string
     */
    protected function _dsn()
    {
        // remove system specific database connect settings
        unset($this->_config['active']);
        unset($this->_config['model']);
        unset($this->_config['initStatements']);
        unset($this->_config['type']);

        if ($this->_config['pdoType'] == 'sqlsrv') {
            /**
             * @link http://msdn.microsoft.com/en-US/library/ff628175(v=SQL.90).aspx
             */
            $this->_pdoType = 'sqlsrv';
            // copy config and remove pdoType
            $config = $this->_config;
            unset($config['pdoType']);

            $server = '(local)';
            if (isset($config['host'])) {
                $server = $config['host'];
                unset($config['host']);
            }
            if (isset($config['server'])) {
                $server = $this->_config['host'];
                unset($config['server']);
            }
            if (isset($config['port'])) {
                $separator = ':';
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    $separator = ',';
                }
                $server .= $separator . $config['port'];
                unset($config['port']);
            }
            $config = array('server' => $server) + $config;
            if (isset($config['dbname'])) {
                $config['database'] = $config['dbname'];
                unset($config['dbname']);
            }

            // don't pass the username and password in the DSN
            unset($config['username']);
            unset($config['password']);
            unset($config['options']);
            unset($config['persistent']);
            unset($config['driver_options']);
            unset($config['charset']);

            $dsn = array();
            // use all remaining parts in the DSN
            foreach ($config as $key => $val) {
                $dsn[] = "{$key}={$val}";
            }

            return sprintf('%s:%s', $this->_pdoType, implode(';', $dsn));
        }

        return parent::_dsn();
    }

    /**
     * Creates a PDO object and connects to the database.
     *
     * @return void
     * @throws Zend_Db_Adapter_Exception
     */
    protected function _connect()
    {
        if ($this->_connection) {
            return;
        }

        $this->_debugTimer();
        parent::_connect();

        $this->_connection->exec('SET TEXTSIZE 2147483647');
        $this->_connection->exec('SET LANGUAGE us_english');
        $this->_connection->exec('SET CONCAT_NULL_YIELDS_NULL ON');
        $this->_connection->exec('SET ANSI_NULLS ON');
        $this->_connection->exec('SET ANSI_WARNINGS ON');
        $this->_connection->exec('SET ANSI_PADDING ON');

        $this->_debugStat(self::DEBUG_CONNECT, '');
    }

    /**
     * Begin new DB transaction for connection
     *
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function beginTransaction()
    {
        if ($this->_transactionLevel === 0) {
            $this->_debugTimer();
            parent::beginTransaction();
            $this->_debugStat(self::DEBUG_TRANSACTION, 'BEGIN');
        }
        ++$this->_transactionLevel;

        return $this;
    }

    /**
     * Begin a transaction.
     *
     * @return boolean
     */
    protected function _beginTransaction()
    {
        if ($this->_pdoType == 'sqlsrv') {
            $this->_connection->beginTransaction();
            return true;
        }
        return parent::_beginTransaction();
    }

    /**
     * Commit DB transaction
     *
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function commit()
    {
        if ($this->_transactionLevel === 1) {
            $this->_debugTimer();
            parent::commit();
            $this->_debugStat(self::DEBUG_TRANSACTION, 'COMMIT');
        }
        --$this->_transactionLevel;

        return $this;
    }

    /**
     * Commit a transaction
     *
     * @return boolean
     */
    protected function _commit()
    {
        if ($this->_pdoType == 'sqlsrv') {
            $this->_connection->commit();
            return true;
        }
        return parent::_commit();
    }

    /**
     * Rollback DB transaction
     *
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function rollBack()
    {
        if ($this->_transactionLevel === 1) {
            $this->_debugTimer();
            try {
                parent::rollBack();
            } catch (PDOException $e) {
                // MS SQL Server auto rollback
                // [3903] The ROLLBACK TRANSACTION request has no corresponding BEGIN TRANSACTION
                if (!preg_match('#BEGIN TRANSACTION#', $e->getMessage())) {
                    throw $e;
                }
            } catch (Exception $e) {
                throw $e;
            }
            $this->_debugStat(self::DEBUG_TRANSACTION, 'ROLLBACK');
        }
        --$this->_transactionLevel;

        return $this;
    }

    /**
     * Roll-back a transaction.
     *
     * @return boolean
     */
    protected function _rollBack()
    {
        if ($this->_pdoType == 'sqlsrv') {
            $this->_connection->rollBack();
            return true;
        }
        return parent::_rollBack();
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
     * Retrieve DDL object for new table
     *
     * @param string $tableName the table name
     * @param string $schemaName the database or schema name
     * @return Varien_Db_Ddl_Table
     */
    public function newTable($tableName = null, $schemaName = null)
    {
        $table = new Varien_Db_Ddl_Table();
        if ($tableName !== null) {
            $table->setName($tableName);
        }
        if ($schemaName !== null) {
            $table->setSchema($schemaName);
        }
        return $table;
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
        // convert keys to upper case
        $options = array_change_key_case($options, CASE_UPPER);

        $cType      = null;
        $cNullable  = true;
        $cDefault   = false;
        $cIdentity  = false;

        // detect and validate column type
        if ($ddlType === null) {
            $ddlType = $this->_getDdlType($options);
        }

        if (empty($ddlType) || !isset($this->_ddlColumnTypes[$ddlType])) {
            throw new Varien_Exception('Invalid column definition data');
        }

        // column size
        $cType = $this->_ddlColumnTypes[$ddlType];
        switch ($ddlType) {
            case Varien_Db_Ddl_Table::TYPE_SMALLINT:
            case Varien_Db_Ddl_Table::TYPE_INTEGER:
            case Varien_Db_Ddl_Table::TYPE_BIGINT:
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
                } else {
                    $options['LENGTH'] = $this->_parseTextSize($options['LENGTH']);
                }
                if ($options['LENGTH'] <= self::VAR_LIMIT) {
                    $ddlType = 'varchar';
                    $cType   = sprintf('%s(%d)', $ddlType, $options['LENGTH']);
                } else {
                    $cType = $ddlType = 'text';
                }
                break;
            case Varien_Db_Ddl_Table::TYPE_VARBINARY:
                $ddlType = 'varbinary';
                if (empty($options['LENGTH'])) {
                    $options['LENGTH'] = Varien_Db_Ddl_Table::DEFAULT_TEXT_SIZE;
                } else {
                    $options['LENGTH'] = $this->_parseTextSize($options['LENGTH']);
                }
                $size = ($options['LENGTH'] <= self::VAR_LIMIT) ? $options['LENGTH'] : 'max';
                $cType = sprintf('%s(%s)', $ddlType, $size);
                break;
        }

        $cDefault = $this->_getDefaultValue($options);
        if (array_key_exists('NULLABLE', $options)) {
            $cNullable = (bool)$options['NULLABLE'];
        }
        if (!empty($options['IDENTITY']) || !empty($options['AUTO_INCREMENT'])) {
            $cIdentity = true;
        }

        if (isset($options['TARGET_QUERY']) && $options['TARGET_QUERY'] == 'alter') {
            $colDef =  sprintf('%s%s%s',
                $cType,
                $cNullable ? ' NULL' : ' NOT NULL',
                $cIdentity ? ' identity (1,1)' : ''
            );
        } else {
            $colDef =  sprintf('%s%s%s%s',
                $cType,
                $cNullable ? ' NULL' : ' NOT NULL',
                $cDefault !== false ? $this->quoteInto(' default ?', $cDefault) : '',
                $cIdentity ? ' identity (1,1)' : ''
            );
        }

        return $colDef;
    }

    /**
     * Get default value by column options
     *
     * @param array $options
     * @return bool|string
     * @throws Varien_Exception
     */
    protected function _getDefaultValue($options)
    {
        // detect and validate column type
        $ddlType = $this->_getDdlType($options);

        if (empty($ddlType) || !isset($this->_ddlColumnTypes[$ddlType])) {
            throw new Varien_Exception('Invalid column definition data');
        }

        $cDefault = false;
        if (array_key_exists('DEFAULT', $options)) {
            $cDefault = $options['DEFAULT'];
        }
        if (array_key_exists('NULLABLE', $options)) {
            $cNullable = (bool)$options['NULLABLE'];
        } else {
            $cNullable = false;
        }

        // prepare default value string
        if ($ddlType == Varien_Db_Ddl_Table::TYPE_TIMESTAMP) {
            if ($cDefault === null) {
                $cDefault = new Zend_Db_Expr('NULL');
            } elseif ($cDefault == Varien_Db_Ddl_Table::TIMESTAMP_INIT) {
                $cDefault = new Zend_Db_Expr('(getdate())');
            } else if ($cDefault == Varien_Db_Ddl_Table::TIMESTAMP_UPDATE) {
                $cDefault = new Zend_Db_Expr('/*0 ON UPDATE CURRENT_TIMESTAMP*/');
            } else if ($cDefault == Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE) {
                $cDefault = new Zend_Db_Expr('(getdate())/*CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP*/');
            } else {
                $cDefault = false;
            }
        } elseif (($cDefault === null) && $cNullable) {
            $cDefault = new Zend_Db_Expr('NULL');
        }

        return $cDefault;
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
            $columnDefinition = $this->_getColumnDefinition($columnData);

            if ($columnData['PRIMARY']) {
                $primary[$columnData['COLUMN_NAME']] = $columnData['PRIMARY_POSITION'];
            }

            $definition[] = sprintf('  %s %s',
                $this->quoteIdentifier($columnData['COLUMN_NAME']),
                $columnDefinition
            );
        }

        // PRIMARY KEY
        if (!empty($primary)) {
            asort($primary, SORT_NUMERIC);
            $primary = array_map(array($this, 'quoteIdentifier'), array_keys($primary));
            $definition[] = sprintf('  PRIMARY KEY (%s)', implode(', ', $primary));
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
                $definition[] = sprintf('  CONSTRAINT [%s] FOREIGN KEY (%s) REFERENCES %s (%s) ',
                    $this->quoteIdentifier($fkData['FK_NAME']),
                    $this->quoteIdentifier($fkData['COLUMN_NAME']),
                    $this->quoteIdentifier($fkData['REF_TABLE_NAME']),
                    $this->quoteIdentifier($fkData['REF_COLUMN_NAME'])
                );
            }
        }

        return $definition;
    }

    /**
     * Disable all table constraints
     *
     * @param string $tableName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    public function disableTableConstraints($tableName, $schemaName)
    {
        $tableName = $this->quoteIdentifier($this->_getTableName($tableName, $schemaName));
        $this->raw_query(sprintf('ALTER TABLE %s NOCHECK CONSTRAINT ALL', $tableName));
        return $this;
    }

    /**
     * Enable all table constraints
     *
     * @param string $tableName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    public function enableTableConstraints($tableName, $schemaName)
    {
        $tableName = $this->quoteIdentifier($this->_getTableName($tableName, $schemaName));
        $this->raw_query(sprintf('ALTER TABLE %s CHECK CONSTRAINT ALL', $tableName));
        return $this;
    }

    /**
     * Disable all Db constraints
     *
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    public function disableAllDbConstraints()
    {
        $this->raw_query("sp_MSforeachtable 'ALTER TABLE ? NOCHECK CONSTRAINT ALL'");
        return $this;
    }

    /**
     * Enable all Db table constraints
     *
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    public function enableAllDbConstraints()
    {
        $this->raw_query("sp_MSforeachtable 'ALTER TABLE ? CHECK CONSTRAINT ALL'");
        return $this;
    }

    /**
     * Retrieve table indexes definition array for create table
     *
     * @param Varien_Db_Ddl_Table $table
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    protected function _createIndexes(Varien_Db_Ddl_Table $table)
    {
        $indexes    = $table->getIndexes();

        if (!empty($indexes)) {
            foreach ($indexes as $indexData) {
                if (strtolower($indexData['TYPE']) == Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE) {
                    continue;
                }
                $columns = array();
                foreach ($indexData['COLUMNS'] as $columnData) {
                    $columns[] = $columnData['NAME'];
                }
                $this->addIndex($this->quoteIdentifier($table->getName()),
                    $indexData['INDEX_NAME'],
                    $columns,
                    $indexData['TYPE']);
           }
        }

        return $this;
    }

    /**
     * Create FOREIGN KEY CASCADE actions
     *
     * @param Varien_Db_Ddl_Table $table
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    protected function _createForeignKeysActions(Varien_Db_Ddl_Table $table)
    {
        $foreignKeys = $table->getForeignKeys();
        $fkActions   = array(Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_SET_NULL);

        if (!empty($foreignKeys)) {
            foreach ($foreignKeys as $fkData) {
                if (in_array($fkData['ON_DELETE'], $fkActions)) {
                    $this->_addForeignKeyDeleteAction(
                        $table->getName(),
                        $fkData['COLUMN_NAME'],
                        $fkData['REF_TABLE_NAME'],
                        $fkData['REF_COLUMN_NAME'],
                        $fkData['ON_DELETE']
                    );

                    $this->_addExtendProperty(
                        array('table' => $table->getName(), 'constraint' => $fkData['FK_NAME']),
                        $fkData['ON_DELETE'],
                        self::EXTPROP_COMMENT_FK_DELETE
                    );
                }
            }
        }

        return $this;
    }

    /**
     * Retrieve table unique constraints definition array for create table
     *
     * @param Varien_Db_Ddl_Table $table
     * @return array
     */
    protected function _getUniqueConstraintsDefinition(Varien_Db_Ddl_Table $table)
    {
        $definition  = array();
        $constraints = $table->getIndexes();

        if (!empty($constraints)) {
            foreach ($constraints as $constraintData) {
                if (strtolower($constraintData['TYPE']) != Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE) {
                    continue;
                }
                $columns = array();
                foreach ($constraintData['COLUMNS'] as $columnData) {
                    $column = $this->quoteIdentifier($columnData['NAME']);
                    $columns[] = $column;
                }
                $definition[] = sprintf(' CONSTRAINT "%s" UNIQUE (%s)',
                    $this->quoteIdentifier($constraintData['INDEX_NAME']),
                    implode(', ', $columns));
            }
        }

        return $definition;
    }
    /**
     * Create table from DDL object
     *
     * @param Varien_Db_Ddl_Table $table
     * @throws Zend_Db_Exception
     * @return Zend_Db_Statement_Interface
     */
    public function createTable(Varien_Db_Ddl_Table $table)
    {
        $columns = $table->getColumns();
        foreach ($columns as $columnEntry) {
            if (empty($columnEntry['COMMENT'])) {
                throw new Zend_Db_Exception("Cannot create table without columns comments");
            }
        }

        $sqlFragment    = array_merge(
            $this->_getColumnsDefinition($table),
            $this->_getUniqueConstraintsDefinition($table),
            $this->_getForeignKeysDefinition($table)
        );


        $sql = sprintf("CREATE TABLE %s (\n%s\n)",
            $this->quoteIdentifier($table->getName()),
            implode(",\n", $sqlFragment));

        $result = $this->query($sql);

        $this->_createIndexes($table);

        $this->_createForeignKeysActions($table);

        if ($table->getComment()) {
            $this->_addTableComment($table->getName(), $table->getComment());
        } else {
            throw new Zend_Db_Exception("Cannot create table without comment");
        }

        foreach ($columns as $columnEntry) {
            $this->_addColumnComment($table->getName(), $columnEntry['COLUMN_NAME'], $columnEntry['COMMENT']);
        }

        return $result;
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
        if (!$this->isTableExists($tableName)) {
            return true;
        }
        // drop foreign key and cascade packages
        $foreignKeys = $this->getForeignKeys($tableName, $schemaName);

        foreach ($foreignKeys as $foreignKey) {
            $this->dropForeignKey($tableName, $foreignKey['FK_NAME'], $schemaName);
        }

        $query = sprintf('DROP TABLE %s',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName))
        );
        $this->query($query);

        return true;
    }

    /**
     * Truncate a table
     *
     * @param string $tableName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    public function truncateTable($tableName, $schemaName = null)
    {
        if (!$this->isTableExists($tableName, $schemaName)) {
            throw new Varien_Exception(sprintf('Table "%s" is not exists', $tableName));
        }

        $query = sprintf('TRUNCATE TABLE %s',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)));
        $this->query($query);

        return true;
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
     * Returns short table status array
     *
     * @param string $tableName
     * @param string $schemaName
     * @return array|false
     */
    public function showTableStatus($tableName, $schemaName = null)
    {
        $sqlShowTableStatus = "
            SELECT name, id, xtype, uid, info, status, base_schema_ver, replinfo,
                parent_obj, crdate, ftcatid, schema_ver, stats_schema_ver, type,
                userstat, sysstat, indexdel, refdate, version, deltrig, instrig,
                updtrig, seltrig, category, cache
            FROM dbo.sysobjects
            WHERE id = object_id(N'%s')
                AND OBJECTPROPERTY(id, N'IsUserTable') = 1";

        $query = sprintf($sqlShowTableStatus,
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)));

        return $this->raw_fetchRow($query);
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
     * PRIMARY_AUTO     => integer; position of auto-generated column in primary key
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
            foreach ($ddl as &$columnProp) {
                //Prepare default value
                $matches = array();
                preg_match('/^(\(*\'+(.*)\'+\)*)/', $columnProp['DEFAULT'], $matches);
                if (!empty($matches) && isset($matches[2])) {
                    $columnProp['DEFAULT'] = $matches[2];
                }
                if ($columnProp['DEFAULT'] == '(NULL)') {
                    $columnProp['DEFAULT'] = null;
                }
            }
            $this->saveDdlCache($cacheKey, self::DDL_DESCRIBE, $ddl);
        }

        return $ddl;
    }

    /**
     * Create Varien_Db_Ddl_Table object by data from describe table
     *
     * @param $tableName
     * @param $newTableName
     * @return Varien_Db_Ddl_Table
     */
    public function createTableByDdl($tableName, $newTableName)
    {
        $describe = $this->describeTable($tableName);
        $table = $this->newTable($newTableName)
            ->setComment(ucwords(str_replace('_', ' ', $newTableName)));
        foreach ($describe as $columnData) {
            $type = $this->_getColumnTypeByDdl($columnData);
            $options = array();
            if ($columnData['IDENTITY'] === true) {
                $options['identity']  = true;
            }
            if ($columnData['UNSIGNED'] === true) {
                $options['unsigned']  = true;
            }
            if ($columnData['NULLABLE'] === false
                && !($type == Varien_Db_Ddl_Table::TYPE_TEXT
                && strlen($columnData['DEFAULT']) != 0)
            ) {
                $options['nullable'] = false;
            }
            if ($columnData['PRIMARY'] === true) {
                $options['primary'] = true;
            }
            if (($columnData['DEFAULT'] !== null)
                && $type != Varien_Db_Ddl_Table::TYPE_TEXT
                ) {
                $options['default'] = $this->quote($columnData['DEFAULT']);
            }
            if (strlen($columnData['SCALE']) > 0) {
                $options['scale'] = $columnData['SCALE'];
            }
            if (strlen($columnData['PRECISION']) > 0) {
                $options['precision'] = $columnData['PRECISION'];
            }
            $comment = ucwords(str_replace('_', ' ', $columnData['COLUMN_NAME']));
            $table->addColumn($columnData['COLUMN_NAME'], $type, $columnData['LENGTH'], $options, $comment);
        }

        $indexes = $this->getIndexList($tableName);
        foreach ($indexes as $indexData) {
            if ($indexData['KEY_NAME'] == 'PRIMARY') {
                continue;
            }

            $fields    = $indexData['COLUMNS_LIST'];
            $options   = array();
            $indexType = '';
            if ($indexData['INDEX_TYPE'] == Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE) {
                $options   = array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE);
                $indexType = Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE;
            }
            $table->addIndex($this->getIndexName($newTableName, $fields, $indexType), $fields, $options);
        }

        $foreignKeys = $this->getForeignKeys($tableName);
        foreach ($foreignKeys as $keyData) {
            $fkName = $this->getForeignKeyName(
                $newTableName, $keyData['COLUMN_NAME'], $keyData['REF_TABLE_NAME'], $keyData['REF_COLUMN_NAME']
            );
            $onDelete = '';
            if ($keyData['ON_DELETE'] == 'CASCADE') {
                $onDelete = Varien_Db_Ddl_Table::ACTION_CASCADE;
            } else if ($keyData['ON_DELETE'] == 'SET NULL') {
                $onDelete = Varien_Db_Ddl_Table::ACTION_SET_NULL;
            } else if ($keyData['ON_DELETE'] == 'RESTRICT') {
                $onDelete = Varien_Db_Ddl_Table::ACTION_RESTRICT;
            } else {
                $onDelete = Varien_Db_Ddl_Table::ACTION_NO_ACTION;
            }
            $onUpdate = '';
            if ($keyData['ON_UPDATE'] == 'CASCADE') {
               $onUpdate = Varien_Db_Ddl_Table::ACTION_CASCADE;
            } else if ($keyData['ON_UPDATE'] == 'SET NULL') {
               $onUpdate = Varien_Db_Ddl_Table::ACTION_SET_NULL;
            } else if ($keyData['ON_UPDATE'] == 'RESTRICT') {
               $onUpdate = Varien_Db_Ddl_Table::ACTION_RESTRICT;
            } else {
               $onUpdate = Varien_Db_Ddl_Table::ACTION_NO_ACTION;
            }
            $table->addForeignKey(
                $fkName, $keyData['COLUMN_NAME'], $keyData['REF_TABLE_NAME'],
                $keyData['REF_COLUMN_NAME'], $onDelete, $onUpdate
            );
        }

        return $table;
    }

    /**
     * Modify the column definition by data from describe table
     *
     * @param string $tableName
     * @param string $columnName
     * @param array $definition
     * @param boolean $flushData
     * @param string $schemaName
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    public function modifyColumnByDdl($tableName, $columnName, $definition, $flushData = false, $schemaName = null)
    {
        $definition = array_change_key_case($definition, CASE_UPPER);
        $definition['COLUMN_TYPE'] = $this->_getColumnTypeByDdl($definition);
        //TODO need to be deleted. modifyColumn() uses _getDefaultValue()
        if (array_key_exists('DEFAULT', $definition) && is_null($definition['DEFAULT'])) {
            unset($definition['DEFAULT']);
        }
        return $this->modifyColumn($tableName, $columnName, $definition, $flushData, $schemaName);
    }

    /**
     * Retrieve column data type by data from describe table
     *
     * @param array $column
     * @return string
     */
    protected function _getColumnTypeByDdl($column)
    {
        switch ($column['DATA_TYPE']) {
            case 'int':
                return Varien_Db_Ddl_Table::TYPE_INTEGER;
            case 'varchar':
                return Varien_Db_Ddl_Table::TYPE_TEXT;
            case 'text':
                return Varien_Db_Ddl_Table::TYPE_TEXT;
            case 'datetime':
                return Varien_Db_Ddl_Table::TYPE_TIMESTAMP;
            case 'decimal':
                return Varien_Db_Ddl_Table::TYPE_DECIMAL;
            case 'float':
                return Varien_Db_Ddl_Table::TYPE_FLOAT;
            case 'bigint':
                return Varien_Db_Ddl_Table::TYPE_BIGINT;
            case 'smallint':
                return Varien_Db_Ddl_Table::TYPE_SMALLINT;
        }
    }

    /**
     * Rename a table
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

        $oldTable = $this->quoteIdentifier($this->_getTableName($oldTableName, $schemaName));
        $newTable = $this->quoteIdentifier($this->_getTableName($newTableName, $schemaName));

        $query = sprintf('EXEC SP_RENAME %s , %s', $oldTable, $newTable);
        $this->raw_query($query);

        $this->resetDdlCache($oldTableName, $schemaName);

        return true;
    }

    /**
     * Adds new column to the table.
     *
     * Generally $defintion must be array with column data to keep this call cross-DB compatible.
     * Using string as $definition is allowed only for concrete DB adapter.
     * Adds primary key if needed
     *
     * @param string $tableName
     * @param string $columnName
     * @param array|string $definition  string specific or universal array DB Server definition
     * @param string $schemaName
     * @return Varien_Db_Adapter_Pdo_Mssql
     * @throws Zend_Db_Exception
     */
    public function addColumn($tableName, $columnName, $definition, $schemaName = null)
    {
        if ($this->tableColumnExists($tableName, $columnName, $schemaName)) {
            return true;
        }

        $comment = null;
        $primaryKey = '';
        if (is_array($definition)) {
            // Retrieve comment to set it later
            $definition = array_change_key_case($definition, CASE_UPPER);
            if (empty($definition['COMMENT'])) {
                throw new Zend_Db_Exception("Impossible to create a column without comment.");
            }
            $comment = $definition['COMMENT'];

            if (!empty($definition['PRIMARY'])) {
                $primaryKey = ' PRIMARY KEY';
            }

            $definition = $this->_getColumnDefinition($definition);
        }

        $realTableName = $this->_getTableName($tableName, $schemaName);
        $sql = sprintf('ALTER TABLE %s ADD %s %s %s',
            $this->quoteIdentifier($realTableName),
            $this->quoteIdentifier($columnName),
            $definition,
            $primaryKey
        );

        $result = $this->raw_query($sql);

        if (!empty($comment)) {
            $this->_addColumnComment($realTableName, $columnName, $comment);
        }
        $this->resetDdlCache($tableName, $schemaName);

        return $result;
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
     * @param boolean $flushData        flush table statistic for MyS
     * @param string $schemaName
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    public function changeColumn($tableName, $oldColumnName, $newColumnName, $definition, $flushData = false,
        $schemaName = null)
    {
        $this->_renameColumn($tableName, $oldColumnName, $newColumnName, $schemaName)
             ->modifyColumn($tableName, $newColumnName, $definition, $flushData, $schemaName);

        $definition = array_change_key_case($definition, CASE_UPPER);
        if (!empty($definition['COMMENT'])) {
            $this->_addColumnComment($tableName, $newColumnName, $definition['COMMENT']);
        }

        return $this;
    }

    /**
     * Rename column
     *
     * @param string $tableName
     * @param string $oldColumnName
     * @param string $newColumnName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Pdo_Mssql
     * @throws Zend_Db_Exception
     */
    protected function _renameColumn($tableName, $oldColumnName, $newColumnName, $schemaName = null)
    {
        if ($oldColumnName == $newColumnName) {
            return $this;
        }

        if (!$this->tableColumnExists($tableName, $oldColumnName, $schemaName)) {
            throw new Zend_Db_Exception(sprintf('Column "%s" does not exists on table "%s"', $oldColumnName, $tableName));
        }

        if ($this->tableColumnExists($tableName, $newColumnName, $schemaName)) {
            throw new Zend_Db_Exception(sprintf('Column "%s" already exists on table "%s"', $newColumnName, $tableName));
        }

        $sql = sprintf("EXEC SP_RENAME '%s.%s', '%s'",
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
            $this->quoteIdentifier($oldColumnName),
            $this->quoteIdentifier($newColumnName));

        $result = $this->raw_query($sql);

        $this->resetDdlCache($tableName, $schemaName);

        return $result;
    }

    /**
     * Get constraint name for column
     *
     * @param string $tableName
     * @param string $columnName
     * @return bool|string
     */
    protected function _getDefaultConstraint($tableName, $columnName)
    {
        $defaultConstraintQuery = "SELECT d.name AS constraint_name
            FROM sys.default_constraints d
            INNER JOIN sys.all_columns c ON d.parent_object_id = c.object_id
            AND d.parent_column_id = c.column_id
            WHERE c.object_id =  object_id('%s')
            AND c.name = '%s'";

        $query = sprintf($defaultConstraintQuery, $tableName, $columnName);
        return $this->raw_fetchRow($query);

    }

    /**
     * Add default value when column is modified
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $defaultValue
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    protected function _addColumnDefaultValue($tableName, $columnName, $defaultValue)
    {
        $constraintName = strtoupper('PF__' . $tableName . '_' . $columnName);
        $query = sprintf("ALTER TABLE %s ADD CONSTRAINT %s DEFAULT %s FOR %s",
            $tableName, $constraintName, $this->quote($defaultValue), $columnName);
        $this->raw_query($query);

        return $this;
    }

    /**
     * Modify the column definition
     *
     * @param string $tableName
     * @param string $columnName
     * @param array|string $definition
     * @param boolean $flushData
     * @param string $schemaName
     * @return Varien_Db_Adapter_Pdo_Mssql
     * @throws Zend_Db_Exception
     */
    public function modifyColumn($tableName, $columnName, $definition, $flushData = false, $schemaName = null)
    {
        if (!$this->tableColumnExists($tableName, $columnName, $schemaName)) {
            throw new Zend_Db_Exception(sprintf('Column "%s" does not exists on table "%s"', $columnName, $tableName));
        }

        $constraint = $this->_getDefaultConstraint($tableName, $columnName);
        if ($constraint) {
            $query = sprintf("ALTER TABLE %s DROP CONSTRAINT %s",
                $tableName, $this->quoteIdentifier($constraint['constraint_name'])
            );
            $this->raw_query($query);
        }

        $defaultValue = false;
        if (is_array($definition)) {
            // convert keys to upper case
            $definition = array_change_key_case($definition, CASE_UPPER);

            $definition['TARGET_QUERY'] = 'alter';
            $defaultValue = $this->_getDefaultValue($definition);
            $definition   = $this->_getColumnDefinition($definition);
        }

        $sql = sprintf('ALTER TABLE %s ALTER COLUMN %s %s',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
            $this->quoteIdentifier($columnName),
            $definition);

        $this->raw_query($sql);
        if ($defaultValue !== false) {
            $this->_addColumnDefaultValue($tableName, $columnName, $defaultValue);
        }
        $this->resetDdlCache($tableName, $schemaName);

        return $this;
    }

    /**
     * Drop the column from table
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $schemaName
     * @return boolean
     */
    public function dropColumn($tableName, $columnName, $schemaName = null)
    {
        if (!$this->tableColumnExists($tableName, $columnName, $schemaName)) {
            return true;
        }

        $default = $this->_getDefaultConstraint($tableName, $columnName);
        if ($default) {
            $query = sprintf("ALTER TABLE %s DROP CONSTRAINT %s",
                $tableName, $this->quoteIdentifier($default['constraint_name'])
            );
            $this->raw_query($query);
        }

        $alterDrop   = array();
        $foreignKeys = $this->getForeignKeys($tableName, $schemaName);
        foreach ($foreignKeys as $fkProp) {
            if ($fkProp['COLUMN_NAME'] == $columnName) {
                $alterDrop[] = sprintf('DROP CONSTRAINT [%s]', $this->quoteIdentifier($fkProp['FK_NAME']));
            }
        }

        $alterDrop[] = sprintf('DROP COLUMN %s', $this->quoteIdentifier($columnName));

        $sql = sprintf('ALTER TABLE %s %s',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
            implode(', ', $alterDrop));

        $result = $this->raw_query($sql);

        $this->resetDdlCache($tableName, $schemaName);

        return $result;
    }

    /**
     * Check is table column exists
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
     * Return Ddl script for drop fulltext index
     *
     * @param string $tableName
     * @param string $schemaName
     * @return string
     */
    protected function _getDdlScriptDropFullText($tableName, $schemaName = null)
    {
        return sprintf('DROP FULLTEXT INDEX ON table_name %s',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)));
    }

    /**
     * Return Ddl script for create fulltext index
     *
     * @param string $tableName
     * @param string $indexName
     * @param string $fields        the quoted fields list
     * @param string schemaName
     * @return boolean
     * @throws Zend_Db_Exception
     */
    protected function _getDdlScriptCreateFullText($tableName, $fields, $schemaName = null)
    {
        $primaryKey = false;
        foreach ($this->getIndexList($tableName, $schemaName) as $index) {
            if ($index['INDEX_TYPE'] == Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY) {
                $primaryKey = $index['KEY_NAME'];
            }
        }

        if (!$primaryKey) {
            throw new Zend_Db_Exception('Cannot create full text index for table without primary key');
        }

        return sprintf('CREATE FULLTEXT INDEX ON %s (%s) KEY INDEX %s',
            $this->_getTableName($tableName, $schemaName), $fields,
            $this->quoteIdentifier($primaryKey),
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName))
        );
    }

    /**
     * Return Ddl script for drop primary key
     *
     * @param string $tableName
     * @param string $indexName
     * @param string $fields    the quoted fields list
     * @param string schemaName
     * @return string
     */
    protected function _getDdlScriptDropPrimaryKey($tableName, $indexName, $schemaName = null)
    {
        return sprintf('ALTER TABLE %s DROP CONSTRAINT [%s]',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
            $this->quoteIdentifier($indexName));
    }

    /**
     * Return Ddl script for create primary key
     *
     * @param string $tableName
     * @param string $indexName
     * @param string $fields        the quoted fields list
     * @param string schemaName
     * @return string
     */
    protected function _getDdlScriptCreatePrimaryKey($tableName, $indexName, $fields, $schemaName = null)
    {
        return sprintf('ALTER TABLE %s ADD CONSTRAINT [%s] PRIMARY KEY CLUSTERED (%s)',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
            $this->quoteIdentifier($indexName),
            $fields);
    }

    /**
     * Return Ddl script for drop index
     *
     * @param string $tableName
     * @param string $indexName
     * @param string schemaName
     * @return string
     */
    protected function _getDdlScriptDropIndex($tableName, $indexName, $schemaName = null)
    {
        return sprintf('DROP INDEX %s ON %s',
            $this->quoteIdentifier($indexName),
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName))
        );
    }

    /**
     * Return Ddl script for create index
     *
     * @param string $tableName
     * @param string $indexName
     * @param string $fields    the quoted fields list
     * @param boolean $isUniqueIndex
     * @param string schemaName
     * @return string
     */
    protected function _getDdlScriptCreateIndex($tableName, $indexName, $fields, $isUniqueIndex, $schemaName = null)
    {
        $table = $this->quoteIdentifier($this->_getTableName($tableName, $schemaName));
        $index = $this->quoteIdentifier($indexName);

        if ($isUniqueIndex) {
            $query = sprintf('ALTER TABLE %s ADD CONSTRAINT [%s] UNIQUE (%s)',
                $table, $index, $fields);
        } else {
            $query = sprintf('CREATE INDEX [%s] ON %s (%s)',
                $index, $table, $fields);
        }
        return $query;
    }

    /**
     * Add new index to table name
     *
     * @param string $tableName
     * @param string $indexName
     * @param string $fields        the validated and queted columns list (SQL)
     * @param string $indexType     the index type
     * @param string $schemaName
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    protected function _createIndex($tableName, $keyName, $indexType, $fields, $schemaName = null)
    {
        switch (strtolower($indexType)) {
            case Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY:
                $query = $this->_getDdlScriptCreatePrimaryKey($tableName, $keyName, $fields, $schemaName);
                break;

            case Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE:
                $query = $this->_getDdlScriptCreateIndex($tableName, $keyName, $fields, true, $schemaName);
                break;

            case Varien_Db_Adapter_Interface::INDEX_TYPE_FULLTEXT:
                $query = $this->_getDdlScriptCreateFullText($tableName, $fields, $schemaName);
                break;

            default:
                $query = $this->_getDdlScriptCreateIndex($tableName, $keyName, $fields, false, $schemaName);
                break;
        }
        $this->raw_query($query);

        $this->resetDdlCache($tableName, $schemaName);

        return $this;
    }

    /**
     * Add new index to table name
     *
     * @param string $tableName
     * @param string $indexName
     * @param string|array $fields  the table column name or array of ones
     * @param string $indexType     the index type
     * @param string $schemaName
     * @return Varien_Db_Adapter_Pdo_Mssql
     * @throws Zend_Db_Exception
     */
    public function addIndex($tableName, $indexName, $fields,
        $indexType = Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX, $schemaName = null)
    {
        $columns = array();
        foreach ($this->describeTable($tableName, $schemaName) as $column) {
            $columns[$column['COLUMN_NAME']] = $column['COLUMN_NAME'];
        }

        if (!is_array($fields)) {
            $fields = array($fields);
        }

        $fieldSql = array();
        foreach ($fields as $field) {
            if (!isset($columns[$field])) {
                $msg = sprintf('There is no field "%s" that you are trying to create an index on "%s"',
                    $field,
                    $tableName
                );
                throw new Zend_Db_Exception($msg);
            }
            $fieldSql[] = $this->quoteIdentifier($field);
        }
        $fieldSql = implode(',', $fieldSql);

        $keyList = $this->getIndexList($tableName, $schemaName);

        // Drop index if exists
        foreach($keyList as $key) {
            if ($key['KEY_NAME'] == strtoupper($indexName)) {
                $this->dropIndex($tableName, $indexName, $schemaName);
            }
        }

        // Create index
        $this->_createIndex($tableName, $indexName, $indexType, $fieldSql, $schemaName);

        return $this;
    }

    /**
     * Drop the index from table
     *
     * @param string $tableName
     * @param string $keyName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    public function dropIndex($tableName, $keyName, $schemaName = null)
    {
        $indexList = $this->getIndexList($tableName, $schemaName);
        $keyName = strtoupper($keyName);
        $indexExists = false;

        foreach($indexList as $index) {
            if ($index['KEY_NAME'] == $keyName) {
                $keyType = $index['INDEX_TYPE'];
                $indexExists = true;
                break;
            }
        }

        if (!$indexExists) {
            return $this;
        }

        switch ($keyType) {
            case Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE:
            case Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY:
                $query = $this->_getDdlScriptDropPrimaryKey($tableName, $keyName, $schemaName);
                break;
            case Varien_Db_Adapter_Interface::INDEX_TYPE_FULLTEXT:
                $query = $this->_getDdlScriptDropFullText($tableName, $schemaName);
                break;
            default:
                $query = $this->_getDdlScriptDropIndex($tableName, $keyName, $schemaName);
                break;
        }

        $this->raw_query($query);

        $this->resetDdlCache($tableName, $schemaName);

        return $this;
    }

    /**
     * Returns the table index information
     *
     * The return value is an associative array keyed by the UPPERCASE index key (except for primary key,
     * that is always stored under 'PRIMARY' key) as returned by the RDBMS.
     *
     * The value of each array element is an associative array
     * with the following keys:
     *
     * SCHEMA_NAME      => string; name of database or schema
     * TABLE_NAME       => string; name of the table
     * KEY_NAME         => string; the original index name
     * COLUMNS_LIST     => array; array of index column names
     * INDEX_TYPE       => string; lowercase, create index type
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
        $ddl      = $this->loadDdlCache($cacheKey, self::DDL_INDEX);

        if ($ddl === false) {
            $ddl = array();
            $query = "
                SELECT
                    UPPER(si.name)                 AS Key_name,
                    CASE
                        WHEN is_unique = 1 AND  (is_unique_constraint = 1 OR is_primary_key = 1) THEN 0
                        ELSE 1
                    END                     AS Non_unique,
                    sc.name                 AS Column_name,
                    CASE
                        WHEN ( si.type = 1 AND si.is_primary_key = 1 ) then 'primary'
                        WHEN ( si.type = 2 AND si.is_unique = 1 AND si.is_unique_constraint = 1 ) then 'unique'
                        WHEN ( si.type = 2 AND is_unique_constraint = 0 ) then 'index'
                        ELSE '??'
                    END                     AS Index_type
                FROM sys.sysobjects so
                INNER JOIN sys.indexes si ON si.object_id = so.id
                INNER JOIN sys.index_columns sic ON sic.object_id = so.id
                    AND sic.index_id = si.index_id
                INNER JOIN sys.columns sc ON sc.object_id = so.id
                    AND sc.column_id = sic.column_id
                WHERE si.type IN (1, 2)
                    AND so.name = '%s'";
            $sql = sprintf($query,
                $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)));

            foreach ($this->fetchAll($sql) as $row) {
                $fieldKeyName   = 'Key_name';
                $fieldColumn    = 'Column_name';
                $fieldIndexType = 'Index_type';

                $indexType = $row[$fieldIndexType];
                switch (strtolower($indexType)) {
                    case Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY:
                          $upperKeyName = strtoupper(Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY);
                          break;
                    default:
                        $upperKeyName = strtoupper($row[$fieldKeyName]);
                        break;
                }

                if (isset($ddl[$upperKeyName])) {
                    $ddl[$upperKeyName]['fields'][] = $row[$fieldColumn]; // for compatible
                    $ddl[$upperKeyName]['COLUMNS_LIST'][] = $row[$fieldColumn];
                } else {
                    /**
                     * @todo index_method
                     */
                    $ddl[$upperKeyName] = array(
                        'SCHEMA_NAME'   => $schemaName,
                        'TABLE_NAME'    => $tableName,
                        'KEY_NAME'      => $row[$fieldKeyName],
                        'COLUMNS_LIST'  => array($row[$fieldColumn]),
                        'INDEX_TYPE'    => strtolower($indexType),
                        'INDEX_METHOD'  => strtoupper($indexType),
                        'type'          => strtolower($indexType), // for compatibility
                        'fields'        => array($row[$fieldColumn]) // for compatibility
                    );
                }
            }
            $this->saveDdlCache($cacheKey, self::DDL_INDEX, $ddl);
        }

        return $ddl;
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
    public function purgeOrphanRecords($tableName, $columnName, $refTableName, $refColumnName, $onDelete = Varien_Db_Adapter_Interface::FK_ACTION_CASCADE)
    {
        // quote table and column
        $tableName      = $this->quoteIdentifier($tableName);
        $refTableName   = $this->quoteIdentifier($refTableName);
        $columnName     = $this->quoteIdentifier($columnName);
        $refColumnName  = $this->quoteIdentifier($refColumnName);

        $sql = '';
        if (strtoupper($onDelete) == Varien_Db_Adapter_Interface::FK_ACTION_CASCADE ||
            strtoupper($onDelete) == Varien_Db_Adapter_Interface::FK_ACTION_RESTRICT)
        {
            $sql = " UPDATE {$tableName} t1 SET t1.code = NULL ";
        } elseif (strtoupper($onDelete) == 'SET NULL') {
            $sql = " DELETE FROM {$tableName} t1";
        }

        $sql .= " WHERE NOT EXISTS("
            . " SELECT 1 "
            . " FROM {$refTableName} t2 "
            . " WHERE t2.{$refColumnName} = t1.{$columnName})";

        $this->raw_query($sql);

        return $this;
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
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    public function addForeignKey($fkName, $tableName, $columnName, $refTableName, $refColumnName,
        $onDelete = Varien_Db_Adapter_Interface::FK_ACTION_CASCADE,
        $onUpdate = Varien_Db_Adapter_Interface::FK_ACTION_CASCADE,
        $purge = false, $schemaName = null, $refSchemaName = null)
    {
        $this->dropForeignKey($tableName, $fkName, $schemaName);

        if ($purge) {
            $this->purgeOrphanRecords($tableName, $columnName, $refTableName, $refColumnName, $onDelete);
        }

        $query = sprintf('ALTER TABLE %s ADD CONSTRAINT [%s] FOREIGN KEY (%s) REFERENCES %s (%s)',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
            $this->quoteIdentifier($fkName),
            $this->quoteIdentifier($columnName),
            $this->quoteIdentifier($this->_getTableName($refTableName, $refSchemaName)),
            $this->quoteIdentifier($refColumnName)
        );

        $fkActions = array(Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_SET_NULL);

        if (in_array($onDelete, $fkActions)) {
            $this->_addForeignKeyDeleteAction($tableName, $columnName, $refTableName, $refColumnName, $onDelete);
        }

        $this->resetDdlCache($tableName, $schemaName);
        $result = $this->raw_query($query);

        $this->_addExtendProperty(
            array('table' => $tableName, 'CONSTRAINT' => $fkName), $onDelete, self::EXTPROP_COMMENT_FK_DELETE);
        $this->_addExtendProperty(
            array('table' => $tableName, 'CONSTRAINT' => $fkName), $onUpdate, self::EXTPROP_COMMENT_FK_UPDATE);

        return $result;

    }

    /**
     * Drop the Foreign Key from table
     *
     * @param string $tableName
     * @param string $fkName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    public function dropForeignKey($tableName, $fkName, $schemaName = null)
    {
        $foreignKeys = $this->getForeignKeys($tableName, $schemaName);

        $upperFkName = strtoupper($fkName);
        if (!isset($foreignKeys[$upperFkName])) {
            return $this;
        }

        foreach ($foreignKeys as $foreignKey) {
            if ($fkName == $foreignKey['FK_NAME']) {
                $query  = sprintf("IF  EXISTS (SELECT * FROM sys.triggers "
                    . "WHERE object_id = OBJECT_ID(N'%s'))\n"
                    . " DROP TRIGGER [%s]",
                    $this->_getTriggerName($tableName, self::TRIGGER_CASCADE_DEL),
                    $this->_getTriggerName($tableName, self::TRIGGER_CASCADE_DEL)
                );
                $this->query($query);
            }
        }

        if (isset($foreignKeys[$upperFkName])) {
            $this->_dropDependTriggersAction($foreignKeys[$upperFkName]['TABLE_NAME'],
                $foreignKeys[$upperFkName]['REF_TABLE_NAME']);

            $sql = sprintf('ALTER TABLE %s DROP CONSTRAINT %s',
                $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
                $this->quoteIdentifier($foreignKeys[$upperFkName]['FK_NAME']));

            $this->resetDdlCache($tableName, $schemaName);

            $this->raw_query($sql);
        }

        return $this;
    }

    /**
     * Drop the Depend triggers part
     *
     * @param string $tableName
     * @param string $refTableName
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    protected function _dropDependTriggersAction($tableName, $refTableName)
    {

        $concatData = array(
            $this->getCheckSql(
                'start_teg_pos != 0',
                'SUBSTRING(trigger_script, 0, start_teg_pos)',
                'NULL'),
            $this->getCheckSql(
                'finish_teg_pos != 0',
                "SUBSTRING(trigger_script, finish_teg_pos + LEN('/* /ACTION ADDED BY '+ :tablename1 + '*/'), DATALENGTH(trigger_script))",
                'NULL')
        );

        $subSelect = $this->select();
        $subSelect->from(array('t' => 'sys.triggers'),
            array (
                'trigger_script'=> new Zend_Db_Expr('OBJECT_DEFINITION(t.object_id)'),
                'start_teg_pos'=> new Zend_Db_Expr("CHARINDEX('/*ACTION ADDED BY '+ :tablename2 + '*/', OBJECT_DEFINITION(t.object_id))"),
                'finish_teg_pos'=> new Zend_Db_Expr("CHARINDEX('/* /ACTION ADDED BY '+ :tablename3 + '*/', OBJECT_DEFINITION(t.object_id))")
                ))
                ->where("t.parent_id = OBJECT_ID(:tablename4)");

                   // "OBJECT_DEFINITION(t.object_id) like '%'+ :tablename4 +'%' AND t.parent_id != OBJECT_ID(:tablename5)"

        $select = $this->select();
        $select->from(array('r' => new Zend_Db_Expr(sprintf('(%s)', $subSelect->assemble()))),
            array(
                'trigger_script' => sprintf('CAST (%s AS VARCHAR(MAX))', $this->getConcatSql($concatData))
            ));

        $query = $this->query($select, array(
            'tablename1' => $tableName,
            'tablename2' => $tableName,
            'tablename3' => $tableName,
            'tablename4' => $refTableName,
        ));

        while ($row = $query->fetchColumn() ) {
            $this->raw_query(str_replace('CREATE TRIGGER', 'ALTER TRIGGER', $row));
        }
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

        $extProp = "SELECT value"
            . " FROM fn_listextendedproperty"
            . " (N'%s', N'user', N'dbo', N'table', sop.name, N'CONSTRAINT', sfk.name )";
        $ddl = $this->loadDdlCache($cacheKey, self::DDL_FOREIGN_KEY);
        if ($ddl === false) {
            $ddl = array();
            $query = "
                SELECT sfk.object_id,
                    sfk.name                            AS fk_name,
                    sop.name                            AS table_name,
                    scp.name                            AS column_name,
                    sor.name                            AS ref_table_name,
                    scr.name                            AS ref_column,
                    CAST((%s) AS VARCHAR) AS on_delete,
                    CAST((%s) AS VARCHAR) AS on_update
                FROM sys.foreign_keys sfk
                INNER JOIN sys.foreign_key_columns sfkc ON sfk.object_id = sfkc.constraint_object_id
                INNER JOIN sys.sysobjects sop ON sop.id = sfk.parent_object_id
                    AND sop.id = sfkc.parent_object_id
                INNER JOIN sys.sysobjects sor ON sor.id = sfk.referenced_object_id
                    AND sor.id = sfkc.referenced_object_id
                INNER JOIN sys.columns scp ON scp.object_id = sop.id
                    AND scp.object_id  = sfkc.parent_object_id
                    AND scp.column_id = sfkc.parent_column_id
                INNER JOIN sys.columns scr ON scr.object_id = sor.id
                    AND scr.object_id  = sfkc.referenced_object_id
                    AND scr.column_id = sfkc.referenced_column_id
                WHERE sop.name = '%s'";
            $sql = sprintf($query,
                sprintf($extProp, self::EXTPROP_COMMENT_FK_DELETE),
                sprintf($extProp, self::EXTPROP_COMMENT_FK_UPDATE),
                $this->quoteIdentifier($this->_getTableName($tableName, $schemaName))
            );

            foreach ($this->fetchAll($sql) as $row) {
                $foreignKeyName             = 'fk_name';
                $columnName                 = 'column_name';
                $referencedTableName        = 'ref_table_name';
                $referencedColumnName       = 'ref_column';
                $deleteReferentialAction    = 'on_delete';
                $updateReferentialAction    = 'on_update';

                $upperKeyName               = strtoupper($row[$foreignKeyName]);

                $ddl[$upperKeyName] = array(
                    'FK_NAME'           => $row[$foreignKeyName],
                    'SCHEMA_NAME'       => $schemaName,
                    'TABLE_NAME'        => $tableName,
                    'COLUMN_NAME'       => $row[$columnName],
                    'REF_SHEMA_NAME'    => $schemaName,
                    'REF_TABLE_NAME'    => $row[$referencedTableName],
                    'REF_COLUMN_NAME'   => $row[$referencedColumnName],
                    'ON_DELETE'         => $row[$deleteReferentialAction],
                    'ON_UPDATE'         => $row[$updateReferentialAction]
                );
            }
        }

        $this->saveDdlCache($cacheKey, self::DDL_FOREIGN_KEY, $ddl);
        return $ddl;
    }

    /**
     * Creates and returns a new Varien_Db_Select object for this adapter.
     *
     * @return Varien_Db_Select
     */
    public function select()
    {
        return new Varien_Db_Select($this);
    }

    /**
     * Retrieve identity columns for table
     *
     * @param string $tableName
     * @param string $schemaName
     * @return array
     */
    protected function _getIdentityColumns($tableName, $schemaName = null)
    {
        $identityColumns = array();
        foreach ($this->describeTable($tableName, $schemaName) as $column) {
            if ($column['IDENTITY']) {
                $identityColumns[$column['COLUMN_NAME']] = $column['COLUMN_NAME'];
            }
        }

        return $identityColumns;
    }

     /**
     * Obtain primary key fields
     *
     * @param string    $tableName
     * @param array     $schemaName
     * @return string the fields of primary key table
     */
    protected function _getPrimaryKeyColumns($tableName, $schemaName = null)
    {
        $primaryKeyColumns = array();

        foreach ($this->getIndexList($tableName, $schemaName) as $index ) {
            if ($index['INDEX_TYPE'] == Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY) {
                foreach($index['COLUMNS_LIST'] as $value) {
                    $primaryKeyColumns[$value] = $value;
                }
            }
        }
        return $primaryKeyColumns;
    }

    /**
     * Obtain unique index fields
     *
     * @param string    $tableName
     * @param array     $schemaName
     * @return string The fields of unique indexes table
     */
    protected function _getUniqueIndexColumns($tableName, $schemaName = null)
    {
        $uniqueIndexColumns = array();

        foreach ($this->getIndexList($tableName, $schemaName) as $index ) {
            if ($index['INDEX_TYPE'] == Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE) {
                foreach($index['COLUMNS_LIST'] as $value) {
                    $uniqueIndexColumns[$value] = $value;
                }
            }
        }
        return $uniqueIndexColumns;
    }

    /**
     * Merge data into table
     *
     * @param string $table
     * @param array $data
     * @param array $fields
     * @return int the affected rows
     * @throws Zend_Db_Exception
     */
    protected function _merge($table, array $data, array $fields = array())
    {
        $cols = array_keys($data);

        // update fields
        if (empty($fields)) {
            $fields = $cols;
        }

        $whereConditions = array();

        // Obtain primary key fields
        $pkColumns = $this->_getPrimaryKeyColumns($table);
        $groupCond = array();
        $usePkCond = true;
        foreach ($pkColumns as $column) {
            if (!in_array($column, $cols)) {
                $usePkCond = false;
            } else {
                $groupCond[] = sprintf("%s = %s", $this->quoteIdentifier($column), $this->quote($data[$column]));
            }

            $k = array_search($column, $fields);
            if ($k !== false) {
                unset($fields[$k]);
            }
        }

        if (!empty($groupCond) && $usePkCond) {
            $whereConditions[] = sprintf('(%s)', implode(') AND (', $groupCond));
        }

        // Obtain unique indexes fields
        $unqColumns = $this->_getUniqueIndexColumns($table);
        $groupCond  = array();
        $useUnqCond = true;
        foreach($unqColumns as $column) {
            if (!in_array($column, $cols)) {
                $useUnqCond = false;
            } else {
                $groupCond[] = sprintf("%s = %s", $this->quoteIdentifier($column), $this->quote($data[$column]));
            }

            $k = array_search($column, $fields);
            if ($k !== false) {
                unset($fields[$k]);
            }
        }

        if (!empty($groupCond) && $useUnqCond) {
            $whereConditions[] = sprintf('(%s)', implode(' AND ', $groupCond));
        }

        // check and prepare where condition
        if (empty($whereConditions)) {
            throw new Zend_Db_Exception('Invalid primary or unique columns in merge data');
        }

        $where = sprintf('(%s)', implode(') OR (', $whereConditions));
        $query = sprintf('SELECT COUNT(1) FROM %s WHERE %s', $table, $where);
        $count = $this->fetchOne($query);

        if ($count == 0) {
            $this->insert($table, $data);
        } else {
            $bind = array();
            foreach ($data as $column => $value) {
                if (in_array($column, $fields)) {
                    $bind[$column] = $value;
                }
            }
            if (count($bind) > 0) {
                $this->update($table, $bind, $where);
            }
        }

        return 1;
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
        $row = reset($data); // get first element from data array
        if (is_array($row)) {
            $cols = array_keys($row);
        } else {
            $cols = array_keys($data);
        }

        $hasIdentityColumns = false;
        $identityColumns    = $this->_getIdentityColumns($table);
        if ($identityColumns && !array_diff($this->_getIdentityColumns($table), $cols)) {
            $hasIdentityColumns = true;
        }

        if ($hasIdentityColumns) {
            $this->query(sprintf('SET IDENTITY_INSERT %s ON', $this->quoteIdentifier($table)));
        }

        if (is_array($row)) { // Array of column-value pairs
            $result = 0;
            foreach ($data as $row) {
                $result += $this->_merge($table, $row, $fields);
            }
        } else { // Column-value pairs
            $result = $this->_merge($table, $data, $fields);
        }

        if ($hasIdentityColumns) {
            $this->query(sprintf('SET IDENTITY_INSERT %s OFF', $this->quoteIdentifier($table)));
        }

        return $result;
    }

    /**
     * Inserts a table multiply rows with specified data.
     *
     * @param mixed $table The table to insert data into.
     * @param array $data Column-value pairs or array of Column-value pairs.
     * @return int The number of affected rows.
     * @throws Zend_Db_Exception
     */
    public function insertMultiple($table, array $data)
    {
        $row = reset($data);
        // support insert syntax
        if (!is_array($row)) {
            return $this->insert($table, $data);
        }

        // validate data array
        $cols = array_keys($row);
        $insertArray = array();
        foreach ($data as $row) {
            $line = array();
            if (array_diff($cols, array_keys($row))) {
                throw new Zend_Db_Exception('Invalid data for insert');
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
     * @param   array $columns  the data array column map
     * @param   array $data
     * @return  int
     * @throws Zend_Db_Exception
     */
    public function insertArray($table, array $columns, array $data)
    {
        $vals = array();
        $bind = array();
        $over = false;
        $i    = 0;
        $columnsCount = count($columns);
        foreach ($data as $row) {
            $i ++;
            // SQL Server supports a maximum of 2100 parameters
            if (count($bind) > 2000) {
                $over = array_slice($data, $i);
                break;
            }
            if ($columnsCount != count($row)) {
                throw new Zend_Db_Exception('Invalid data for insert');
            }
            $line = array();
            if ($columnsCount == 1) {
                if ($row instanceof Zend_Db_Expr) {
                    $line = $row->__toString();
                } else {
                    $line = '?';
                    $bind[] = $row;
                }
                $vals[] = sprintf('SELECT %s', $line);
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
                $vals[] = sprintf('SELECT %s', implode(',', $line));
            }
        }

        // build the statement
        $columns = array_map(array($this, 'quoteIdentifier'), $columns);

        $sql = sprintf("INSERT INTO %s (%s) %s",
            $this->quoteIdentifier($table, true),
            implode(',', $columns), implode(' UNION ALL ', $vals));

        // execute the statement and return the number of affected rows
        $stmt = $this->query($sql, $bind);
        $result = $stmt->rowCount();

        if ($over) {
            $result += $this->insertArray($table, $columns, $over);
        }

        return $result;
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
        if ($this->_pdoType == 'sqlsrv') {
            return $this->_sqlsrvInsertForce($table, $bind);
        }

        $this->query(sprintf('SET IDENTITY_INSERT %s ON', $this->quoteIdentifier($table)));
        $result = parent::insert($table, $bind);
        $this->query(sprintf('SET IDENTITY_INSERT %s OFF', $this->quoteIdentifier($table)));
        return $result;
    }

    /**
     * Gets the last ID generated automatically by an IDENTITY/AUTOINCREMENT column.
     *
     * As a convention, on RDBMS brands that support sequences
     * (e.g. Oracle, PostgreSQL, DB2), this method forms the name of a sequence
     * from the arguments and returns the last id generated by that sequence.
     * On RDBMS brands that support IDENTITY/AUTOINCREMENT columns, this method
     * returns the last value generated for such a column, and the table name
     * argument is disregarded.
     *
     * On RDBMS brands that don't support sequences, $tableName and $primaryKey
     * are ignored.
     *
     * @param string $tableName   OPTIONAL Name of table.
     * @param string $primaryKey  OPTIONAL Name of primary key column.
     * @return string
     */
    public function lastInsertId($tableName = null, $primaryKey = null)
    {
        if ($this->_pdoType == 'sqlsrv') {
            $this->_connect();
            return $this->_connection->lastInsertId($tableName);
        }

        return parent::lastInsertId($tableName, $primaryKey);
    }

    /**
     * Inserts a table row with specified data
     * Special for Zero values to identity column
     *
     * @param string $table
     * @param array $bind
     * @return int The number of affected rows.
     */
    protected function _sqlsrvInsertForce($table, array $bind)
    {
        $sql  = sprintf('SET IDENTITY_INSERT %s ON', $this->quoteIdentifier($table));
        // extract and quote col names from the array keys
        $cols = array();
        $vals = array();
        foreach ($bind as $col => $val) {
            $cols[] = $this->quoteIdentifier($col, true);
            if ($val instanceof Zend_Db_Expr) {
                $vals[] = $val->__toString();
                unset($bind[$col]);
            } else {
                $vals[] = '?';
            }
        }

        // build the statement
        $sql .= "\n INSERT INTO "
            . $this->quoteIdentifier($table, true)
            . ' (' . implode(', ', $cols) . ') '
            . 'VALUES (' . implode(', ', $vals) . ')'
            . sprintf("\nSET IDENTITY_INSERT %s OFF", $this->quoteIdentifier($table));

        // execute the statement and return the number of affected rows
        $stmt = $this->query($sql, array_values($bind));
        $result = $stmt->rowCount();
        return $result;
    }

    /**
     * Prepares and executes an SQL statement with bound data.
     *
     * @param  mixed  $sql  The SQL statement with placeholders.
     *                      May be a string or Zend_Db_Select.
     * @param  mixed  $bind An array of data or data itself to bind to the placeholders.
     * @return Zend_Db_Statement_Interface
     * @throws Zend_Db_Statement_Exception
     */
    public function query($sql, $bind = array())
    {
        $this->_debugTimer();
        try {
            if ($this->_pdoType == 'sqlsrv') {
                // This pdo has some problems with different binds - so convert them to non-buggy ones
                $this->_prepareQuery($sql, $bind);
            } else {
                // Embed blobs as hex data into query
                $this->_embedBlobs($sql, $bind);
            }

            $result = parent::query($sql, $bind);
        } catch (Exception $e) {
            $this->_debugStat(self::DEBUG_QUERY, $sql, $bind);
            $this->_debugException($e);
        }
        $this->_debugStat(self::DEBUG_QUERY, $sql, $bind, $result);
        return $result;
    }

    /**
     *
     * Embeds blobs into query as hex data.
     * Query must use positional binds only.
     *
     * @param Zend_Db_Select|string $sql
     * @param mixed $bind
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    protected function _embedBlobs(&$sql, &$bind = array())
    {
        // Safely check $bind, do not modify it, if there are no blobs, and we won't do anything
        if (is_array($bind)) {
            $hasBlobs = false;
            foreach ($bind as $param) {
                if (($param instanceof Varien_Db_Statement_Parameter) && $param->getIsBlob()) {
                    $hasBlobs = true;
                    break;
                }
            }
        } else {
            $hasBlobs = ($bind instanceof Varien_Db_Statement_Parameter) && $param->getIsBlob();
        }
        if (!$hasBlobs) {
            return $this;
        }

        // We really have blobs there - insert them as hex data
        $this->_prepareQuery($sql, $bind); // Convert to positional binds
        $sqlParts = explode('?', $sql);
        $sql = '';
        foreach ($bind as $key => $val) {
            $sql .= array_shift($sqlParts);
            if (($val instanceof Varien_Db_Statement_Parameter) && ($val->getIsBlob())) {
                $rawValue = $val->getValue();
                $hexValue = bin2hex($rawValue);
                $sql .= '0x' . $hexValue;
                unset($bind[$key]);
            } else {
                $sql .= '?';
            }
        }
        $sql .= array_shift($sqlParts);
        $bind = array_values($bind);
        return $this;
    }

    /**
     * Prepares SQL query either to suit for SqlServer Windows client, or just to simplify query.
     * 1) Moves to bind all special parameters that can be confused with bind placeholders
     * (e.g. "foo:bar").
     * 2) Changes named bind to positional one
     *
     * @param Zend_Db_Select|string $sql
     * @param mixed $bind
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    protected function _prepareQuery(&$sql, &$bind = array())
    {
        if ($sql instanceof Zend_Db_Select) {
            $sql = $sql->assemble();
        }
        if (!is_array($bind)) {
            $bind = array($bind);
        }

        /**
         * Mixed bind is not supported - so remember whether it is named bind, and normalize later if required.
         */
        $isNamedBind = false;
        if ($bind) {
            foreach ($bind as $k => $v) {
                if (!is_int($k)) {
                    $isNamedBind = true;
                    if ($k[0] != ':') {
                        $bind[":{$k}"] = $v;
                        unset($bind[$k]);
                    }
                }
            }
        }

        if (strpos($sql, ':') !== false || strpos($sql, '?') !== false) {
            $before = count($bind);
            $this->_bindParams = $bind; // Used by callback
            $sql = preg_replace_callback('#((N?)([\'"])((\\3)|((.*?[^\\\\])\\3)))#',
                array($this, '_processBindCallback'),
                $sql);
            Varien_Exception::processPcreError();
            $bind = $this->_bindParams;
            // If _processBindCallbacks() has added named entries - convert bind to positional
            if (count($bind) != $before) {
                if ($before) {
                    if (!$isNamedBind) {
                        // We have mixed bind with positional and named params
                        $this->_convertMixedBind($sql, $bind);
                        $isNamedBind = false;
                    }
                } else {
                    // Callback has added params and we have normal named bind
                    $isNamedBind = true;
                }
            }
        }

        // Always convert named bind to positional, because underlying library has problems with named binds
        if (!empty($bind) && $isNamedBind) {
            $this->_convertNamedBind($sql, $bind);
        }

        return $this;
    }

    /**
     * Convert SQL string and named bind to positional one.
     *
     * @param string $sql
     * @param array $bind
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    protected function _convertNamedBind(&$sql, &$bind)
    {
        $bindResult = array();
        $map = array();
        foreach ($bind as $k => $v) {
            $offset = 0;
            while (true) {
                $pos = strpos($sql, $k, $offset);
                if ($pos === false) {
                    break;
                } else {
                    $offset = $pos + strlen($k);
                    $bindResult[$pos] = $v;
                }
            }

            $map[$k] = '?';
        }

        ksort($bindResult);
        $bind = array_values($bindResult);
        $sql = strtr($sql, $map);

        return $this;
    }

    /**
     * Normalizes mixed positional-named bind to positional bind, and replaces named placeholders in query to
     * '?' placeholders.
     *
     * @param string $sql
     * @param array $bind
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    protected function _convertMixedBind(&$sql, &$bind)
    {
        $positions  = array();
        $offset     = 0;
        // get positions
        while (true) {
            $pos = strpos($sql, '?', $offset);
            if ($pos !== false) {
                $positions[] = $pos;
                $offset = ++$pos;
            } else {
                break;
            }
        }

        $bindResult = array();
        $map = array();
        foreach ($bind as $k => $v) {
            // positional
            if (is_int($k)) {
                if (!isset($positions[$k])) {
                    continue;
                }
                $bindResult[$positions[$k]] = $v;
            } else {
                $offset = 0;
                while (true) {
                    $pos = strpos($sql, $k, $offset);
                    if ($pos === false) {
                        break;
                    } else {
                        $offset = $pos + strlen($k);
                        $bindResult[$pos] = $v;
                    }
                }
                $map[$k] = '?';
            }
        }

        ksort($bindResult);
        $bind = array_values($bindResult);
        $sql = strtr($sql, $map);

        return $this;
    }

    /**
     * Callback function for preparation of query and bind by regexp.
     * Checks query parameters for special symbols and moves such parameters to bind array as named ones.
     * This method writes to $_bindParams, where query bind parameters are kept.
     * This method requires further normalizing, if bind array is positional.
     *
     * @param array $matches
     * @return string
     */
    protected function _processBindCallback($matches)
    {
        if ($matches[1] != 'N' && isset($matches[7]) && (
            strpos($matches[7], "'") !== false ||
            strpos($matches[7], ':') !== false ||
            strpos($matches[7], '?') !== false)) {
            $bindName = ':_mage_bind_var_' . (++$this->_bindIncrement);
            $this->_bindParams[$bindName] = $this->_unQuote($matches[7]);
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
     * Returns the symbol the adapter uses for delimited identifiers.
     *
     * @return string
     */
    public function getQuoteIdentifierSymbol()
    {
        return '';
    }

    /**
     * Executes a SQL statement(s)
     *
     * @param string $sql
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    public function multiQuery($sql)
    {
        try {
            $stmts = $this->_splitMultiQuery($sql);
            $result = array();
            foreach ($stmts as $stmt) {
                $result[] = $this->raw_query($stmt);
            }
        } catch (Exception $e) {
            throw $e;
        }

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
        $parts = preg_split('#(;|\'|"|\\\\|//|--|\n|GO|/\*|\*/)#', $sql, null, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        $q = false;
        $c = false;
        $stmts = array();
        $s = '';

        foreach ($parts as $i => $part) {
            // strings
            if (($part === "'" || $part === '"') && ($i === 0 || $parts[$i-1] !== '\\')) {
                if ($q===false) {
                    $q = $part;
                } elseif ($q===$part) {
                    $q = false;
                }
            }

            // single line comments
            if (($part === '//' || $part === '--') && ($i === 0 || $parts[$i-1] === "\n")) {
                $c = $part;
            } elseif ($part === "\n" && ($c === '//' || $c === '--')) {
                $c = false;
            }

            // multi line comments
            if ($part === '/*' && $c === false) {
                $c = '/*';
            } else if ($part === '*/' && $c === '/*') {
                $c = false;
            }

            // statements
            if ($part === ';' && $q === false && $c === false) {
                if (trim($s) !== '') {
                    $stmts[] = trim($s);
                    $s = '';
                }
            } elseif (strtoupper($part) == 'GO') {
                $s = '';
            } else {
                $s .= $part;
            }
        }
        if (trim($s) !== '') {
            $stmts[] = trim($s);
        }

        return $stmts;
    }

    /**
     * Format Date to internal database date format
     *
     * @param int|string|Zend_Date $date
     * @param boolean $includeTime
     * @return Zend_Db_Expr
     */
    public function formatDate($date, $includeTime = true)
    {
        $date = Varien_Date::formatDate($date, $includeTime);

        if ($date === null) {
            return new Zend_Db_Expr('NULL');
        }
        return new Zend_Db_Expr($this->quoteInto('CAST(? as datetime)', $date));
    }

    /**
     * Run additional environment before setup
     *
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    public function startSetup()
    {
        return $this;
    }

    /**
     * Run additional environment after setup
     *
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    public function endSetup()
    {
        return $this;
    }

    /**
     * Set cache adapter
     *
     * @param Zend_Cache_Backend_Interface $adapter
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    public function setCacheAdapter($adapter)
    {
        $this->_cacheAdapter = $adapter;
        return $this;
    }

    /**
     * Run RAW Query
     *
     * @param string $sql
     * @return Zend_Db_Statement_Interface
     * @throws Zend_Db_Statement_Exception
     */
    public function raw_query($sql)
    {
        $noConnectionStart = 'SQLSTATE[08001]';
        $tries = 0;
        do {
            $retry = false;
            try {
                $result = $this->query($sql);
            } catch (Exception $e) {
                if ($tries < 10 && substr($e->getMessage(), 0, strlen($noConnectionStart)) == $noConnectionStart) {
                    $retry = true;
                    $tries++;
                } else {
                    throw $e;
                }
            }
        } while ($retry);

        return $result;
    }

    /**
     * Run RAW query and Fetch First row
     *
     * @param string $sql
     * @param string|int $field
     * @return mixed
     */
    public function raw_fetchRow($sql, $field = null)
    {
        $result = $this->raw_query($sql);
        if (!$result) {
            return false;
        }

        $row = $result->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return false;
        }

        if (empty($field)) {
            return $row;
        } else {
            return isset($row[$field]) ? $row[$field] : false;
        }
    }

    /**
     * Allow DDL caching
     *
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    public function allowDdlCache()
    {
        $this->_isDdlCacheAllowed = true;
        return $this;
    }

    /**
     * Disallow DDL caching
     *
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    public function disallowDdlCache()
    {
        $this->_isDdlCacheAllowed = false;
        return $this;
    }

    /**
     * Reset cached DDL data from cache
     * if table name is null - reset all cached DDL data
     *
     * @param string $tableName
     * @param string $schemaName OPTIONAL
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    public function resetDdlCache($tableName = null, $schemaName = null)
    {
        if (!$this->_isDdlCacheAllowed) {
            return $this;
        }
        if ($tableName === null) {
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
     * Save DDL data into cache
     *
     * @param string $tableCacheKey
     * @param int $ddlType
     * @return Varien_Db_Adapter_Pdo_Mssql
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
     * Return ID for cache key
     *
     * @param string $tableKey
     * @param string $ddlType
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
     * Build SQL statement for condition
     *
     * If $condition integer or string - exact value will be filtered ('eq' condition)
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
     * - array("seq" => $stringValue)
     * - array("sneq" => $stringValue)
     *
     * If non matched - sequential array is expected and OR conditions
     * will be built using above mentioned structure
     *
     * @param string $fieldName
     * @param integer|string|array $condition
     * @return string
     */
    public function prepareSqlCondition($fieldName, $condition)
    {
        $conditionKeyMap = array(
            'eq'            => "{{fieldName}} = ?",
            'neq'           => "{{fieldName}} != ?",
            'like'          => "{{fieldName}} LIKE ?",
            'nlike'         => "{{fieldName}} NOT LIKE ?",
            'in'            => "{{fieldName}} IN(?)",
            'nin'           => "{{fieldName}} NOT IN(?)",
            'is'            => "{{fieldName}} IS ?",
            'notnull'       => "{{fieldName}} IS NOT NULL",
            'null'          => "{{fieldName}} IS NULL",
            'gt'            => "{{fieldName}} > ?",
            'lt'            => "{{fieldName}} < ?",
            'gteq'          => "{{fieldName}} >= ?",
            'lteq'          => "{{fieldName}} <= ?",
            'finset'        => "dbo.find_in_set(?, {{fieldName}}) = 1",
            'regexp'        => "dbo.regexp({{fieldName}}, ?, 1) = 1",
            'from'          => "{{fieldName}} >= ?",
            'to'            => "{{fieldName}} <= ?",
            'seq'           => null,
            'sneq'          => null
        );

        $query = '';
        if (is_array($condition)) {
            if (isset($condition['field_expr'])) {
                $fieldName = str_replace('#?', $this->quoteIdentifier($fieldName), $condition['field_expr']);
                unset($condition['field_expr']);
            }
            $key = key(array_intersect_key($condition, $conditionKeyMap));

            if (isset($condition['from']) || isset($condition['to'])) {
                if (isset($condition['from'])) {
                    $from   = $this->_prepareSqlDateCondition($condition, 'from');
                    $query = $this->_prepareQuotedSqlCondition($conditionKeyMap['from'], $from, $fieldName);
                }

                if (isset($condition['to'])) {
                    $query .= empty($query) ? '' : ' AND ';
                    $to     = $this->_prepareSqlDateCondition($condition, 'to');
                    $query = $this->_prepareQuotedSqlCondition($query . $conditionKeyMap['to'], $to, $fieldName);
                }
            } elseif (array_key_exists($key, $conditionKeyMap)) {
                $value = $condition[$key];
                if (($key == 'seq') || ($key == 'sneq')) {
                    $key = $this->_transformStringSqlCondition($key, $value);
                }
                $query = $this->_prepareQuotedSqlCondition($conditionKeyMap[$key], $value, $fieldName);
            } else {
                $queries = array();
                foreach ($condition as $orCondition) {
                    $queries[] = sprintf('(%s)', $this->prepareSqlCondition($fieldName, $orCondition));
                }

                $query = sprintf('(%s)', implode(' OR ', $queries));
            }
        } else {
            $query = $this->_prepareQuotedSqlCondition($conditionKeyMap['eq'], (string)$condition, $fieldName);
        }

        return $query;
    }

    /**
     * Prepare Sql condition
     *
     * @param  $text Condition value
     * @param  mixed $value
     * @param  string $fieldName
     * @return string
     */
    protected function _prepareQuotedSqlCondition($text, $value, $fieldName)
    {
        $sql = $this->quoteInto($text, $value);
        $sql = str_replace('{{fieldName}}', $fieldName, $sql);
        return $sql;
    }

    /**
     * Transforms sql condition key 'seq' / 'sneq' that is used for comparing string values to its analog:
     * - 'null' / 'notnull' for empty strings
     * - 'eq' / 'neq' for non-empty strings
     *
     * @param string $conditionKey
     * @param mixed $value
     * @return string
     */
    protected function _transformStringSqlCondition($conditionKey, $value)
    {
        $value = (string) $value;
        if ($value == '') {
            return ($conditionKey == 'seq') ? 'null' : 'notnull';
        } else {
            return ($conditionKey == 'seq') ? 'eq' : 'neq';
        }
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
            case 'integer':
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

            case 'datetime':
                $value  = $this->formatDate($value);
                break;

            case 'varchar':
            case 'text':
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
     * @return Zend_Db_Expr
     */
    public function getCheckSql($condition, $true, $false)
    {
        return new Zend_Db_Expr("CASE WHEN {$condition} THEN {$true} ELSE {$false} END");
    }

    /**
     * Returns valid IFNULL expression
     *
     * @param string $column
     * @param string $value OPTIONAL. Applies when $expression is NULL
     * @return Zend_Db_Expr
     */
    public function getIfNullSql($expression, $value = 0)
    {
        if ($expression instanceof Zend_Db_Expr || $expression instanceof Zend_Db_Select) {
            $expression = sprintf("ISNULL((%s), %s)", $expression, $value);
        } else {
            $expression = sprintf("ISNULL(%s, %s)", $expression, $value);
        }

        return new Zend_Db_Expr($expression);
    }

    /**
     * Generate fragment of SQL, that check value against multiple condition cases
     * and return different result depends on them
     *
     * @param string $valueName Name of value to check
     * @param array $casesResults Cases and results
     * @param string $defaultValue value to use if value doesn't confirmed to any cases
     * @return Zend_Db_Expr
     */
    public function getCaseSql($valueName, $casesResults, $defaultValue)
    {
        $expression = "CASE {$valueName}";
        foreach ($casesResults as $case => $result) {
            $expression .= " WHEN {$case} THEN {$result}";
        }
        if ($defaultValue !== null) {
            $expression .= ' ELSE ' . $defaultValue;
        }
        $expression .= ' END';
        return new Zend_Db_Expr($expression);
    }

    /**
     * Generate fragment of SQL, that combine together (concatenate) the results from data array
     *
     * @param array $data
     * @param string $separator concatenate with separator
     * @return Zend_Db_Expr
     */
    public function getConcatSql(array $data, $separator = null)
    {
        $glue = empty($separator) ? ' + ' : " + '{$separator}' + ";
        return new Zend_Db_Expr(implode($glue, $data));
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
        return new Zend_Db_Expr(sprintf('LEN(%s)', $string));
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
        $format = '(SELECT MIN(v) FROM (SELECT %s AS v) least)';
        return new Zend_Db_Expr(sprintf($format, implode(' AS v UNION ALL SELECT ', $data)));
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
        $format = '(SELECT MAX(v) FROM (SELECT %s AS v) greatest)';
        return new Zend_Db_Expr(sprintf($format, implode(' AS v UNION ALL SELECT ', $data)));
    }

    /**
     * Get Interval Unit SQL fragment
     *
     * @param int $interval
     * @param string $unit
     * @return string
     * @throws Zend_Db_Exception
     */
    protected function _getIntervalUnitSql($interval, $unit)
    {
        if (!isset($this->_intervalUnits[$unit])) {
            throw new Zend_Db_Exception(sprintf('Undefined interval unit "%s" specified', $unit));
        }

        return sprintf('%s, %d', $this->_intervalUnits[$unit], $interval);
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
        $expression = sprintf('DATEADD(%s, %s)',
            $this->_getIntervalUnitSql($interval, $unit),
            $this->quoteIdentifier($date)
        );
        return new Zend_Db_Expr($expression);
    }

    /**
     * Subtract time values (intervals) to a date value
     *
     * @see INTERVAL_* constants for $unit
     *
     * @param Zend_Db_Expr|string $date   quoted field name or SQL statement
     * @param int|string $interval
     * @param string $unit
     * @return Zend_Db_Expr
     */
    public function getDateSubSql($date, $interval, $unit)
    {
        return $this->getDateAddSql($date, -1 * $interval, $unit);
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
        switch ($format) {
            case '%Y-%m-%d %H:%i:%s':
                $expr = sprintf('CONVERT(VARCHAR(20), %s, 120)', $date);
                break;
            case '%Y-%m-%d %H:%i':
                $expr = sprintf('CONVERT(VARCHAR(16), %s, 120)', $date);
                break;
            case '%Y-%m-%d %H':
                $expr = sprintf('CONVERT(VARCHAR(14), %s, 120)', $date);
                break;
            case '%Y-%m-%d':
                $expr = sprintf('CONVERT(VARCHAR(10), %s, 120)', $date);
                break;
            default:
                $expr = sprintf("dbo.date_format(%s, '%s')", $date, $format);
                break;
        }

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
        return $this->getDateFormatSql($date, '%Y-%m-%d');
    }

    /**
     * Extract part of a date
     *
     * @see INTERVAL_* constants for $unit
     *
     * @param Zend_Db_Expr|string $date   quoted field name or SQL statement
     * @param string $unit
     * @return Zend_Db_Expr
     * @throws Zend_Db_Exception
     */
    public function getDateExtractSql($date, $unit)
    {
        $formatMap = array(
            self::INTERVAL_YEAR     => '%Y',
            self::INTERVAL_MONTH    => '%m',
            self::INTERVAL_DAY      => '%d',
            self::INTERVAL_HOUR     => '%H',
            self::INTERVAL_MINUTE   => '%i',
            self::INTERVAL_SECOND   => '%s',
        );

        if (!isset($formatMap[$unit])) {
            throw new Zend_Db_Exception(sprintf('Undefined interval unit "%s" specified', $unit));
        }

        return $this->getDateFormatSql($date, $formatMap[$unit]);
    }

    /**
     * Quotes an identifier.
     *
     * Accepts a string representing a qualified indentifier. For Example:
     * <code>
     * $adapter->quoteIdentifier('myschema.mytable')
     * </code>
     * Returns: "myschema"."mytable"
     *
     * Or, an array of one or more identifiers that may form a qualified identifier:
     * <code>
     * $adapter->quoteIdentifier(array('myschema','my.table'))
     * </code>
     * Returns: "myschema"."my.table"
     *
     * The actual quote character surrounding the identifiers may vary depending on
     * the adapter.
     *
     * @param string|array|Zend_Db_Expr $ident The identifier.
     * @param boolean $auto If true, heed the AUTO_QUOTE_IDENTIFIERS config option.
     * @return string The quoted identifier.
     */
    protected function _quoteIdentifier($value, $auto = false)
    {
        if ($auto === false || $this->_autoQuoteIdentifiers === true) {
            $upperValue = strtoupper($value);
            if (in_array($upperValue, $this->_reservedWords) || (int)$upperValue[0] > 0 || $upperValue[0] === "0") {
                $value = sprintf('[%s]', $value);
            }
        }
        return $value;
    }

    /**
     * Get table name with schema name if defined
     *
     * @param string $tableName
     * @param string $schemaName
     * @return string
     */
    protected function _getTableName($tableName, $schemaName = null)
    {
        return ($schemaName ? $schemaName . '.' : '') . $tableName;
    }

    /**
     * Start debug timer
     *
     * @return Varien_Db_Adapter_Pdo_Mssql
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
        $str = '## ' . date('Y-m-d H:i:s') . "\r\n" . $str;
        if (!$this->_debugIoAdapter) {
            $this->_debugIoAdapter = new Varien_Io_File();
            $dir = Mage::getBaseDir() . DS . $this->_debugIoAdapter->dirname($this->_debugFile);
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
     * Check is exists object comment
     *
     * @param string|array $object
     * @param string $commentType
     * @return boolean
     */
    protected function _checkCommentExists($object, $commentType)
    {
        $existsDependedObject = false;
        if (!is_array($object)) {
            $level1ObjectType = 'table';
            $level1ObjectName = $object;

        } else {
            reset($object);
            $level1ObjectType = key($object);
            $level1ObjectName = current($object);

            next($object);
            $level2ObjectType = key($object);
            $level2ObjectName = current($object);

            if (!empty($level2ObjectName) && !empty($level2ObjectType)) {
                $existsDependedObject = true;
            }
        }

        $sql = "SELECT COUNT(1) AS qty FROM fn_listextendedproperty (N'%s', N'user', N'dbo', N'%s', N'%s', %s, %s)";
        $sqlExistsComment = sprintf($sql,
            $commentType,
            $level1ObjectType,
            $level1ObjectName,
            $existsDependedObject ? " N'{$level2ObjectType}'" : 'NULL',
            $existsDependedObject ? " N'{$level2ObjectName}'" : 'NULL'
        );

        return ($this->raw_fetchRow($sqlExistsComment, 'qty') != 0);
    }
    /**
     * Add or update extended property to a database object.
     *
     * @param string $object
     * @param string $comment
     * @param string $commentType
     */
    protected function _addExtendProperty($object, $comment, $commentType)
    {
        $existsDependedObject = false;
        if (!is_array($object)) {
            $level1ObjectType = 'table';
            $level1ObjectName = $object;
        } else {
            // sp_%extendedproperty has only 3 levels: fist schema|user second object third depended object (like column)

            reset($object);
            $level1ObjectType = key($object);
            $level1ObjectName = current($object);

            next($object);
            $level2ObjectType = key($object);
            $level2ObjectName = current($object);

            if (!empty($level2ObjectName) && !empty($level2ObjectType)) {
                $existsDependedObject = true;
            }
        }

        if(!$this->_checkCommentExists($object, $commentType)) {
            $function = 'sp_addextendedproperty';
        } else {
            $function = 'sp_updateextendedproperty';
        }

        $sql = "EXEC %s N'%s', N'%s', N'user', N'dbo', N'%s', N'%s', %s, %s";
        $query = sprintf($sql,
            $function,
            $commentType,
            $comment,
            $level1ObjectType,
            $level1ObjectName,
            $existsDependedObject ? " N'{$level2ObjectType}'" : 'NULL',
            $existsDependedObject ? " N'{$level2ObjectName}'" : 'NULL'
        );
        $this->query($query);
    }

    /**
     * Add or update table comment
     *
     * @param string $tableName
     * @param string $comment
     * @return Varien_Db_Adapter_Oracle
     */
    protected function _addTableComment($tableName, $comment)
    {
        $this->_addExtendProperty($tableName, $comment, self::EXTPROP_COMMENT_TABLE);
        return $this;
    }

    /**
     * Add or update column comment
     *
     * @param string $tableName
     * @param string $comment
     * @return Varien_Db_Adapter_Oracle
     */
    protected function _addColumnComment($tableName, $columnName, $comment)
    {
        $object = array('table' => $tableName, 'column' => $columnName);
        $this->_addExtendProperty($object, $comment, self::EXTPROP_COMMENT_COLUMN);
        return $this;
    }

    /**
     * Return column data type
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $schemaName
     * @return string|false
     */
    protected function _getColumnDataType($tableName, $columnName, $schemaName = null)
    {
        foreach ($this->describeTable($tableName, $schemaName) as $column) {
            if ($column['COLUMN_NAME'] == $columnName) {
                if (array_search(strtoupper($column['DATA_TYPE']), array('CHAR', 'VARCHAR')) !== false) {
                    $datetype = sprintf('%s(%s)', $column['DATA_TYPE'], $column['LENGTH']);
                } else {
                    $datetype = $column['DATA_TYPE'];
                }
                return $datetype;
            }
        }
        return false;
    }
    /**
     * Retrieve trigger name for cascade update / delete
     *
     * @param string $tableName
     * @return string
     */
    protected function _getTriggerName($tableName, $triggerType = self::TRIGGER_CASCADE_UPD)
    {
        return strtoupper(sprintf("TRIGGER_%s_%s", $triggerType, $tableName));
    }

    /**
     * Create trigger for cascade delete
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $refTableName
     * @param string $refColumnName
     * @return Varien_Db_Adapter_Pdo_Mssql
     * @throws Zend_Db_Exception
     */
    protected function _addForeignKeyDeleteAction($tableName, $columnName, $refTableName, $refColumnName, $fkAction)
    {
        $sqlTrigger = $this->_getInsteadTriggerBody($refTableName);
        $ids = '';
        if ($tableName == $refTableName) {
            $ids = "\n;WITH depended_ids ({$refColumnName}) AS (                    \n"
		        . "SELECT m.{$refColumnName}                                        \n"
                . "FROM {$tableName} AS m                                           \n"
		        . "INNER JOIN deleted d ON m.{$refColumnName} = d.{$refColumnName}  \n"
                . "UNION ALL                                                        \n"
                . "SELECT m.{$refColumnName}                                        \n"
                . "FROM {$tableName} AS m                                           \n"
                . "INNER JOIN depended_ids AS di ON di.{$refColumnName} = m.{$columnName}\n)"
                . "INSERT INTO @deletedRows                                         \n"
                . "SELECT $refColumnName FROM depended_ids                          \n";
            if (strpos($sqlTrigger, "/*place ids here*/") === false) {
                throw new Zend_Db_Exception("Hierarchical query already exist! Cannot add anymore!");
            }
            $sqlTrigger = str_replace(
                "/*place ids here*/",
                 "/*HIERARCHICAL ACTION*/ \n". $ids,
                $sqlTrigger
            );
        } else {
            if ($fkAction == Varien_Db_Ddl_Table::ACTION_CASCADE) {
                $deleteAction = "/*ACTION ADDED BY {$tableName}*/                   \n"
                                . "        DELETE t FROM {$tableName} t             \n"
                                . "        INNER JOIN @deletedRows d ON             \n"
                                . "         t.{$columnName} = d.{$refColumnName};   \n"
                                . "/* /ACTION ADDED BY {$tableName}*/";
            } else {
                $deleteAction = "/*ACTION ADDED BY {$tableName}*/                   \n"
                                . "        UPDATE t \n"
                                . "        SET t.{$columnName} = NULL               \n"
                                . "      FROM {$tableName} t                        \n"
                                . "        INNER JOIN @deletedRows d ON             \n"
                                . "         t.{$columnName} = d.{$refColumnName};   \n"
                                . "/* /ACTION ADDED BY {$tableName}*/";
            }

            $sqlTrigger = str_replace(
                "/*place code here*/",
                $deleteAction . "\n /*place code here*/",
                $sqlTrigger
            );
        }
        $this->getConnection()->exec("SET ANSI_NULLS ON");
        $this->getConnection()->exec("SET QUOTED_IDENTIFIER ON");
        $this->getConnection()->exec($sqlTrigger);

        return $this;
    }

    /**
     * Minus superfluous characters from hash.
     *
     * @param  $hash
     * @param  $prefix
     * @param  $maxCharacters
     * @return string
     */
     protected function _minusSuperfluous($hash, $prefix, $maxCharacters)
     {
         $diff = strlen($hash) + strlen($prefix) - $maxCharacters;
         $superfluous = $diff / 2;
         $odd = $diff % 2;
         $hash = substr($hash, $superfluous, - ($superfluous+$odd));
         return $prefix . $hash;
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
        $prefix = 't_';
        if (strlen($tableName) > self::LENGTH_TABLE_NAME) {
            $shortName = Varien_Db_Helper::shortName($tableName);
            if (strlen($shortName) > self::LENGTH_TABLE_NAME) {
                $hash = md5($tableName);
                if (strlen($prefix.$hash) > self::LENGTH_TABLE_NAME) {
                    $tableName = $this->_minusSuperfluous($hash, $prefix, self::LENGTH_TABLE_NAME);
                } else {
                    $tableName = $prefix . $hash;
                }
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
     * @param string $indexType
     * @return string
     */
    public function getIndexName($tableName, $fields, $indexType = '')
    {
        if (is_array($fields)) {
            $fields = implode('_', $fields);
        }

        switch (strtolower($indexType)) {
            case Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE:
                $prefix = 'unq_';
                $shortPrefix = 'u_';
                break;
            case Varien_Db_Adapter_Interface::INDEX_TYPE_FULLTEXT:
                $prefix = 'fti_';
                $shortPrefix = 'f_';
                break;
            case Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX:
            default:
                $prefix = 'idx_';
                $shortPrefix = 'i_';
        }

        $hash = $tableName . '_' . $fields;

        if (strlen($hash) + strlen($prefix) > self::LENGTH_INDEX_NAME) {
            $short = Varien_Db_Helper::shortName($prefix . $hash);
            if (strlen($short) > self::LENGTH_INDEX_NAME) {
                $hash = md5($hash);
                if (strlen($hash) + strlen($shortPrefix) > self::LENGTH_INDEX_NAME) {
                    $hash = $this->_minusSuperfluous($hash, $shortPrefix, self::LENGTH_INDEX_NAME);
                }
            } else {
                $hash = $short;
            }
        } else {
            $hash = $prefix . $hash;
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
        $prefix = 'fk_';
        $hash = sprintf('%s_%s_%s_%s', $priTableName, $priColumnName, $refTableName, $refColumnName);
        if (strlen($prefix.$hash) > self::LENGTH_FOREIGN_NAME) {
            $short = Varien_Db_Helper::shortName($prefix.$hash);
            if (strlen($short) > self::LENGTH_FOREIGN_NAME) {
                $hash = md5($hash);
                if (strlen($prefix.$hash) > self::LENGTH_FOREIGN_NAME) {
                    $hash = $this->_minusSuperfluous($hash, $prefix, self::LENGTH_FOREIGN_NAME);
                } else {
                    $hash = $prefix.$hash;
                }
            } else {
                $hash = $short;
            }
        } else {
            $hash = $prefix . $hash;
        }

        return strtoupper($hash);
    }
        /**
     * Adds an adapter-specific LIMIT clause to the SELECT statement.
     *
     * @param string $sql
     * @param integer $count
     * @param integer $offset OPTIONAL
     * @return string
     * @throws Zend_Db_Adapter_Exception
     */
    public function limit($sql, $count, $offset = 0)
    {
        $query = '';
        $count = intval($count);
        if ($count <= 0) {
            throw new Zend_Db_Adapter_Exception("LIMIT argument count={$count} is not valid");
        }

        $offset = intval($offset);
        if ($offset < 0) {
            throw new Zend_Db_Adapter_Exception("LIMIT argument offset={$offset} is not valid");
        }

        $sql = preg_replace('/^SELECT\s+(DISTINCT\s)?/i', 'SELECT $1TOP ' . ($count + $offset) . ' ', $sql);

        if ($offset == 0) {
            $query = $sql;
        } else {
            $query = sprintf('
                SELECT mage2.* FROM (
                    SELECT mage1.*, ROW_NUMBER() OVER (ORDER BY RAND()) AS analytic_clmn
                    FROM (%s) mage1) mage2
                WHERE mage2.analytic_clmn >= %d', $sql,  $offset + 1);
        }

        return $query;
    }

    /**
     * Stop updating nonunique indexes
     *
     * @param string $tableName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    public function disableTableKeys($tableName, $schemaName = null)
    {
        $indexList = $this->getIndexList($tableName, $schemaName);
        $tableName = $this->_getTableName($tableName, $schemaName);
        foreach ($indexList as $indexProp) {
            if ($indexProp['INDEX_TYPE'] != Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX) {
                continue;
            }
            $query = sprintf('ALTER INDEX %s ON %s DISABLE',
                $this->quoteIdentifier($indexProp['KEY_NAME']),
                $this->quoteIdentifier($tableName)
            );
            $this->query($query);
        }

        return $this;
    }

    /**
     * Re-create missing indexes
     *
     * @param string $tableName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    public function enableTableKeys($tableName, $schemaName = null)
    {
        $indexList = $this->getIndexList($tableName, $schemaName);
        $tableName = $this->_getTableName($tableName, $schemaName);
        foreach ($indexList as $indexProp) {
            if ($indexProp['INDEX_TYPE'] != Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX) {
                continue;
            }
            $query = sprintf('ALTER INDEX %s ON %s REBUILD',
                $this->quoteIdentifier($indexProp['KEY_NAME']),
                $this->quoteIdentifier($tableName)
            );
            $this->query($query);
        }

        return $this;
    }

    /**
     * Get insert to table from select
     *
     * @param Varien_Db_Select $select
     * @param string $table
     * @param array $fields
     * @return string
     * @throws Zend_Db_Exception
     */
    public function insertFromSelect(Varien_Db_Select $select, $table, array $fields = array(), $mode = false)
    {
        if (!$mode) {
            return $this->_getInsertFromSelectSql($select, $table, $fields);
        }

        $indexes    = $this->getIndexList($table);
        $columns    = $this->describeTable($table);

        if (!$fields) {
            $fields = array_keys($columns);
        }

        // remap column aliases
        $select    = clone $select;
        $fields    = array_values($fields);
        $colsPart  = $select->getPart(Zend_Db_Select::COLUMNS);
        if (count($colsPart) != count($fields)) {
            throw new Zend_Db_Exception('Wrong columns count in SELECT for INSERT');
        }

        $i = 0;
        foreach ($colsPart as &$colData) {
            $colData[2] = $fields[$i++];
        }
        $select->setPart(Zend_Db_Select::COLUMNS, $colsPart);

        $insertCols = $fields;
        $updateCols = $fields;
        $whereCond  = array();

        // Obtain primary key fields
        $pkColumns = $this->_getPrimaryKeyColumns($table);
        $groupCond = array();
        $usePkCond = true;
        foreach ($pkColumns as $pkColumn) {
            if (!in_array($pkColumn, $insertCols)) {
                $usePkCond = false;
            } else {
                $groupCond[] = sprintf('t3.%1$s = t2.%1$s', $this->quoteIdentifier($pkColumn));
            }
            $k = array_search($pkColumn, $updateCols);
            if ($k !== false) {
                unset($updateCols[$k]);
            }
        }

        if (!empty($groupCond) && $usePkCond) {
            $whereCond[] = sprintf('(%s)', implode(') AND (', $groupCond));
        }

        // Obtain unique indexes fields
        foreach ($indexes as $indexData) {
            if ($indexData['INDEX_TYPE'] != Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE) {
                continue;
            }

            $groupCond  = array();
            $useUnqCond = true;
            foreach($indexData['COLUMNS_LIST'] as $column) {
                if (!in_array($column, $insertCols)) {
                    $useUnqCond = false;
                }
                $k = array_search($column, $updateCols);
                if ($k !== false) {
                    unset($updateCols[$k]);
                }
                $groupCond[] = sprintf('t3.%1$s = t2.%1$s', $this->quoteIdentifier($column));
            }
            if (!empty($groupCond) && $useUnqCond) {
                $whereCond[] = sprintf('(%s)', implode(' AND ', $groupCond));
            }
        }

        // validate where condition
        if (empty($whereCond)) {
            throw new Zend_Db_Exception('Invalid primary or unique columns in merge data');
        }

        $query = sprintf("MERGE INTO %s t3\nUSING (%s) t2\nON ( %s )",
            $this->quoteIdentifier($table),
            $select->assemble(),
            implode(' OR ', $whereCond)
        );

        // UPDATE Section
        if ($mode == Varien_Db_Adapter_Interface::INSERT_ON_DUPLICATE && $updateCols) {
            $updateCond = array();
            foreach ($updateCols as $column) {
                $updateCond[] = sprintf('t3.%1$s = t2.%1$s', $this->quoteIdentifier($column));
            }
            $query = sprintf("%s\nWHEN MATCHED THEN UPDATE SET %s", $query, implode(', ', $updateCond));
        }

        // INSERT SECTION
        // prepare insert columns condition and values
        $insertCond = array_map(array($this, 'quoteIdentifier'), $insertCols);
        $insertVals = array();

        foreach ($insertCols as $column) {
            $insertVals[] = sprintf('t2.%s', $this->quoteIdentifier($column));
        }
        $query = sprintf("%s\nWHEN NOT MATCHED THEN INSERT (%s) VALUES (%s);",
            $query,
            implode(', ', $insertCond),
            implode(', ', $insertVals)
        );

        return $query;
    }

    /**
     * Get insert to table from select
     *
     * @param Varien_Db_Select $select
     * @param string $table
     * @param array $fields
     * @return string
     */
    protected function _getInsertFromSelectSql(Varien_Db_Select $select, $table, array $fields = array())
    {
        $query = sprintf('INSERT INTO %s ', $this->quoteIdentifier($table));
        if ($fields) {
            $columns = array_map(array($this, 'quoteIdentifier'), $fields);
            $query .= sprintf('(%s)', implode(', ', $columns));
        }

        $query .= $select->assemble();

        return $query;
    }


    /**
     * Get update table query using select object for join and update
     *
     * @param Varien_Db_Select $select
     * @param string|array $table
     * @return string
     * @throws Zend_Db_Exception
     */
    public function updateFromSelect(Varien_Db_Select $select, $table)
    {
        if (!is_array($table)) {
            $table = array($table => $table);
        }
        $keys       = array_keys($table);
        $tableAlias = $keys[0];
        $tableName  = $table[$keys[0]];

        // render UPDATE SET
        $columns    = $select->getPart(Zend_Db_Select::COLUMNS);
        $updateSet  = array();
        foreach ($columns as $columnEntry) {
            list($correlationName, $column, $alias) = $columnEntry;
            if (empty($alias)) {
                $alias = $column;
            }
            if (!$column instanceof Zend_Db_Expr && !empty($correlationName)) {
                $column = $this->quoteIdentifier(array($correlationName, $column));
            }
            $updateSet[] = sprintf('%s = %s', $this->quoteIdentifier(array($tableAlias, $alias)), $column);
        }

        if (!$updateSet) {
            throw new Zend_Db_Exception('Undefined columns for UPDATE');
        }

        $joinSelect = clone $select;
        $joinSelect->reset(Zend_Db_Select::DISTINCT);
        $joinSelect->reset(Zend_Db_Select::COLUMNS);
        $joinSelect->from(array($tableAlias => $tableName), null);

        $query = sprintf('UPDATE %s SET %s %s',
            $this->quoteIdentifier($tableAlias),
            implode(', ', $updateSet),
            $joinSelect->assemble()
        );

        return $query;
    }

    /**
     * Get delete from select object query
     *
     * @param Varien_Db_Select $select
     * @param string $table the table name or alias used in select
     * @return string|int
     * @throws Zend_Db_Exception
     */
    public function deleteFromSelect(Varien_Db_Select $select, $table)
    {
        // check is used table in condition
        $tableAlias = false;

        $fromPart = $select->getPart(Zend_Db_Select::FROM);
        foreach ($fromPart as $correlationName => $joinProp) {
            if ($correlationName == $table || $joinProp['tableName'] == $table) {
                $tableAlias = $correlationName;
                break;
            }
        }

        if (!$tableAlias) {
            throw new Zend_Db_Exception('Invalid table name or table alias in select');
        }

        $joinSelect = clone $select
            ->reset(Zend_Db_Select::DISTINCT)
            ->reset(Zend_Db_Select::COLUMNS);

        $query = sprintf('DELETE %s %s',
            $this->quoteIdentifier($tableAlias),
            $joinSelect->assemble()
        );

        return $query;
    }

    /**
     * Return tables checksum
     */
    public function getTablesChecksum($tableNames, $schemaName = null)
    {
        $result = array();
        if(!is_array($tableNames)){
            $tableNames = array($tableNames);
        }
        foreach($tableNames as $tableName){
            $query = sprintf("SELECT CHECKSUM_AGG(BINARY_CHECKSUM(*)) AS CHECKSUM FROM %s",
                $this->_getTableName($tableName, $schemaName)
            );
            $checksum = $this->fetchOne($query);
            $result[$tableName] = is_null($checksum) ? 0 : $checksum;
        }
        return $result;
    }

    /**
     * Check if the database support STRAIGHT JOIN
     *
     * @return boolean
     */
    public function supportStraightJoin()
    {
        return false;
    }

    /**
     * Adds order by random to select object
     * Possible using integer field for optimization
     *
     * @param Varien_Db_Select $select
     * @param string $field
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    public function orderRand(Varien_Db_Select $select, $field = null)
    {
        $spec = new Zend_Db_Expr('NEWID()');
        $select->order($spec);

        return $this;
    }

    /**
     * Get instead trigger sql template
     *
     * @param string $tableName
     * @return string
     */
    protected function _getInsteadTriggerBody($tableName)
    {
        $pkColumns = $this->_getPrimaryKeyColumns($tableName);
        $query = sprintf(" SELECT CAST(OBJECT_DEFINITION (object_id) AS VARCHAR(MAX)) \n"
                . " FROM sys.triggers t                \n"
                . " WHERE t.is_instead_of_trigger = 1 \n"
                . " AND t.parent_id = OBJECT_ID('%s')",
            $tableName
        );
        $deletedRows = array();
        foreach ($pkColumns as $column) {
            $deletedRows[] = sprintf('%s %s', $column, $this->_getColumnDataType($tableName, $column));
        }
        $triggerBody = str_replace('CREATE TRIGGER', 'ALTER TRIGGER', $this->fetchOne($query));

        if (empty($triggerBody)){
            $triggerName = $this->_getTriggerName($tableName, self::TRIGGER_CASCADE_DEL);
            $pKeysCond = array();
            foreach ($pkColumns as $column) {
                $pKeysCond[] = sprintf('t.%s = d.%s', $column, $column);
            }

            $fields = implode(', ', $pkColumns);
            $triggerBody = "CREATE TRIGGER [{$triggerName}]                 \n"
                . "    ON  {$tableName}                                     \n"
                . "    INSTEAD OF DELETE                                    \n"
                . "AS                                                       \n"
                . "BEGIN                                                    \n"
                . "    SET NOCOUNT ON;                                      \n"
                . "    DECLARE @deletedRows TABLE (" . implode(",\n", $deletedRows) . " )\n"
                . "    INSERT INTO @deletedRows SELECT {$fields} FROM deleted       \n"
                . "    /*place ids here*/                                  \n"
                . "    BEGIN TRANSACTION                                    \n"
                . "    BEGIN TRY                                            \n"
                . "  /*place code here*/                                   \n"
                . "        DELETE t FROM {$tableName} t INNER JOIN @deletedRows d ON\n"
                . implode(' AND ', $pKeysCond)
                . "        COMMIT                                           \n"
                . "    END TRY                                              \n"
                . "    BEGIN CATCH                                          \n"
                . "        DECLARE @ErrorMessage NVARCHAR(4000);            \n"
                . "        DECLARE @ErrorSeverity INT;                      \n"
                . "        DECLARE @ErrorState INT;                         \n"
                . "        SELECT @ErrorMessage = ERROR_MESSAGE(),          \n"
                . "            @ErrorSeverity = ERROR_SEVERITY(),           \n"
                . "            @ErrorState = ERROR_STATE();                 \n"
                . "        RAISERROR (@ErrorMessage, @ErrorSeverity, @ErrorState);\n"
                . "        ROLLBACK TRANSACTION                             \n"
                . "    END CATCH                                            \n"
                . "END                                                      \n"
                . "                                                         \n";
        }

        return $triggerBody;
    }

    /**
     * Render SQL FOR UPDATE clause
     *
     * @param string $sql
     * @return string
     */
    public function forUpdate($sql)
    {
        $sql = preg_replace_callback('#FROM ([^ ]+)( (AS )?([^ ]+))?#i',
            array($this, '_forUpdateFromCallback'), $sql);
        $sql = preg_replace_callback('#((INNER|OUTER|LEFT|RIGHT) JOIN ([^ ]+)( (AS )?([^ ]+))?)( ON)#i',
            array($this, '_forUpdateJoinCallback'), $sql);
        $sql = preg_replace_callback('#(CROSS JOIN ([^ ]+)( (AS )?([^ ]+))?)#i',
            array($this, '_forUpdateJoinCallback'), $sql);

        return $sql;
    }

    /**
     * Call-back replace function for forUpdate method
     *
     * @param array $match
     * @return string
     */
    protected function _forUpdateFromCallback($match)
    {
        $alias     = '';
        $afterAlias = '';
        if (!empty($match[2])) {
            $skip = array('INNER', 'LEFT', 'RIGHT', 'OUTER', 'CROSS', 'JOIN', 'WHERE', 'ORDER', 'GROUP');
            if (!in_array(strtoupper(trim($match[2])), $skip)) {
                $alias      = $match[2];
            } else {
                $afterAlias = $match[2];
            }
        }
        return sprintf('FROM %s%s WITH(UPDLOCK) %s', $match[1], $alias, $afterAlias);
    }

    /**
     * Call-back replace function for forUpdate method
     *
     * @param array $match
     * @return string
     */
    protected function _forUpdateJoinCallback($match)
    {
        $on = !empty($match[7]) ? $match[7] : '';
        return sprintf('%s WITH(UPDLOCK)%s', $match[1], $on);
    }

    /**
     * Return ddl type
     *
     * @param array $options
     * @return string
     */
    protected function _getDdlType($options)
    {
        $ddlType = null;
        if (isset($options['TYPE'])) {
            $ddlType = $options['TYPE'];
        } elseif (isset($options['COLUMN_TYPE'])) {
            $ddlType = $options['COLUMN_TYPE'];
        }

        return $ddlType;
    }

    /**
     * Prepare sql date condition
     *
     * @param array $condition
     * @param string $key
     * @return string
     */
    protected function _prepareSqlDateCondition($condition, $key)
    {
        if (empty($condition['date'])) {
            if (empty($condition['datetime'])) {
                $result = $condition[$key];
            } else {
                $result = $this->formatDate($condition[$key]);
            }
        } else {
            $result = $this->formatDate($condition[$key]);
        }

        return $result;
    }

    /**
     * Try to find installed primary key name, if not - formate new one.
     *
     * @param string $tableName Table name
     * @param string $schemaName OPTIONAL
     * @return string Primary Key name
     */
    public function getPrimaryKeyName($tableName, $schemaName = null)
    {
        $indexes = $this->getIndexList($tableName, $schemaName);
        if (isset($indexes['PRIMARY'])) {
            return $indexes['PRIMARY']['KEY_NAME'];
        } else {
            return 'PK_' . strtoupper($tableName);
        }
    }

    /**
     * Parse text size
     * Returns max allowed size if value great it
     *
     * @param string|int $size
     * @return int
     */
    protected function _parseTextSize($size)
    {
        $size = trim($size);
        $last = strtolower(substr($size, -1));

        switch ($last) {
            case 'k':
                $size = intval($size) * 1024;
                break;
            case 'm':
                $size = intval($size) * 1024 * 1024;
                break;
            case 'g':
                $size = intval($size) * 1024 * 1024 * 1024;
                break;
        }

        if (empty($size)) {
            return Varien_Db_Ddl_Table::DEFAULT_TEXT_SIZE;
        }
        if ($size >= Varien_Db_Ddl_Table::MAX_TEXT_SIZE) {
            return Varien_Db_Ddl_Table::MAX_TEXT_SIZE;
        }

        return intval($size);
    }

    /**
     * Converts fetched blob into raw binary PHP data.
     * The 'sqlsrv' windows drivers return it as hex string.
     *
     * @mixed $value
     * @return mixed
     */
    public function decodeVarbinary($value)
    {
        if ($this->_pdoType == 'sqlsrv') {
            return pack('H*', $value);
        } else {
            return $value;
        }
    }
}
