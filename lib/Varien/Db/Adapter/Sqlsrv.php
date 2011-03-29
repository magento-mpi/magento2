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
 * Varien DB Adapter for MS SQL (Sql server native drivers)
 *
 * @category    Varien
 * @package     Varien_DB
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Db_Adapter_Sqlsrv extends Zend_Db_Adapter_Sqlsrv implements Varien_Db_Adapter_Interface
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
    const DDL_CACHE_PREFIX          = 'DB_SQLSRV_DDL';
    const DDL_CACHE_TAG             = 'DB_SQLSRV_DDL';

    const TRIGGER_UPDATE_CASCADE    = 'on_update';
    const TRIGGER_DELETE_CASCADE    = 'on_delete';

    const LENGTH_TABLE_NAME         = 128;
    const LENGTH_INDEX_NAME         = 128;
    const LENGTH_FOREIGN_NAME       = 128;
    /**
     * Default class name for a DB statement.
     *
     * @var string
     */
    protected $_defaultStmtClass = 'Varien_Db_Statement_Sqlsrv';

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
    protected $_debug               = false;

    /**
     * Minimum query duration time to be logged
     *
     * @var float
     */
    protected $_logQueryTime        = 0.001;

    /**
     * Log all queries (ignored minimum query duration time)
     *
     * @var bool
     */
    protected $_logAllQueries       = false;

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
    protected $_debugFile           = 'var/debug/sqlsrv.log';

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
        'PARAMETER', 'PARAMETERS', 'PARTIAL', 'PASCAL', 'PATH', 'PERCENT', 'PLAN', 'POSITION', 'POSTFIX', 'PRECISION',
        'PREFIX', 'PREORDER', 'PREPARE', 'PRESERVE', 'PRIMARY', 'PRINT', 'PRIOR', 'PRIVILEGES', 'PROC', 'PROCEDURE',
        'PUBLIC', 'RAISERROR', 'READ', 'READS', 'READTEXT', 'REAL', 'RECONFIGURE', 'RECURSIVE', 'REF', 'REFERENCES',
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
     * MS SQL Server column data type to Table DDL type pairs
     *
     * @var array
     */
    protected $_ddlColumnTypes      = array(
        Varien_Db_Ddl_Table::TYPE_BOOLEAN       => 'boolean',
        Varien_Db_Ddl_Table::TYPE_SMALLINT      => 'smallint',
        Varien_Db_Ddl_Table::TYPE_INTEGER       => 'int',
        Varien_Db_Ddl_Table::TYPE_BIGINT        => 'bigint',
        Varien_Db_Ddl_Table::TYPE_FLOAT         => 'float',
        Varien_Db_Ddl_Table::TYPE_DECIMAL       => 'decimal',
        Varien_Db_Ddl_Table::TYPE_NUMERIC       => 'decimal',
        Varien_Db_Ddl_Table::TYPE_DATE          => 'datetime',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP     => 'datetime',
        Varien_Db_Ddl_Table::TYPE_TEXT          => 'text',
        Varien_Db_Ddl_Table::TYPE_BLOB          => 'text',
    );

    /**
     * Allowed interval units array
     *
     * @var array
     */
    protected $_intervalUnits = array(
        self::INTERVAL_YEAR     => 'year',
        self::INTERVAL_MONTH    => 'month',
        self::INTERVAL_DAY      => 'day',
        self::INTERVAL_HOUR     => 'hour',
        self::INTERVAL_MINUTE   => 'minute',
        self::INTERVAL_SECOND   => 'second',
    );

    /**
     * Creates a PDO DSN for the adapter from $this->_config settings.
     *
     * @return string
     */
    protected function _dsn()
    {
        unset($this->_config['active']);
        unset($this->_config['model']);
        unset($this->_config['initStatements']);
        unset($this->_config['type']);
        return parent::_dsn();
    }

     /**
     *
     * @return void
     * @throws Zend_Db_Adapter_Sqlsrv_Exception
     */
    protected function _connect()
    {
        if (is_resource($this->_connection)) {
            // connection already exists
            return;
        }

        if (!isset($this->_config['driver_options']) || !is_array($this->_config['driver_options'])) {
            $this->_config['driver_options'] = array();
        }
        $this->_config['driver_options']['ReturnDatesAsStrings'] = "1";

        $this->_debugTimer();
        parent::_connect();

        $this->query('SET TEXTSIZE 2147483647');
        $this->query('SET LANGUAGE us_english');

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
        $this->_transactionLevel ++;
        return $this;
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
        $this->_transactionLevel --;
        return $this;
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
        $this->_transactionLevel --;
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
     * Retrieve DDL object for new table
     *
     * @param string $tableName the table name
     * @param string $schemaName the database or schema name
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
        foreach ($options as $k => $v) {
            unset($options[$k]);
            $options[strtoupper($k)] = $v;
        }

        $cType      = null;
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
                }
                if ($options['LENGTH'] <= 1024) {
                    $cType = $ddlType == Varien_Db_Ddl_Table::TYPE_TEXT ? 'varchar' : 'varbinary';
                    $cType = sprintf('%s(%d)', $cType, $options['LENGTH']);
                } else {
                    $cType = $ddlType == Varien_Db_Ddl_Table::TYPE_TEXT ? 'text' : 'image';
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
                $cDefault = new Zend_Db_Expr('(getdate())');
            } else if ($cDefault == Varien_Db_Ddl_Table::TIMESTAMP_UPDATE) {
                $cDefault = new Zend_Db_Expr('/*0 ON UPDATE CURRENT_TIMESTAMP*/');
            } else if ($cDefault == Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE) {
                $cDefault = new Zend_Db_Expr('(getdate())/*CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP*/');
            }
        } else if (is_null($cDefault) && $cNullable) {
            $cDefault = new Zend_Db_Expr('NULL');
        }

        $colDef =  sprintf('%s%s%s%s',
            $cType,
            $cNullable ? ' NULL' : ' NOT NULL',
            $cDefault !== false ? $this->quoteInto(' default ?', $cDefault) : '',
            $cIdentity ? ' identity (1,1)' : ''
        );

        return $colDef;
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
            $definition[] = sprintf('  PRIMARY KEY (%s)', join(', ', $primary));
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
     * Retrieve table options definition array for create table
     *
     * @param Varien_Db_Ddl_Table $table
     * @return array
     */
    protected function _getOptionsDefinition(Varien_Db_Ddl_Table $table)
    {
        $definition = array();
        $tableProps = array(
            'comment'           => '/*COMMENT=\'%s\'*/',
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
     * Disable all table constraints
     *
     * @param string $tableName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    public function disableTableConstraints($tableName, $schemaName)
    {
        $this->raw_query(sprintf('ALTER TABLE %s NOCHECK CONSTRAINT ALL',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName))));
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
        $this->raw_query(sprintf('ALTER TABLE %s CHECK CONSTRAINT ALL',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName))));
        return $this;
    }

    /**
     * Disable all Db constraints
     *
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    public function disableAllDbConstraints()
    {
        $this->raw_query("sp_MSforeachtable 'ALTER TABLE ? NOCHECK CONSTRAINT ALL' ");
        return $this;
    }

    /**
     * Enable all Db table constraints
     *
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    public function enableAllDbConstraints()
    {
        $this->raw_query("sp_MSforeachtable 'ALTER TABLE ? NOCHECK CONSTRAINT ALL' ");
        return $this;
    }

     /**
     * Retrieve table indexes definition array for create table
     *
     * @param Varien_Db_Ddl_Table $table
     * @return array
     */
    protected function _createIndexes(Varien_Db_Ddl_Table $table)
    {
        $indexes    = $table->getIndexes();

        if (!empty($indexes)) {
            foreach ($indexes as $indexData) {
                if ($indexData['UNIQUE']) {
                    continue;
                }
                $columns = array();
                foreach ($indexData['COLUMNS'] as $columnData) {
                    $columns[] = $columnData['NAME'];
                }
                $this->addIndex($this->quoteIdentifier($table->getName()), $indexData['INDEX_NAME'], $columns);
            }
        }
    }

    /**
     * Create Foreign Key Cascade actions
     *
     * @param Varien_Db_Ddl_Table $table
     */
    protected function _createForeignKeysActions(Varien_Db_Ddl_Table $table)
    {
        $foreignKeys = $table->getForeignKeys();
        $fkActions = array (Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_SET_NULL);

        if (!empty($foreignKeys)) {
            foreach ($foreignKeys as $fkData) {

                if (in_array($fkData['ON_DELETE'], $fkActions)) {
                    $this->_addForeignKeyDeleteAction($table->getName(), $fkData['COLUMN_NAME'],
                            $fkData['REF_TABLE_NAME'], $fkData['REF_COLUMN_NAME'], $fkData['ON_DELETE']);
                }

                if (in_array($fkData['ON_UPDATE'], $fkActions)) {
                    $this->_addForeignKeyUpdateAction($table->getName(), $fkData['COLUMN_NAME'],
                            $fkData['REF_TABLE_NAME'], $fkData['REF_COLUMN_NAME'], $fkData['ON_UPDATE']);
                }
            }
        }
    }

    /**
     * Retrieve table unique constraints definition array for create table
     *
     * @param Varien_Db_Ddl_Table $table
     * @return array
     */
    protected function _getUniqueConstraintsDefinition(Varien_Db_Ddl_Table $table)
    {
        $definition = array();

        $constraints    = $table->getIndexes();

        if (!empty($constraints)) {
            foreach ($constraints as $constraintData) {
                if ($constraintData['UNIQUE']) {
                    $columns = array();

                    foreach ($constraintData['COLUMNS'] as $columnData) {
                        $column = $this->quoteIdentifier($columnData['NAME']);
                        $columns[] = $column;
                    }
                    $definition[] = sprintf('  CONSTRAINT "%s" UNIQUE (%s)',
                        $this->quoteIdentifier($constraintData['INDEX_NAME']),
                        join(', ', $columns));
                }
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

        if ($table->getOption('comment')) {
            $this->_createTableComment($table->getName(), $table->getOption('comment'));
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
     * @return Varien_Db_Adapter_Interface
     */
    public function truncateTable($tableName, $schemaName = null)
    {
        if (!$this->isTableExists($tableName, $schemaName)) {
            throw new Varien_Exception(sprintf('Table "%s" is not exists', $tableName));
        }

        $query = sprintf(
            'TRUNCATE TABLE %s',
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
            $this->saveDdlCache($cacheKey, self::DDL_DESCRIBE, $ddl);
        }

        return $ddl;
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

        $oldTable = $this->_getTableName($oldTableName, $schemaName);
        $newTable = $this->_getTableName($newTableName, $schemaName);

        $query = sprintf('EXEC SP_RENAME %s , %s', $oldTable, $newTable);
        $this->raw_query($query);

        $this->resetDdlCache($oldTableName, $schemaName);

        return true;
    }

    /**
     * Add new column to the table.
     *
     * Generally $defintion must be array with column data to keep this call cross-DB compatible.
     * Using string as $definition is allowed only for concrete DB adapter.
     *
     * @param string $tableName
     * @param string $columnName
     * @param array|string $definition  string specific or universal array DB Server definition
     * @param string $schemaName
     * @return Varien_Db_Adapter_Interface
     */
    public function addColumn($tableName, $columnName, $definition, $schemaName = null)
    {
        if ($this->tableColumnExists($tableName, $columnName, $schemaName)) {
            return true;
        }

        if (is_array($definition)) {
            $definition = array_change_key_case($definition, CASE_UPPER);
            if (empty($definition['COMMENT'])) {
                throw new Zend_Db_Exception("Impossible to create a column without comment.");
            }

            $definition = $this->_getColumnDefinition($definition);
        }

        $sql = sprintf('ALTER TABLE %s ADD %s %s',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
            $this->quoteIdentifier($columnName),
            $definition
        );

        $result = $this->raw_query($sql);
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
     * @param boolean $flushData        flush table statistic
     * @param string $schemaName
     * @return Varien_Db_Adapter_Interface
     */
    public function changeColumn($tableName, $oldColumnName, $newColumnName, $definition, $flushData = false,
        $schemaName = null)
    {
        $this->renameColumn($tableName, $oldColumnName, $newColumnName, $schemaName);
        $this->modifyColumn($tableName, $newColumnName, $definition, $flushData, $schemaName);

        return $this;
    }

    /**
     * Rename column
     *
     * @param string $tableName
     * @param string $oldColumnName
     * @param string $newColumnName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Interface
     */
    public function renameColumn($tableName, $oldColumnName, $newColumnName, $schemaName = null)
    {
        if ($oldColumnName == $newColumnName) {
            return $this;
        }

        if (!$this->tableColumnExists($tableName, $oldColumnName, $schemaName)) {
            throw new Varien_Exception(sprintf('Column "%s" does not exists on table "%s"', $oldColumnName, $tableName));
        }

        if ($this->tableColumnExists($tableName, $newColumnName, $schemaName)) {
            throw new Varien_Exception(sprintf('Column "%s" already exists on table "%s"', $newColumnName, $tableName));
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
     * Modify the column definition
     *
     * @param string $tableName
     * @param string $columnName
     * @param array|string $definition
     * @param boolean $flushData        flush table statistic for Mysql only
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

        $sql = sprintf('ALTER TABLE %s ALTER COLUMN i %s %s',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
            $this->quoteIdentifier($columnName),
            $definition);

        $result = $this->raw_query($sql);

        $this->resetDdlCache($tableName, $schemaName);

        return $result;

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

        $alterDrop = array();
        $foreignKeys = $this->getForeignKeys($tableName, $schemaName);
        foreach ($foreignKeys as $fkProp) {
            if ($fkProp['COLUMN_NAME'] == $columnName) {
                $alterDrop[] = sprintf('DROP CONSTRAINT [%s]', $this->quoteIdentifier($fkProp['FK_NAME']));
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
     * @param string $chemaName
     * @return boolean
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
     */
    protected function _getDdlScriptCreateFullText($tableName, $fields, $schemaName = null)
    {
        $primaryKey = false;
        foreach ($this->getIndexList($tableName, $schemaName) as $index) {
            if ($index['INDEX_TYPE'] == 'PRIMARY') {
                $primaryKey = $index['INDEX_NAME'];
            }
        }

        if (!$primaryKey) {
            throw new Varien_Db_Exception('Cannot create full text index for table without primary key');
        }

        return sprintf('CREATE FULLTEXT INDEX ON (%s) KEY INDEX %s ON %s',
            $fields,
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
     * @return boolean
     */
    protected function _getDdlScriptDropPrimaryKey($tableName, $indexName, $schemaName = null)
    {
        return sprintf('ALTER TABLE %s DROP CONSTRAINT [%s]',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
            $this->quoteIdentifier($indexName)
        );
    }

    /**
     * Return Ddl script for create primary key
     *
     * @param string $tableName
     * @param string $indexName
     * @param string $fields        the quoted fields list
     * @param string schemaName
     * @return boolean
     */
    protected function _getDdlScriptCreatePrimaryKey($tableName, $indexName, $fields, $schemaName = null)
    {
        return sprintf('ALTER TABLE %s ADD CONSTRAINT [%s] PRIMARY KEY CLUSTERED (%s)',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
            $this->quoteIdentifier($indexName),
            $fields
        );
    }

    /**
     * Return Ddl script for drop index
     *
     * @param string $tableName
     * @param string $indexName
     * @param string schemaName
     * @return boolean
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
     * @return boolean
     */
    protected function _getDdlScriptCreateIndex($tableName, $indexName, $fields, $isUniqueIndex, $schemaName = null)
    {
        return sprintf('CREATE %s INDEX [%s] ON %s ( %s )',
            ($isUniqueIndex === true ? 'UNIQUE' : ''),
            $this->quoteIdentifier($indexName),
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
            $fields
        );
    }

    /**
     * Add new index to table name
     *
     * @param string $tableName
     * @param string $indexName
     * @param string $fields        the validated and queted columns list (SQL)
     * @param string $indexType     the index type
     * @param string $schemaName
     * @return Varien_Db_Adapter_Interface
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
        foreach ($this->describeTable($tableName, $schemaName) as $column) {
            $columns[$column['COLUMN_NAME']] = $column['COLUMN_NAME'];
        }

        $keyList = $this->getIndexList($tableName, $schemaName);

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

        // Drop index if exists
        if (isset($keyList[strtoupper($indexName)])) {
            $this->dropIndex($tableName, $indexName, $schemaName);
        }

        // Create index
        $this->_createIndex($tableName, $indexName, $indexType, $fieldSql, $schemaName);

        return true;
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

        switch (strtolower($keyName)) {
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

        return true;
    }

    /**
     * Returns the table index information
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
            $query = "
                SELECT
                    si.name                 AS Key_name,
                    CASE
                        WHEN is_unique = 1 THEN 0
                        ELSE 1
                    END                     AS Non_unique,
                    sc.name                 AS Column_name,
                    CASE
                        WHEN ( si.type = 1 AND si.is_primary_key = 1 ) then 'primary'
                        WHEN ( si.type = 2 AND si.is_unique = 1 ) then 'unique'
                        WHEN ( si.type = 2 AND si.is_unique = 0 ) then 'index'
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

                $upperKeyName = strtoupper($row[$fieldKeyName]);
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
                        'INDEX_TYPE'    => strtoupper($indexType),
                        'INDEX_METHOD'  => strtoupper($indexType),
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
        // quote table and column
        $tableName      = $this->quoteIdentifier($tableName);
        $refTableName   = $this->quoteIdentifier($refTableName);
        $columnName     = $this->quoteIdentifier($columnName);
        $refColumnName  = $this->quoteIdentifier($refColumnName);

        if (strtoupper($onDelete) == 'CASCADE' || strtoupper($onDelete) == 'RESTRICT') {
            $sql = " UPDATE {$tableName} t1 SET t1.code = NULL ";
        } else if (strtoupper($onDelete) == 'SET NULL') {
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
     * @return Varien_Db_Adapter_Interface
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

        if (!is_null($onDelete)) {
            $query .= ' ON DELETE ' . strtoupper($onDelete);
        }
        if (!is_null($onUpdate)) {
            $query .= ' ON UPDATE ' . strtoupper($onUpdate);
        }

        $this->resetDdlCache($tableName, $schemaName);
        return $this->raw_query($query);
    }

    /**
     * Drop the Foreign Key from table
     *
     * @param string $tableName
     * @param string $fkName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Interface
     */
    public function dropForeignKey($tableName, $fkName, $schemaName = null)
    {
        $foreignKeys = $this->getForeignKeys($tableName, $schemaName);

        if (!isset($foreignKeys[strtoupper($fkName)])) {
            return $this;
        }

        $fkActions = array (Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_SET_NULL);
        // drop cascade triggers
        $columnName = "";
        foreach ($foreignKeys as $foreignKey) {
            if ($fkName == $foreignKey['FK_NAME']) {
                $columnName = $foreignKey['COLUMN_NAME'];

                if (in_array($foreignKey['ON_UPDATE'], $fkActions)) {
                     $this->raw_fetchRow(sprintf(" DROP TRIGGER %s",
                     $this->quoteIdentifier($this->_getTriggerName($tableName, $columnName))));
                }
                if (in_array($foreignKey['ON_DELETE'], $fkActions)) {
                     $this->raw_fetchRow(sprintf(" DROP TRIGGER %s",
                     $this->quoteIdentifier($this->_getTriggerName($tableName, $columnName, self::TRIGGER_DELETE_CASCADE))));
                }
            }
        }

        if (isset($foreignKeys[strtoupper($fkName)])) {
            $sql = sprintf('ALTER TABLE %s DROP CONSTRAINT %s',
                $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
                $this->quoteIdentifier($foreignKeys[strtoupper($fkName)]['FK_NAME']));

            $this->resetDdlCache($tableName, $schemaName);

            $this->raw_query($sql);
        }

        return $this;
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
            $query = "
                SELECT sfk.object_id,
                    sfk.name                            AS fk_name,
                    sop.name                            AS table_name,
                    scp.name                            AS column_name,
                    sor.name                            AS ref_table_name,
                    scr.name                            AS ref_column,
                    sfk.delete_referential_action_desc  AS on_delete,
                    sfk.update_referential_action_desc  AS on_update
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
            $sql = sprintf($query, $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)));
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

            if ( $index['INDEX_TYPE'] == 'PRIMARY' ) {
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
            if ( strtoupper($index['INDEX_TYPE']) == 'UNIQUE' ) {
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
                $groupCond[] = sprintf("%s = '%s'", $column, $data[$column]);
            }

            if (false !== ($k = array_search($column, $fields))) {
                unset($fields[$k]);
            }
        }

        if (!empty($groupCond) && $usePkCond) {
            $whereConditions[] = sprintf('(%s)', join(') AND (', $groupCond));
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

            if (false !== ($k = array_search($column, $fields))) {
                unset($fields[$k]);
            }
        }

        if (!empty($groupCond) && $useUnqCond) {
            $whereConditions[] = sprintf('(%s)', join(' AND ', $groupCond));
        }

        // check and prepare where condition
        if (empty($whereConditions)) {
            throw new Exception('Invalid primary or unique columns in merge data');
        }

        $where = sprintf('(%s)', join(') OR (', $whereConditions));
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

            $this->update($table, $bind, $where);
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
        $row = reset($data); // get first elemnt from data array
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
     * @param   array $columns  the data array column map
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
                $vals[] = sprintf('SELECT %s', join(',', $line));
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
        $this->query(sprintf('SET IDENTITY_INSERT %s ON', $this-> quoteIdentifier($table)));
        $result = parent::insert($table, $bind);
        $this->query(sprintf('SET IDENTITY_INSERT %s OFF', $this->quoteIdentifier($table)));
        return $result;
    }

    /**
     * Prepares and executes an SQL statement with bound data.
     *
     * @param  mixed  $sql  The SQL statement with placeholders.
     *                      May be a string or Zend_Db_Select.
     * @param  mixed  $bind An array of data to bind to the placeholders.
     * @return Zend_Db_Statement_Interface
     */
    public function query($sql, $bind = array())
    {
        $this->_debugTimer();
        try {
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
     * @return Varien_Db_Adapter_Interface
     */
    public function multiQuery($sql)
    {
//        $this->beginTransaction();
        try {
            $stmts = $this->_splitMultiQuery($sql);
            $result = array();
            foreach ($stmts as $stmt) {
                $result[] = $this->raw_query($stmt);
            }
//            $this->commit();
        } catch (Exception $e) {
//            $this->rollback();
            throw $e;
        }

        return $result;
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
        return new Zend_Db_Expr($this->quoteInto('CAST(? as datetime)', $date));
    }

    /**
     * Run additional environment before setup
     *
     * @return Varien_Db_Adapter_Interface
     */
    public function startSetup()
    {
        //$this->_addCommentProcedure();
        /**
         * @todo disable FK check etc
         */
    }

    /**
     * Run additional environment after setup
     *
     * @return Varien_Db_Adapter_Interface
     */
    public function endSetup()
    {
        /**
         * @todo restore state by FK check etc
         */
    }

    /**
     * Set cache adapter
     *
     * @param Zend_Cache_Backend_Interface $adapter
     * @return Varien_Db_Adapter_Interface
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
     */
    public function raw_query($sql)
    {
        /**
         * @todo fix message for MSSQL Error
         */
        $lostConnectionMessage = 'SQLSTATE[HY000]: General error: 2013 Lost connection to MySQL server during query';
        $tries = 0;
        do {
            $retry = false;
            try {
                $result = $this->query($sql);
            } catch (PDOException $e) {
                if ($tries < 10 && $e->getMessage() == $lostConnectionMessage) {
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
     * Allow DDL caching
     *
     * @return Varien_Db_Adapter_Interface
     */
    public function allowDdlCache()
    {
        $this->_isDdlCacheAllowed = true;
        return $this;
    }

    /**
     * Disallow DDL caching
     *
     * @return Varien_Db_Adapter_Interface
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
     * @return Varien_Db_Adapter_Interface
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
     * Save DDL data into cache
     *
     * @param string $tableCacheKey
     * @param int $ddlType
     * @return Varien_Db_Adapter_Interface
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
     * @todo FIND_IN_SET
     * @param string $fieldName
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
                            $from = $this->formatDate($condition['from']);
                        }
                    } else {
                        $from = $this->formatDate($condition['from']);
                    }
                    $query .= $this->quoteInto("{$fieldName} >= ?", $from);
                }
                if (isset($condition['to'])) {
                    $query .= empty($query) ? '' : ' AND ';

                    if (empty($condition['date'])) {
                        if (empty($condition['datetime'])) {
                            $to = $condition['to'];
                        } else {
                            $to = $this->formatDate($condition['to']);
                        }
                    } else {
                        $to = $this->formatDate($condition['to']);
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
                $query = $this->quoteInto("dbo.find_in_set(?, {$fieldName})", $condition['finset']);
            } else if (isset($condition['regexp'])) {
                $query = $this->quoteInto("dbo.regexp({$fieldName}, ?)", $condition['regexp']);
            } else if (isset($condition['seq']) || isset($condition['sneq'])) {
                $key = isset($condition['seq']) ? 'seq' : 'sneq';
                $value = $condition[$key];
                $key = $this->_transformStringSqlCondition($key, $value);
                $query = $this->prepareSqlCondition($fieldName, array($key => $value));
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
     */
    public function getCheckSql($condition, $true, $false)
    {
        return new Zend_Db_Expr("CASE WHEN {$condition} THEN {$true} ELSE {$false} END");
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
        return new Zend_Db_Expr(join($glue, $data));
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
        return new Zend_Db_Expr(sprintf($format, join(' AS v UNION ALL SELECT ', $data)));
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
        return new Zend_Db_Expr(sprintf($format, join(' AS v UNION ALL SELECT ', $data)));
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
        if (!isset($this->_intervalUnits[$unit])) {
            throw new Varien_Db_Exception(sprintf('Undefined interval unit "%s" specified', $unit));
        }
        return new Zend_Db_Expr(sprintf('DATEADD(%s, %d, %s)', $this->_intervalUnits[$unit], $interval, $date));
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
            throw new Varien_Db_Exception(sprintf('Undefined interval unit "%s" specified', $unit));
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
            if (in_array($upperValue, $this->_reservedWords)) {
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
     * Set comment on the table
     *
     * @param string $tableName
     * @param string $comment
     * @return Varien_Db_Adapter_Oracle
     */
    protected function _createTableComment($tableName, $comment)
    {
        $sqlExistsComment = sprintf("  SELECT COUNT(1) AS qty               \n"
            . "  FROM sys.extended_properties ep                            \n"
            . "  WHERE ep.name = 'TABLE_COMMENT'                            \n"
            . "    AND ep.major_id = OBJECT_ID(N'%s')                       \n",
             $tableName);
        $isExists = $this->raw_fetchRow($sqlExistsComment, 'qty');
        if($isExists == 0) {
           $this->query(sprintf(
                   "EXEC sp_addextendedproperty N'TABLE_COMMENT', '%s', N'user', N'dbo', N'table', N'%s', NULL, NULL",
                   $comment, $tableName));
        } else {
           $this->query(sprintf(
                   "EXEC sp_updateextendedproperty N'TABLE_COMMENT', '%s', N'user', N'dbo', N'table', N'%s', NULL, NULL \n",
                   $comment, $tableName));
        }

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
                return $column['DATA_TYPE'];
            }
        }
        return false;
    }

    /**
     * Retrieve trigger name for cascade update / delete
     *
     * @param string $tableName
     * @param string $fieldName
     * @return string
     */
    protected function _getTriggerName($tableName, $fieldName, $triggerType = self::TRIGGER_UPDATE_CASCADE)
    {
        $hash = sprintf('trigger-%s-%s-%s', $triggerType, $tableName, $fieldName);
        return substr(strtoupper(md5($hash)), 1, -1);
    }

    /**
     * Create trigger for cascade update
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $refTableName
     * @param string $refColumnName
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    protected function _addForeignKeyUpdateAction($tableName, $columnName, $refTableName, $refColumnName, $fkAction)
    {
        $refColumnDataType  = $this->_getColumnDataType($refTableName, $refColumnName);
        $triggerName        = $this->_getTriggerName($tableName, $columnName);

       if ($refColumnDataType == false) {
                throw new Exception('Unknown column data type!');
       }

        $sqlTrigger = "                                                     \n"
            . "CREATE TRIGGER [{$triggerName}]                              \n"
            . "    ON  {$refTableName}                                      \n"
            . "    AFTER UPDATE                                             \n"
            . "AS                                                           \n"
            . "DECLARE                                                      \n"
            . (
                    $fkAction == Varien_Db_Ddl_Table::ACTION_CASCADE ?
                "    @new_{$refColumnName} {$refColumnDataType},            \n" : ""
              )

            . "    @old_{$refColumnName} {$refColumnDataType}               \n"
            . "BEGIN                                                        \n"
            . "    SET NOCOUNT ON;                                          \n"
            . (
                    $fkAction == Varien_Db_Ddl_Table::ACTION_CASCADE ?
                "    SELECT @new_{$refColumnName} = {$refColumnName}        \n"
              . "    FROM inserted                                          \n" : ""
              )

            . "    SELECT @old_{$refColumnName}  = {$refColumnName}         \n"
            . "    FROM deleted                                             \n"
            . "    UPDATE {$tableName}                                      \n"
            . (
                    $fkAction == Varien_Db_Ddl_Table::ACTION_CASCADE ?
                "    SET {$columnName} = @new_{$refColumnName}              \n":
                "    SET {$columnName} = NULL                               \n"
               )
            . "    WHERE {$columnName} = @old_{$refColumnName};             \n"
            . "END                                                          \n";

        $this->query("SET ANSI_NULLS ON");
        $this->query("SET QUOTED_IDENTIFIER ON");
        $this->query($sqlTrigger);

        return $this;
    }

    /**
     * Create trigger for cascade delete
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $refTableName
     * @param string $refColumnName
     * @return Varien_Db_Adapter_Pdo_Mssql
     */
    protected function _addForeignKeyDeleteAction($tableName, $columnName, $refTableName, $refColumnName, $fkAction)
    {
        $refColumnDataType  = $this->_getColumnDataType($refTableName, $refColumnName);
        $triggerName        = $this->_getTriggerName($tableName, $columnName, self::TRIGGER_DELETE_CASCADE);

       if ($refColumnDataType == false) {
                throw new Exception('Unknown column data type!');
       }

        $sqlTrigger = "CREATE TRIGGER [{$triggerName}]                      \n"
            . "    ON  {$refTableName}                                      \n"
            . "    AFTER DELETE                                             \n"
            . "AS                                                           \n"
            . "DECLARE                                                      \n"
            . "    @old_{$refColumnName} {$refColumnDataType}               \n"
            . "BEGIN                                                        \n"
            . "    SET NOCOUNT ON;                                          \n"
            . "    SELECT @old_{$refColumnName} = {$refColumnName}          \n"
            . "    FROM deleted                                             \n"
            . (
                    $fkAction == Varien_Db_Ddl_Table::ACTION_CASCADE ?
                "    DELETE FROM {$tableName}                               \n"
              . "    WHERE {$columnName} = @old_{$refColumnName};           \n":
                "    UPDATE {$tableName}                                    \n"
              . "    SET {$columnName} = NULL                               \n"
              . "    WHERE {$columnName} = @old_{$refColumnName};           \n"
              )
            . "END                                                          \n"
            . "                                                             \n";

        $this->query("SET ANSI_NULLS ON");
        $this->query("SET QUOTED_IDENTIFIER ON");
        $this->query($sqlTrigger);

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
         $diff = strlen($hash) + strlen($prefix) -  $maxCharacters;
         $superfluous = $diff / 2;
         $odd = $diff % 2;
         $hash = substr($hash, $superfluous, -($superfluous+$odd));
         return $prefix.$hash;
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
                    $tableName = $prefix.$hash;
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
            $hash = $prefix.$hash;
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
     * @throws Zend_Db_Adapter_Sqlsrv_Exception
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

        $sql = preg_replace('/^SELECT\s+(DISTINCT\s)?/i', 'SELECT $1TOP ' . ($count+$offset) . ' ', $sql);

        if ($offset + $count == $offset + 1) {
            $query = $sql;
        } else {
            $query = sprintf('
                SELECT z2.*
                FROM (
                    SELECT z1.*, ROW_NUMBER() OVER ( ORDER BY  RAND()) AS varien_db_rownum
                    FROM (%s) z1) z2
                WHERE z2.varien_db_rownum >= %d', $sql,  $offset + 1);
        }

        return $query;
    }

    /**
     * Stop updating nonunique indexes
     *
     * @param string $tableName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Interface
     */
    public function disableTableKeys($tableName, $schemaName = null)
    {
        $indexList = $this->getIndexList($tableName, $schemaName);
        $tableName = $this->_getTableName($tableName, $schemaName);
        foreach ($indexList as $indexProp) {
            if (strtolower($indexProp['INDEX_TYPE']) != Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX) {
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
     * @return Varien_Db_Adapter_Interface
     */
    public function enableTableKeys($tableName, $schemaName = null)
    {
        $indexList = $this->getIndexList($tableName, $schemaName);
        $tableName = $this->_getTableName($tableName, $schemaName);
        foreach ($indexList as $indexProp) {
            if (strtolower($indexProp['INDEX_TYPE']) != Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX) {
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
        $i         = 0;
        $colsPart  = $select->getPart(Zend_Db_Select::COLUMNS);
        if (count($colsPart) != count($fields)) {
            throw new Varien_Db_Exception('Wrong columns count in SELECT for INSERT,');
        }
        foreach ($colsPart as &$colData) {
            $colData[2] = $fields[$i];
            $i ++;
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

            if (false !== ($k = array_search($pkColumn, $updateCols))) {
                unset($updateCols[$k]);
            }
        }

        if (!empty($groupCond) && $usePkCond) {
            $whereCond[] = sprintf('(%s)', join(') AND (', $groupCond));
        }

        // Obtain unique indexes fields
        foreach ($indexes as $indexData) {
            if (strtolower($indexData['INDEX_TYPE']) != Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE) {
                continue;
            }

            $groupCond  = array();
            $useUnqCond = true;
            foreach($indexData['COLUMNS_LIST'] as $column) {
                if (!in_array($column, $insertCols)) {
                    $useUnqCond = false;
                }
                if (false !== ($k = array_search($column, $updateCols))) {
                    unset($updateCols[$k]);
                }
                $groupCond[] = sprintf('t3.%1$s = t2.%1$s', $this->quoteIdentifier($column));
            }
            if (!empty($groupCond) && $useUnqCond) {
                $whereCond[] = sprintf('(%s)', join(' AND ', $groupCond));
            }
        }

        // validate where condition
        if (empty($whereCond)) {
            throw new Varien_Db_Exception('Invalid primary or unique columns in merge data');
        }

        // prepare insert columns condition
        $insertCond = array_map(array($this, 'quoteIdentifier'), $insertCols);

        $query = sprintf('INSERT INTO %1$s (%2$s) SELECT * FROM (%3$s) t2'
            . ' WHERE NOT EXISTS (SELECT 1 FROM %1$s t3 WHERE (%4$s))',
            $this->quoteIdentifier($table),
            join(', ', $insertCond),
            $select->assemble(),
            join(') OR (', $whereCond)
        );

        if ($mode == self::INSERT_ON_DUPLICATE && $updateCols) {
            $updateCond = array();
            foreach ($updateCols as $updateCol) {
                $updateCond[] = sprintf('t3.%1$s = t2.%1$s', $this->quoteIdentifier($updateCol));
            }

            $query = sprintf('%s UPDATE t3 SET %s FROM (%s) t2 INNER JOIN %s t3 ON (%s)',
                $query,
                join(', ', $updateCond),
                $select->assemble(),
                $this->quoteIdentifier($table),
                join(') OR (', $whereCond)
            );
        }

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
        $query = sprintf('INSERT INTO %s', $this->quoteIdentifier($table));
        if ($fields) {
            $columns = array_map(array($this, 'quoteIdentifier'), $fields);
            $query .= sprintf(' (%s)', join(', ', $columns));
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
            $updateSet[] = $this->quoteIdentifier(array($tableAlias, $alias)) . " = {$column}";
        }

        if (!$updateSet) {
            throw new Varien_Db_Exception('Undefined columns for UPDATE');
        }

        $joinSelect = clone $select;
        $joinSelect->reset(Zend_Db_Select::DISTINCT);
        $joinSelect->reset(Zend_Db_Select::COLUMNS);
        $joinSelect->from(array($tableAlias => $tableName), null);

        $query = sprintf('UPDATE %s SET %s %s',
            $this->quoteIdentifier($tableAlias),
            join(', ', $updateSet),
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
            throw new Exception('Invalid table name or table alias in select');
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
}
