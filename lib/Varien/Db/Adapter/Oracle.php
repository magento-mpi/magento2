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
 * Oracle DB Adapter
 *
 * @category    Varien
 * @package     Varien_Db
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Db_Adapter_Oracle extends Zend_Db_Adapter_Oracle implements Varien_Db_Adapter_Interface
{
    const DEBUG_CONNECT             = 0;
    const DEBUG_TRANSACTION         = 1;
    const DEBUG_QUERY               = 2;

    const NLS_DATETIME_FORMAT       = 'YYYY-MM-DD HH24:MI:SS';
    const NLS_DATE_FORMAT           = 'YYYY-MM-DD';

    const DDL_DESCRIBE              = 1;
    const DDL_CREATE                = 2;
    const DDL_INDEX                 = 3;
    const DDL_FOREIGN_KEY           = 4;
    const DDL_CACHE_PREFIX          = 'DB_ORACLE_DDL';
    const DDL_CACHE_TAG             = 'DB_ORACLE_DDL';

    const PACKAGE_CASCADE_ACTION    = 'ac';
    const TRIGGER_IDENTITY          = 'id';
    const TRIGGER_TIME_UPDATE       = 'ts_up';
    const TRIGGER_BEFORE_UPDATE     = 'b_up';
    const TRIGGER_BEFORE_UPDATE_ER  = 'b_up_er';
    const TRIGGER_AFTER_UPDATE      = 'a_up';

    const LENGTH_TABLE_NAME         = 30;
    const LENGTH_INDEX_NAME         = 25;
    const LENGTH_FOREIGN_NAME       = 30;

    const SQL_FOR_UPDATE            = 'FOR UPDATE';

    /**
     * Default class name for a DB statement.
     *
     * @var string
     */
    protected $_defaultStmtClass = 'Varien_Db_Statement_Oracle';

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
     * Autoincrement for bind value. Used by query preparation routine and regexp callback.
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
    protected $_logCallStack        = true;

    /**
     * Path to SQL debug data log
     *
     * @var string
     */
    protected $_debugFile           = 'var/debug/oracle.log';

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
     * Current schema name
     *
     * @var string
     */
    protected $_schemaName;

    /**
     * Oracle column - Table DDL type pairs
     *
     * @var array
     */
    protected $_ddlColumnTypes      = array(
        Varien_Db_Ddl_Table::TYPE_BOOLEAN       => 'SMALLINT',
        Varien_Db_Ddl_Table::TYPE_SMALLINT      => 'SMALLINT',
        Varien_Db_Ddl_Table::TYPE_INTEGER       => 'INTEGER',
        Varien_Db_Ddl_Table::TYPE_BIGINT        => 'NUMBER',
        Varien_Db_Ddl_Table::TYPE_FLOAT         => 'FLOAT',
        Varien_Db_Ddl_Table::TYPE_DECIMAL       => 'NUMBER',
        Varien_Db_Ddl_Table::TYPE_NUMERIC       => 'NUMBER',
        Varien_Db_Ddl_Table::TYPE_DATE          => 'DATE',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP     => 'TIMESTAMP',
        Varien_Db_Ddl_Table::TYPE_TEXT          => 'VARCHAR2',
        Varien_Db_Ddl_Table::TYPE_BLOB          => 'CLOB',
        Varien_Db_Ddl_Table::TYPE_VARBINARY     => 'CLOB'
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
     * Oracle Database Reserved Words
     * All words in upper case
     *
     * @var array
     */
    protected $_reservedWords       = array('ACCESS', 'ADD', 'ALL', 'ALTER', 'AND', 'ANY', 'AS', 'ASC', 'AUDIT',
        'BETWEEN', 'BY', 'CHAR', 'CHECK', 'CLUSTER', 'COLUMN', 'COMMENT', 'COMPRESS', 'CONNECT', 'CREATE', 'CURRENT',
        'DATE', 'DECIMAL', 'DEFAULT', 'DELETE', 'DESC', 'DISTINCT', 'DROP', 'ELSE', 'EXCLUSIVE', 'EXISTS', 'FILE',
        'FLOAT', 'FOR', 'FROM', 'GRANT', 'GROUP', 'HAVING', 'IDENTIFIED', 'IMMEDIATE', 'IN', 'INCREMENT', 'INDEX',
        'INITIAL', 'INSERT', 'INTEGER', 'INTERSECT', 'INTO', 'IS', 'LEVEL', 'LIKE', 'LOCK', 'LONG', 'MAXEXTENTS',
        'MINUS', 'MLSLABEL', 'MODE', 'MODIFY', 'NOAUDIT', 'NOCOMPRESS', 'NOT', 'NOWAIT', 'NULL', 'NUMBER', 'OF',
        'OFFLINE', 'ON', 'ONLINE', 'OPTION', 'OR', 'ORDER', 'PCTFREE', 'PRIOR', 'PRIVILEGES', 'PUBLIC', 'RAW', 'RENAME',
        'RESOURCE', 'REVOKE', 'ROW', 'ROWID', 'ROWNUM', 'ROWS', 'SELECT', 'SESSION', 'SET', 'SHARE', 'SIZE', 'SMALLINT',
        'START', 'SUCCESSFUL', 'SYNONYM', 'SYSDATE', 'TABLE', 'THEN', 'TO', 'TRIGGER', 'UID', 'UNION', 'UNIQUE',
        'UPDATE', 'USER', 'VALIDATE', 'VALUES', 'VARCHAR', 'VARCHAR2', 'VIEW', 'WHENEVER', 'WHERE', 'WITH');

    /**
     * Begin new DB transaction for connection
     *
     * @return Varien_Db_Adapter_Oracle
     */
    public function beginTransaction()
    {
        if ($this->_transactionLevel == 0) {
            $this->_debugTimer();
            parent::beginTransaction();
            $this->_debugStat(self::DEBUG_TRANSACTION, 'BEGIN');
        }
        ++$this->_transactionLevel;

        return $this;
    }

    /**
     * Commit DB transaction
     *
     * @return Varien_Db_Adapter_Oracle
     */
    public function commit()
    {
        if ($this->_transactionLevel == 1) {
            $this->_debugTimer();
            parent::commit();
            $this->_debugStat(self::DEBUG_TRANSACTION, 'COMMIT');
        }
        --$this->_transactionLevel;

        return $this;
    }

    /**
     * Rollback DB transaction
     *
     * @return Varien_Db_Adapter_Oracle
     */
    public function rollBack()
    {
        if ($this->_transactionLevel == 1) {
            $this->_debugTimer();
            parent::rollBack();
            $this->_debugStat(self::DEBUG_TRANSACTION, 'ROLLBACK');
        }
        --$this->_transactionLevel;

        return $this;
    }

    /**
     * Prepares and executes an SQL statement with bound data.
     *
     * @param  mixed  $sql  The SQL statement with placeholders.
     *                      May be a string or Zend_Db_Select.
     * @param  mixed  $bind An array of data or data itself to bind to the placeholders.
     * @return Zend_Db_Statement_Interface
     * @throws Zend_Db_Statement_Oracle_Exception
     */
    public function query($sql, $bind = array())
    {
        $this->_debugTimer();
        try {
            $this->_prepareQuery($sql, $bind);
            $result = parent::query($sql, $bind);
        } catch (Exception $e) {
            $this->_debugStat(self::DEBUG_QUERY, $sql, $bind);
            $this->_debugException($e);
        }
        $this->_debugStat(self::DEBUG_QUERY, $sql, $bind, $result);

        return $result;
    }

    /**
     * Prepare SQL query by converting positional bind to named one,
     * because Oracle doesn't support positional binds
     *
     * @param Zend_Db_Select|string $sql
     * @param mixed $bind
     * @return Varien_Db_Adapter_Oracle
     */
    protected function _prepareQuery(&$sql, &$bind = array())
    {
        // Maybe nothing to bind
        if (!is_array($bind)) {
            $bind = array($bind);
        }
        if (!$bind) {
            return $this;
        }

        // Maybe we have no positional placeholders
        if ($sql instanceof Zend_Db_Select) {
            $sql = $sql->assemble();
        }
        if (strpos($sql, '?') === false) {
            return $this;
        }

        // Maybe we have named bind
        $isNamedBind = false;
        foreach ($bind as $k => $v) {
            if (!is_int($k)) {
                $isNamedBind = true;
                if ($k[0] != ':') {
                    $bind[":{$k}"] = $v;
                    unset($bind[$k]);
                }
            }
        }

        if ($isNamedBind) {
            return $this;
        }

        /**
         * Ok, we have positional bind and placeholders for parameters. Move '?', that are not placeholders, to named
         * bind parameters. And then convert resulting mixed bind to named one.
         */
        $this->_bindParams = $bind; // Used by callback
        $sql = preg_replace_callback('#(([\'"])((\\2)|((.*?[^\\\\])\\2)))#',
            array($this, 'proccessBindCallback'),
            $sql);
        Varien_Exception::processPcreError();
        $bind = $this->_bindParams;

        return $this->_convertMixedBind($sql, $bind);
    }

    /**
     * Callback function for preparation of query and bind by regexp.
     * Checks query parameters for '?' placeholders and moves them to named bind parameters.
     * This method writes to $_bindParams, where query bind parameters are kept.
     * This method requires further normalizing.
     *
     * @param array $matches
     * @return string
     */
    public function proccessBindCallback($matches)
    {
        if (isset($matches[6]) && strpos($matches[6], '?') !== false) {
            $bindName = ':mage_bind_var_' . (++$this->_bindIncrement);
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
     * Changes query and converts mixed bind to named one
     *
     * @param string $sql
     * @param array $bind
     * @return Varien_Db_Adapter_Oracle
     */
    protected function _convertMixedBind(&$sql, &$bind)
    {
        $parts = explode('?', $sql);
        $sqlResult = $parts[0];
        $partIndex = 1;
        $totalParts = count($parts);
        $bindResult = array();
        foreach ($bind as $k => $v) {
            if (is_int($k)) {
                if ($partIndex >= $totalParts) {
                    $message = "Number of '?' placeholders in '{$sql}' is less than number"
                    . " of positional bind parameters";
                    throw new Zend_Db_Adapter_Oracle_Exception($message);
                }
                $bindName = ':mage_bind_var_' . (++$this->_bindIncrement);
                $part = $parts[$partIndex++];
                $sqlResult .= $bindName . $part;
            } else {
                $bindName = $k;
            }
            $bindResult[$bindName] = $v;
        }

        if ($partIndex < $totalParts) {
            $message = "Number of '?' placeholders in '{$sql}' is bigger than the number"
            . " of positional bind parameters";
            throw new Zend_Db_Adapter_Oracle_Exception($message);
        }

        $bind = $bindResult;
        $sql = $sqlResult;

        return $this;
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
     * Drop table from database
     *
     * @param string $tableName
     * @param string $schemaName
     * @return boolean
     */
    public function dropTable($tableName, $schemaName = null)
    {
        if (!$this->isTableExists($tableName, $schemaName)) {
            return true;
        }
        /* Drop foreign key and cascade packages */
        $foreignKeys = $this->getForeignKeys($tableName, $schemaName);

        foreach ($foreignKeys as $foreignKey) {
            $this->dropForeignKey($tableName, $foreignKey['FK_NAME'], $schemaName);
        }

        /* Drop table sequence if exists */
        $sequence = $this->_getSequenceName($tableName);
        if ($this->isSequenceExists($sequence)) {
            $this->query('DROP SEQUENCE ' . $this->quoteIdentifier($sequence));
        }

        $query = 'DROP TABLE ' . $this->quoteIdentifier($this->_getTableName($tableName, $schemaName));
        $this->query($query);

        return true;
    }

    /**
     * Creates a connection resource.
     *
     * @throws Zend_Db_Adapter_Oracle_Exception
     */
    protected function _connect()
    {
        if (is_resource($this->_connection)) {
            return;
        }

        $this->_lobAsString = true;

        $this->_debugTimer();
        parent::_connect();

        $this->query("ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
        $this->query("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
        $this->query("ALTER SESSION SET NLS_COMP = LINGUISTIC");
        $this->query("ALTER SESSION SET NLS_SORT = BINARY_CI");
        $this->query("ALTER SESSION SET NLS_NUMERIC_CHARACTERS = '.,'");
        //$this->query("ALTER SESSION SET PLSQL_CODE_TYPE = NATIVE");

        $this->_debugStat(self::DEBUG_CONNECT, '');
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
     * Quote an identifier.
     *
     * @param  string $value The identifier or expression.
     * @param boolean $auto  If true, heed the AUTO_QUOTE_IDENTIFIERS config option.
     * @return string        The quoted identifier and alias.
     */
    protected function _quoteIdentifier($value, $auto = false)
    {
        if ($auto === false || $this->_autoQuoteIdentifiers === true) {
            $q          = $this->getQuoteIdentifierSymbol();
            $upperValue = strtoupper($value);
            if (in_array($upperValue, $this->_reservedWords)) {
                $q     = '"';
                $value = $upperValue;
            }
            return ($q . str_replace("$q", "$q$q", $value) . $q);
        }
        return $value;
    }

    /**
     * Re an adapter-specific LIMIT clause to the SELECT statement.
     *
     * @param string $sql
     * @param integer $count
     * @param integer $offset OPTIONAL
     * @return string
     * @throws Zend_Db_Adapter_Oracle_Exception
     */
    public function limit($sql, $count, $offset = 0)
    {
        $query = '';
        $count = intval($count);
        if ($count <= 0) {
            throw new Zend_Db_Adapter_Oracle_Exception("LIMIT argument count={$count} is not valid");
        }

        $offset = intval($offset);
        if ($offset < 0) {
            throw new Zend_Db_Adapter_Oracle_Exception("LIMIT argument offset={$offset} is not valid");
        }
        $offsetCountSum = $offset + $count;
        if ($offsetCountSum == $offset + 1) {
            $query = sprintf('SELECT m1.* FROM (%s) m1 WHERE ROWNUM <= %d',
                $sql, $offsetCountSum);
        } else {
            $query = sprintf('
                SELECT m2.* FROM (
                    SELECT m1.*, ROWNUM AS analytic_clmn
                    FROM (%s) m1
                    WHERE ROWNUM <= %d) m2
                WHERE m2.analytic_clmn >= %d', $sql, $offsetCountSum, $offset + 1);
        }
            return $query;
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
     * Returns the column descriptions for a table.
     *
     * @param string $tableName
     * @param string $schemaName OPTIONAL
     * @return array
     */
    public function _describeTable($tableName, $schemaName = null)
    {
        if (!$schemaName) {
            $schemaName = $this->_getSchemaName();
        }
        $version = $this->getServerVersion();
        if (($version === null) || version_compare($version, '9.0.0', '>=')) {
            $sql = "SELECT TC.TABLE_NAME, TC.OWNER, TC.COLUMN_NAME,
                    CASE
                        WHEN tc.data_type = 'NUMBER' AND tc.data_scale = 0 AND tc.data_precision IS NULL
                            THEN 'INTEGER'
                        ELSE tc.data_type
                    END AS DATA_TYPE,
                    TC.DATA_DEFAULT, TC.NULLABLE, TC.COLUMN_ID, TC.DATA_LENGTH,
                    TC.DATA_SCALE, TC.DATA_PRECISION, C.CONSTRAINT_TYPE, CC.POSITION
                FROM ALL_TAB_COLUMNS TC
                LEFT JOIN (ALL_CONS_COLUMNS CC JOIN ALL_CONSTRAINTS C
                    ON (CC.CONSTRAINT_NAME = C.CONSTRAINT_NAME AND CC.TABLE_NAME = C.TABLE_NAME AND CC.OWNER = C.OWNER AND C.CONSTRAINT_TYPE = 'P'))
                  ON TC.TABLE_NAME = CC.TABLE_NAME AND TC.COLUMN_NAME = CC.COLUMN_NAME AND CC.OWNER = TC.OWNER
                WHERE UPPER(TC.TABLE_NAME) = UPPER(:TBNAME)";
            $bind[':TBNAME'] = $tableName;
            if ($schemaName) {
                $sql .= ' AND UPPER(TC.OWNER) = UPPER(:SCNAME)';
                $bind[':SCNAME'] = $schemaName;
            }
            $sql .= ' ORDER BY TC.COLUMN_ID';
        } else {
            $subSql = "SELECT AC.OWNER, AC.TABLE_NAME, ACC.COLUMN_NAME, AC.CONSTRAINT_TYPE, ACC.POSITION
                from ALL_CONSTRAINTS AC, ALL_CONS_COLUMNS ACC
                  WHERE ACC.CONSTRAINT_NAME = AC.CONSTRAINT_NAME
                    AND ACC.TABLE_NAME = AC.TABLE_NAME
                    AND ACC.OWNER = AC.OWNER
                    AND AC.CONSTRAINT_TYPE = 'P'
                    AND UPPER(AC.TABLE_NAME) = UPPER(:TBNAME)";
            $bind[':TBNAME'] = $tableName;
            if ($schemaName) {
                $subSql .= ' AND UPPER(ACC.OWNER) = UPPER(:SCNAME)';
                $bind[':SCNAME'] = $schemaName;
            }
            $sql = "SELECT TC.TABLE_NAME, TC.OWNER, TC.COLUMN_NAME, TC.DATA_TYPE,
                    TC.DATA_DEFAULT, TC.NULLABLE, TC.COLUMN_ID, TC.DATA_LENGTH,
                    TC.DATA_SCALE, TC.DATA_PRECISION, CC.CONSTRAINT_TYPE, CC.POSITION
                FROM ALL_TAB_COLUMNS TC, ($subSql) CC
                WHERE UPPER(TC.TABLE_NAME) = UPPER(:TBNAME)
                  AND TC.OWNER = CC.OWNER(+) AND TC.TABLE_NAME = CC.TABLE_NAME(+) AND TC.COLUMN_NAME = CC.COLUMN_NAME(+)";
            if ($schemaName) {
                $sql .= ' AND UPPER(TC.OWNER) = UPPER(:SCNAME)';
            }
            $sql .= ' ORDER BY TC.COLUMN_ID';
        }

        $stmt = $this->query($sql, $bind);

        /**
         * Use FETCH_NUM so we are not dependent on the CASE attribute of the PDO connection
         */
        $result = $stmt->fetchAll(Zend_Db::FETCH_NUM);

        $table_name      = 0;
        $owner           = 1;
        $column_name     = 2;
        $data_type       = 3;
        $data_default    = 4;
        $nullable        = 5;
        $column_id       = 6;
        $data_length     = 7;
        $data_scale      = 8;
        $data_precision  = 9;
        $constraint_type = 10;
        $position        = 11;

        $desc = array();
        foreach ($result as $key => $row) {
            list ($primary, $primaryPosition, $identity) = array(false, null, false);
            if ($row[$constraint_type] == 'P') {
                $primary = true;
                $primaryPosition = $row[$position];
                /**
                 * Oracle does not support auto-increment keys.
                 */
                $identity = false;
            }
            $desc[$this->foldCase($row[$column_name])] = array(
                'SCHEMA_NAME'      => $this->foldCase($row[$owner]),
                'TABLE_NAME'       => $this->foldCase($row[$table_name]),
                'COLUMN_NAME'      => $this->foldCase($row[$column_name]),
                'COLUMN_POSITION'  => $row[$column_id],
                'DATA_TYPE'        => $row[$data_type],
                'DEFAULT'          => trim($row[$data_default], "' "),
                'NULLABLE'         => (bool) ($row[$nullable] == 'Y'),
                'LENGTH'           => $row[$data_length],
                'SCALE'            => $row[$data_scale],
                'PRECISION'        => $row[$data_precision],
                'UNSIGNED'         => null, // @todo
                'PRIMARY'          => $primary,
                'PRIMARY_POSITION' => $primaryPosition,
                'IDENTITY'         => $identity
            );
        }
        /*
         * Set Identity to fields with autoincrement
         */
        $bind = array(
            ':TBNAME'   => $tableName,
            ':TRIGNAME' => $this->_getTriggerName($tableName, strtolower($result[0][$column_name])),
            ':SCNAME'   => $schemaName,
        );
        $sql = 'SELECT COUNT(1)
            FROM ALL_TRIGGERS
            WHERE UPPER(TABLE_NAME) = UPPER(:TBNAME)
            AND TRIGGER_NAME = :TRIGNAME
            AND OWNER = :SCNAME';

        if ($this->fetchOne($sql, $bind) != false) {
            $desc[$this->foldCase($result[0][$column_name])]['IDENTITY'] = true;
        }

        return $desc;
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
        $ddl      = $this->loadDdlCache($cacheKey, self::DDL_DESCRIBE);
        if ($ddl === false) {
            if ($schemaName === null) {
                $schemaName = $this->_getSchemaName();
            }
            $ddl = $this->_describeTable($tableName, $schemaName);
            // convert field name to lower case
            if ($this->_caseFolding == Zend_Db::CASE_NATURAL) {
                $ddl = array_change_key_case($ddl, CASE_LOWER);
            }
            foreach ($ddl as &$v) {
                $v['COLUMN_NAME'] = strtolower($v['COLUMN_NAME']);
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
            if ($columnData['DEFAULT'] !== null
                && $type != Varien_Db_Ddl_Table::TYPE_TEXT
                ) {
                $options['default'] = trim($columnData['DEFAULT'], "' ");
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
            if ($indexData['INDEX_TYPE'] == Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY) {
                continue;
            }

            $fields = $indexData['COLUMNS_LIST'];
            $options = array();
            $indexType = '';
            if ($indexData['INDEX_TYPE'] == Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE) {
                $options = array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE);
                $indexType = Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE;
            }
            $table->addIndex($this->getIndexName($newTableName, $fields, $indexType), $fields, $options);
        }

        $foreignKeys = $this->getForeignKeys($tableName);
        foreach ($foreignKeys as $keyData) {
            $fkName = $this->getForeignKeyName(
                $newTableName, $keyData['COLUMN_NAME'], $keyData['REF_TABLE_NAME'], $keyData['REF_COLUMN_NAME']
            );
            $onDelete = $this->_getDdlAction($keyData['ON_DELETE']);
            $onUpdate = $this->_getDdlAction($keyData['ON_UPDATE']);

            $table->addForeignKey(
                $fkName, $keyData['COLUMN_NAME'], $keyData['REF_TABLE_NAME'],
                $keyData['REF_COLUMN_NAME'], $onDelete, $onUpdate
            );
        }
        return $table;
    }

    /**
     * Return DDL action
     *
     * @param string $action
     * @return string
     */
    protected function _getDdlAction($action)
    {
        switch ($action) {
            case Varien_Db_Adapter_Interface::FK_ACTION_CASCADE:
                return Varien_Db_Ddl_Table::ACTION_CASCADE;
            case Varien_Db_Adapter_Interface::FK_ACTION_SET_NULL:
                return Varien_Db_Ddl_Table::ACTION_SET_NULL;
            case Varien_Db_Adapter_Interface::FK_ACTION_RESTRICT:
                return Varien_Db_Ddl_Table::ACTION_RESTRICT;
            default:
                return Varien_Db_Ddl_Table::ACTION_NO_ACTION;
        }
    }


    /**
     * Modify the column definition by data from describe table
     *
     * @param string $tableName
     * @param string $columnName
     * @param array|string $definition
     * @param boolean $flushData
     * @param string $schemaName
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function modifyColumnByDdl($tableName, $columnName, $definition, $flushData = false, $schemaName = null)
    {
        $definition = array_change_key_case($definition, CASE_UPPER);
        $definition['COLUMN_TYPE'] = $this->_getColumnTypeByDdl($definition);
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
        $columnDataType = $column['DATA_TYPE'];
        if (strpos($column['DATA_TYPE'], 'TIMESTAMP') !== false) {
            $columnDataType = 'TIMESTAMP';
        }
        switch ($columnDataType) {
            case 'INTEGER':
                return Varien_Db_Ddl_Table::TYPE_INTEGER;
            case 'VARCHAR2':
                return Varien_Db_Ddl_Table::TYPE_TEXT;
            case 'CLOB':
                return Varien_Db_Ddl_Table::TYPE_BLOB;
            case 'TIMESTAMP':
                return Varien_Db_Ddl_Table::TYPE_TIMESTAMP;
            case 'NUMBER':
                if(array_key_exists('SCALE', $column) && $column['SCALE'] > 0) {
                    return Varien_Db_Ddl_Table::TYPE_NUMERIC;
                } else {
                    return Varien_Db_Ddl_Table::TYPE_BIGINT;
                }
            case 'DATE':
                return Varien_Db_Ddl_Table::TYPE_DATE;
            case 'FLOAT':
                return Varien_Db_Ddl_Table::TYPE_FLOAT;
        }
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
     * Check is a sequence exists
     *
     * @param string $sequenceName
     * @return boolean
     */
    public function isSequenceExists($sequenceName)
    {
        $select = $this->select()
            ->from('user_sequences')
            ->where('SEQUENCE_NAME = ?', $this->quoteIdentifier($sequenceName));

        return (bool)$this->fetchOne($select);
    }

    /**
     * Returns a list of the tables in the database.
     *
     * @return array
     */
    public function listTables($schemaName = null)
    {
        if ($schemaName === null) {
            $schemaName = $this->_getSchemaName();
        }
        $select = $this->select()
            ->from('ALL_TABLES', array('table_name'))
            ->where('owner = upper(?)', $schemaName);;

        return $this->fetchCol($select);
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
        if ($schemaName === null) {
            $schemaName = new Zend_Db_Expr("sys_context('USERENV','CURRENT_SCHEMA')");
        }

        $select = $this->select()
            ->from('all_tables')
            ->where('table_name = upper(?)', $tableName)
            ->where('owner = upper(?)', $schemaName);

        return $this->fetchRow($select);
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
     * Run RAW Query
     *
     * @param string $sql
     * @return Zend_Db_Statement_Interface
     * @throws Zend_Db_Statement_Oracle_Exception
     */
    public function raw_query($sql)
    {
        $lostConnectionMessage = 'ORA-12170: TNS:Connect timeout occurred';
        $tries = 0;
        do {
            $retry = false;
            try {
                $result = $this->query($sql);
            } catch (Exception $e) {
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
     * Rename table
     *
     * @param string $oldTableName
     * @param string $newTableName
     * @param string $schemaName
     * @return boolean
     * @throws Zend_Db_Exception
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

        $query = sprintf('RENAME %s TO %s', $oldTable, $newTable);
        $this->query($query);

        return true;
    }

    /**
     * Adds new column to the table.
     *
     * Generally $defintion must be array with column data to keep this call cross-DB compatible.
     * Using string as $definition is allowed only for concrete DB adapter.
     *
     * @param string $tableName
     * @param string $columnName
     * @param array|string $definition  string specific or universal array DB Server definition
     * @param string $schemaName
     * @return Varien_Db_Adapter_Oracle
     * @throws Zend_Db_Exception
     */
    public function addColumn($tableName, $columnName, $definition, $schemaName = null)
    {
        if ($this->tableColumnExists($tableName, $columnName, $schemaName)) {
            return true;
        }

        $comment      = null;
        $isPrimaryKey = false;
        $isIdentity   = false;
        $needTimestampUpdate = false;

        if (is_array($definition)) {
            // Retrieve comment to set it later
            $definition = array_change_key_case($definition, CASE_UPPER);
            if (empty($definition['COMMENT'])) {
                throw new Zend_Db_Exception("Impossible to create a column without comment.");
            }
            $comment = $definition['COMMENT'];

            if (!empty($definition['PRIMARY'])) {
                $isPrimaryKey = true;
            }
            if (!empty($definition['IDENTITY'])) {
                $isIdentity   = true;
            }
            if (!empty($definition['DEFAULT']) && $definition['TYPE'] == Varien_Db_Ddl_Table::TYPE_TIMESTAMP) {
                if ($definition['DEFAULT']== Varien_Db_Ddl_Table::TIMESTAMP_UPDATE
                    || $definition['DEFAULT'] == Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE
                ) {
                    $needTimestampUpdate = true;
                }
            }

            $definition = $this->_getColumnDefinition($definition);
        }

        $realTableName = $this->_getTableName($tableName, $schemaName);
        $query = sprintf('ALTER TABLE %s ADD %s %s %s',
            $this->quoteIdentifier($realTableName),
            $this->quoteIdentifier($columnName),
            $definition,
            ($isPrimaryKey) ? ' PRIMARY KEY' : ''
        );

        $result = $this->query($query);

        /* Add identity trigger */
        if ($isIdentity) {
            $this->_createIdentityTrigger($realTableName, $columnName);
        }

        /* Add time update trigger */
        if ($needTimestampUpdate) {
            $this->_createTimeUpdateTrigger($realTableName, $columnName);
        }

        if (!empty($comment)) {
            $this->_addColumnComment($realTableName, $columnName, $comment);
        }
        $this->resetDdlCache($tableName, $schemaName);

        return $result;
    }

    /**
     * Check is table column exist
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $schemaName
     * @return boolean
     */
    public function tableColumnExists($tableName, $columnName, $schemaName = null)
    {
        $describe = $this->describeTable($tableName, $schemaName);

        $columnName = strtolower($columnName);
        foreach ($describe as $column) {
            if (strtolower($column['COLUMN_NAME']) == $columnName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Change the column name
     *
     * @param string $tableName
     * @param string $oldColumnName
     * @param string $newColumnName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Oracle
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
            throw new Exception(sprintf('Column "%s" already exists on table "%s"', $newColumnName, $tableName));
        }

        $query = sprintf('ALTER TABLE %s RENAME COLUMN %s TO %s',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
            $this->quoteIdentifier($oldColumnName),
            $this->quoteIdentifier($newColumnName)
        );
        $this->query($query);
        $this->resetDdlCache($tableName, $schemaName);

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
     * @return Varien_Db_Adapter_Oracle
     * @throws Zend_Db_Adapter_Oracle_Exception
     */
    public function modifyColumn($tableName, $columnName, $definition, $flushData = false, $schemaName = null)
    {
        if (!$this->tableColumnExists($tableName, $columnName, $schemaName)) {
            $msg = sprintf('Column "%s" does not exists on table "%s"', $columnName, $tableName);
            throw new Zend_Db_Adapter_Oracle_Exception($msg);
        }

        if (is_array($definition)) {
            $definition['table_name']  = $tableName;
            $definition['column_name'] = $columnName;
            $definition = $this->_getColumnDefinition($definition);
        }

        $query = sprintf('ALTER TABLE %s MODIFY %s %s',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
            $this->quoteIdentifier($columnName),
            $definition);

        $this->raw_query($query);
        $this->resetDdlCache($tableName, $schemaName);

        return $this;
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
     * @return Varien_Db_Adapter_Oracle
     */
    public function changeColumn($tableName, $oldColumnName, $newColumnName, $definition, $flushData = false,
        $schemaName = null)
    {
        $this->_renameColumn($tableName, $oldColumnName, $newColumnName, $schemaName);
        $this->modifyColumn($tableName, $newColumnName, $definition, $flushData, $schemaName);
        if (!empty($definition['COMMENT'])) {
            $this->_addColumnComment($tableName, $newColumnName, $definition['COMMENT']);
        }

        return $this;
    }

    /**
     * Drop the column from table
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Oracle
     */
    public function dropColumn($tableName, $columnName, $schemaName = null)
    {
        if (!$this->tableColumnExists($tableName, $columnName, $schemaName)) {
            return true;
        }

        $columnName = strtolower($columnName);
        foreach ($this->getForeignKeys($tableName, $schemaName) as $fkData) {
            if (strtolower($fkData['COLUMN_NAME']) == $columnName) {
                $this->dropForeignKey($tableName, $fkData['FK_NAME'], $schemaName);
            }
        }

        /* Drop column trigger if exists */
        $availableTriggers = array(
            $this->_getTriggerName($tableName, $columnName, self::TRIGGER_IDENTITY),
            $this->_getTriggerName($tableName, $columnName, self::TRIGGER_TIME_UPDATE)
        );

        $select = $this->select()
            ->from('user_triggers')
            ->where('TRIGGER_NAME IN(?)', $availableTriggers);

        $triggerName = $this->fetchOne($select);
        if ($triggerName) {
            $this->query('DROP TRIGGER ' . $this->quoteIdentifier($triggerName));
        }

        $query = sprintf('ALTER TABLE %s DROP COLUMN %s',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
            $this->quoteIdentifier($columnName));

        $this->query($query);

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
     * @return Varien_Db_Adapter_Oracle
     * @throws Zend_Db_Adapter_Oracle_Exception
     */
    public function addIndex($tableName, $indexName, $fields,
        $indexType = Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX, $schemaName = null)
    {
        if ($schemaName === null) {
            $schemaName = $this->_getSchemaName();
        }
        $this->resetDdlCache($tableName, $schemaName);
        $keyList = $this->getIndexList($tableName, $schemaName);

        // Drop index if exists
        foreach($keyList as $key) {
            if ($key['KEY_NAME'] == strtoupper($indexName)) {
                $this->dropIndex($tableName, $indexName, $schemaName);
            }
        }

        if (!is_array($fields)) {
            $fields = array($fields);
        }
        $fieldSql = array();
        foreach ($fields as $field) {
            if (!$this->tableColumnExists($tableName, $field, $schemaName)) {
                throw new Zend_Db_Adapter_Oracle_Exception(
                    sprintf('Column "%s" does not exists on table "%s"', $field, $tableName));
            }
            $fieldSql[] = $this->quoteIdentifier($field);
        }

        $fieldSql = implode(', ', $fieldSql);

        switch (strtolower($indexType)) {
            case Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY:
                $condition = 'ALTER TABLE %1$s ADD CONSTRAINT "%2$s" PRIMARY KEY (%3$s)';
                break;
            case Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE:
                $condition = 'ALTER TABLE %1$s ADD CONSTRAINT "%2$s" UNIQUE (%3$s)';
                break;
            case Varien_Db_Adapter_Interface::INDEX_TYPE_FULLTEXT:
                $condition = 'CREATE INDEX "%2$s" ON %1$s (%3$s) INDEXTYPE IS CTXSYS.CONTEXT';
                break;
            default:
                $condition = 'CREATE INDEX "%2$s" ON %1$s (%3$s)';
                break;
        }

        $query = sprintf($condition,
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
            $this->quoteIdentifier($indexName),
            $fieldSql);

        $this->raw_query($query);
        $this->resetDdlCache($tableName, $schemaName);

        return $this;
    }

    /**
     * Drop the index from table
     *
     * @param string $tableName
     * @param string $keyName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Oracle
     */
    public function dropIndex($tableName, $keyName, $schemaName = null)
    {

        $indexList  = $this->getIndexList($tableName, $schemaName);
        if ($schemaName === null) {
            $schemaName = $this->_getSchemaName();
        }

        $keyType = null;
        foreach($indexList as $index) {
            if ($index['KEY_NAME'] == $keyName) {
                $keyType = $index['INDEX_TYPE'];
                break;
            }
        }

        if ($keyType === null) {
            return $this;
        }

        switch ($keyType) {
            case Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY:
            case Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE:
                $condition = 'ALTER TABLE %1$s DROP CONSTRAINT "%2$s" CASCADE';
                break;
            case Varien_Db_Adapter_Interface::INDEX_TYPE_FULLTEXT:
            default:
                $condition = 'DROP INDEX "%2$s"';
                break;
        }

        $table   = $this->quoteIdentifier($this->_getTableName($tableName, $schemaName));
        $sql     = sprintf($condition, $table, $keyName);

        $this->raw_query($sql);
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
        if ($schemaName === null) {
            $schemaName = $this->_getSchemaName();
        }

        $ddl = $this->loadDdlCache($cacheKey, self::DDL_INDEX);
        if ($ddl === false) {
            $ddl = array();

            $casesResults  = array(
                "uc.constraint_type = 'P'"                                        => "'" . Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY . "'",
                "nvl(uc.constraint_type,'N') = 'U'/* AND ui.uniqueness = 'UNIQUE'*/ " => "'" . Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE . "'",
                "ui.ityp_owner = 'CTXSYS' AND ui.ityp_name = 'CONTEXT'"           => "'" . Varien_Db_Adapter_Interface::INDEX_TYPE_FULLTEXT . "'",
            );
            $defaultValue  = "'" . Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX . "'";
            $indexTypeExpr = $this->getCaseSql('', $casesResults, $defaultValue);

            $select = $this->select()
                ->from(array('ui' => 'all_indexes'), '')
                ->joinLeft(
                    array('uc' => 'all_constraints'),
                    'ui.index_name = uc.constraint_name AND ui.table_owner = uc.owner',
                    array())
                ->join(
                    array('uic' => 'all_ind_columns'),
                    'ui.index_name = uic.index_name AND ui.table_owner = uic.table_owner',
                    array())
                ->columns(array(
                    'schema_name'   => 'ui.owner',
                    'table_name'    => 'ui.table_name',
                    'key_name'      => 'ui.index_name',
                    'column_name'   => 'uic.column_name',
                    'index_type'    => $indexTypeExpr,
                    'index_method'  => 'ui.index_type',
                ))
                ->where('ui.table_name = upper(?)', $tableName)
                ->where('ui.owner = upper(?)', $schemaName);

            $isIndexExists = $this->select()
                ->from(
                    array('ai' => 'all_indexes'),
                    array(new Zend_Db_Expr('1')))
                ->where('ai.owner = ac.owner')
                ->where('ai.index_name = ac.constraint_name');
            $constraints = $this->select()
                ->from(
                    array('ac' => 'all_constraints'),
                    array())
                ->join(
                    array('acc' => 'all_cons_columns'),
                    'acc.owner = ac.owner AND acc.constraint_name = ac.constraint_name',
                    array())
                ->where('ac.table_name = upper(?)', $tableName)
                ->where("ac.constraint_type IN ('P','U')")
                ->where('ac.owner = upper(?)', $schemaName)
                ->where(sprintf('NOT EXISTS (%s)', $isIndexExists->assemble()))
                ->columns(array(
                    'schema_name'   => 'ac.owner',
                    'table_name'    => 'ac.table_name',
                    'key_name'      => 'ac.constraint_name',
                    'column_name'   => 'acc.column_name',
                    'index_type'    => new Zend_Db_Expr(
                        sprintf("CASE WHEN constraint_type = 'P' THEN '%s' ELSE '%s' END",
                            Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY,
                            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)),

                    'index_method'  => 'ui.index_type',
                    'index_method'  => new Zend_Db_Expr('NULL')
                ));
            $keys = $this->select()->union(array($select, $constraints));
            $rowset = $this->fetchAll($keys);
            foreach ($rowset as $row) {
                $upperKeyName = strtoupper($row['key_name']);
                $columnName   = strtolower($row['column_name']);

                $indexType = $row['index_type'];
                switch (strtolower($indexType)) {
                    case Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY:
                          $upperKeyName = strtoupper(Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY);
                          break;
                    default:
                        $upperKeyName = strtoupper($row['key_name']);
                        break;
                }
                if (isset($ddl[$upperKeyName])) {
                    $ddl[$upperKeyName]['fields'][] = $columnName; // for compatibility
                    $ddl[$upperKeyName]['COLUMNS_LIST'][] = $columnName;
                } else {
                    $ddl[$upperKeyName] = array(
                        'SCHEMA_NAME'   => $row['schema_name'],
                        'TABLE_NAME'    => $row['table_name'],
                        'KEY_NAME'      => $row['key_name'],
                        'COLUMNS_LIST'  => array($columnName),
                        'INDEX_TYPE'    => strtolower($row['index_type']),
                        'INDEX_METHOD'  => $row['index_method'],
                        'type'          => strtolower($row['index_type']), // for compatibility
                        'fields'        => array($columnName) // for compatibility
                    );
                }
            }
            $this->saveDdlCache($cacheKey, self::DDL_INDEX, $ddl);
        }

        return $ddl;
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
     * @return Varien_Db_Adapter_Oracle
     */
    public function addForeignKey($fkName, $tableName, $columnName, $refTableName, $refColumnName,
        $onDelete = self::FK_ACTION_CASCADE, $onUpdate = self::FK_ACTION_CASCADE,
        $purge = false, $schemaName = null, $refSchemaName = null)
    {
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

        if ($onDelete !== null) {
            $query .= ' ON DELETE ' . strtoupper($onDelete);
        }
        /**
         * @todo ON UPDATE
         */
//        if (!is_null($onUpdate)) {
//            $query .= ' ON UPDATE ' . strtoupper($onUpdate);
//        }

        $this->raw_query($query);
        $this->resetDdlCache($tableName, $schemaName);

        return $this;
    }

    /**
     * Prepare table before add constraint foreign key
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $refTableName
     * @param string $refColumnName
     * @param string $onDelete
     * @return Varien_Db_Adapter_Pdo_Oracle
     */
    public function purgeOrphanRecords($tableName, $columnName, $refTableName, $refColumnName, $onDelete = 'cascade')
    {
        // quote table and column
        $tableName      = $this->quoteIdentifier($tableName);
        $refTableName   = $this->quoteIdentifier($refTableName);
        $columnName     = $this->quoteIdentifier($columnName);
        $refColumnName  = $this->quoteIdentifier($refColumnName);

        if (strtoupper($onDelete) == Varien_Db_Ddl_Table::ACTION_CASCADE || strtoupper($onDelete) == Varien_Db_Ddl_Table::ACTION_RESTRICT) {
            $sql = " UPDATE {$tableName} t1 SET t1.code = NULL ";
        } else if (strtoupper($onDelete) == Varien_Db_Ddl_Table::ACTION_SET_NULL) {
            $sql = " DELETE FROM {$tableName} t1";
        }

        $sql .= sprintf(" WHERE NOT EXISTS( SELECT 1 FROM %s t2 WHERE t2.%s = t1.%s)",
            $refTableName, $refColumnName, $columnName);

        $this->raw_query($sql);

        return $this;
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
     * Drop the Foreign Key from table
     *
     * @param string $tableName
     * @param string $fkName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Oracle
     */
    public function dropForeignKey($tableName, $fkName, $schemaName = null)
    {
        $foreignKeys = $this->getForeignKeys($tableName, $schemaName);
        $fkName      = strtoupper($fkName);
        if (!isset($foreignKeys[$fkName])) {
            return $this;
        }
        $fkActions = array(Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_SET_NULL);
        // drop cascade packages and triggers
        foreach ($foreignKeys as $foreignKey) {
            if ($fkName == $foreignKey['FK_NAME']) {
                $columnName = $foreignKey['COLUMN_NAME'];

                if (in_array($foreignKey['ON_UPDATE'], $fkActions)) {
                    $this->raw_fetchRow(sprintf(" DROP PACKAGE %s",
                        $this->quoteIdentifier($this->_getPackageName($tableName, $columnName))));
                    $this->raw_fetchRow(sprintf(" DROP TRIGGER %s",
                        $this->quoteIdentifier($this->_getTriggerName($tableName, $columnName, self::TRIGGER_BEFORE_UPDATE))));
                    $this->raw_fetchRow(sprintf(" DROP TRIGGER %s", $this->quoteIdentifier(
                        $this->_getTriggerName($tableName, $columnName, self::TRIGGER_BEFORE_UPDATE_ER))));
                    $this->raw_fetchRow(sprintf(" DROP TRIGGER %s", $this->quoteIdentifier(
                        $this->_getTriggerName($tableName, $columnName, self::TRIGGER_AFTER_UPDATE))));
                }
            }
        }

        // Do not modify $schemaName, or we'll get wrong cache key
        $reqSchemaName = ($schemaName !== null) ? $schemaName : $this->_getSchemaName();
        $sql = sprintf('ALTER TABLE %s DROP CONSTRAINT %s',
            $this->quoteIdentifier($this->_getTableName($tableName, $reqSchemaName)),
            $this->quoteIdentifier($foreignKeys[$fkName]['FK_NAME']));

        $this->raw_query($sql);
        $this->resetDdlCache($tableName, $schemaName);

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
            if ($schemaName === null) {
                $schemaName = $this->_getSchemaName();
            }

            $select = $this->select()
                ->from(array('c' => 'all_constraints'), '')
                ->join(
                    array('cp' => 'all_cons_columns'),
                    'c.constraint_name = cp.constraint_name AND c.owner = cp.owner',
                    array())
                ->join(
                    array('cr' => 'all_cons_columns'),
                    'c.r_constraint_name = cr.constraint_name AND c.owner = cr.owner',
                    array())
                ->columns(array(
                    'fk_name'           => 'c.constraint_name',
                    'schema_name'       => 'cp.owner',
                    'table_name'        => 'cp.table_name',
                    'column_name'       => 'cp.column_name',
                    'ref_schema_name'   => 'cr.owner',
                    'ref_table_name'    => 'cr.table_name',
                    'ref_column_name'   => 'cr.column_name',
                    'on_delete'         => 'c.delete_rule'
                ))
                ->where('c.constraint_type=?', 'R')
                ->where('c.table_name  = upper(?)', $tableName)
                ->where('c.owner = upper(?)', $schemaName);

            $rowset = $this->fetchAll($select);
            foreach ($rowset as $row) {
                $upperKeyName = strtoupper($row['fk_name']);
                $ddl[$upperKeyName] = array(
                    'FK_NAME'           => $row['fk_name'],
                    'SCHEMA_NAME'       => $row['schema_name'],
                    'TABLE_NAME'        => $row['table_name'],
                    'COLUMN_NAME'       => $row['column_name'],
                    'REF_SHEMA_NAME'    => $row['ref_schema_name'],
                    'REF_TABLE_NAME'    => $row['ref_table_name'],
                    'REF_COLUMN_NAME'   => $row['ref_column_name'],
                    'ON_DELETE'         => $row['on_delete'],
                    'ON_UPDATE'         => ''
                );
            }

            $this->saveDdlCache($cacheKey, self::DDL_FOREIGN_KEY, $ddl);
        }

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
     * Inserts a table row with specified data
     * Special for Zero values to identity column
     *
     * @param string $table
     * @param array $bind
     * @return int The number of affected rows.
     */
    public function insertForce($table, array $bind)
    {
        return $this->insert($table, $bind);
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
        $indexList = $this->getIndexList($tableName, $schemaName);
        $columns   = array();
        foreach($indexList as $indexData) {
            if ($indexData['INDEX_TYPE'] == self::INDEX_TYPE_PRIMARY) {
                foreach($indexData['COLUMNS_LIST'] as $column) {
                    $columns[$column] = $column;
                }
            }
        }
        return $columns;
    }

    /**
     * Inserts a table row with specified data.
     *
     * Oracle does not support anonymous ('?') binds.
     *
     * @param mixed $table The table to insert data into.
     * @param array $bind Column-value pairs.
     * @return int The number of affected rows.
     */
    public function insert($table, array $bind)
    {
        $i = 0;
        // extract and quote col names from the array keys
        $cols = array();
        $vals = array();
        foreach ($bind as $col => $val) {
            $cols[] = $col;
            if ($val instanceof Zend_Db_Expr) {
                $vals[] = $val->__toString();
                unset($bind[$col]);
            } else {
                $key = sprintf(':%s%d', $col, $i);
                if (strlen($key) > 30) {
                    $key = ':vdbao' . $i;
                }
                $vals[] = $key;
                unset($bind[$col]);
                $bind[$key] = $val;
            }
            $i++;
        }

        $insertSql = $this->_getInsertSqlQuery($table, $cols, $vals);
        // execute the statement and return the number of affected rows
        $stmt   = $this->query($insertSql, $bind);

        return $stmt->rowCount();
    }

    /**
     * Return insert sql query
     *
     * @param string $tableName
     * @param array $columns
     * @param array $values
     * @return string
     */
    protected function _getInsertSqlQuery($tableName, array $columns, array $values)
    {
        $tableName = $this->quoteIdentifier($tableName, true);
        $columns   = array_map(array($this, 'quoteIdentifier'), $columns);
        $columns   = implode(', ', $columns);
        $values    = implode(', ', $values);

        $insertSql = sprintf('INSERT INTO %s (%s) VALUES (%s)', $tableName, $columns, $values);

        return $insertSql;
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
        $row    = reset($data);     // get first elemnt from data array
        $bind   = array();          // SQL bind array
        $cols   = array();
        $values = array();

        $ddl = $this->describeTable($table);

        if (is_array($row)) { // Array of column-value pairs
            $cols = array_keys($row);
            $i    = 0;
            foreach ($data as $row) {
                $line = array();
                if (array_diff($cols, array_keys($row))) {
                    throw new Varien_Exception('Invalid data for insert');
                }
                foreach ($row as $col => $val) {
                    if ($val instanceof Zend_Db_Expr) {
                        $line[] = $this->quoteColumnAs($val, $col);
                    } else {
                        $key    = ':vv' . $i++;
                        if ($ddl[$col]['DATA_TYPE'] == $this->_ddlColumnTypes[Varien_Db_Ddl_Table::TYPE_BLOB]) {
                            $line[] = "to_clob({$key}) AS {$col}";
                        } else {
                            $line[] = $this->quoteColumnAs($key, $col);
                        }
                        $bind[$key] = $val;
                    }
                }
                $values[] = sprintf('SELECT %s FROM dual', implode(', ', $line));
            }
            unset($row);
        } else { // Column-value pairs
            $cols = array_keys($data);
            $line = array();
            $i    = 0;
            foreach ($data as $col => $val) {
                if ($val instanceof Zend_Db_Expr) {
                    $line[] = $this->quoteColumnAs($val, $col);
                } else {
                    $key    = ':vv' . $i++;
                    if ($ddl[$col]['DATA_TYPE'] == $this->_ddlColumnTypes[Varien_Db_Ddl_Table::TYPE_BLOB]) {
                        $line[] = "to_clob({$key}) AS {$col}";
                    } else {
                        $line[] = $this->quoteColumnAs($key, $col);
                    }
                    $bind[$key] = $val;
                }
            }
            $values[] = sprintf('SELECT %s FROM dual', implode(', ', $line));
        }

        // update fields
        if (empty($fields)) {
            $fields = $cols;
        }

        $joinConditions = array();

        // Obtain primary key fields
        $pkColumns = $this->_getPrimaryKeyColumns($table);
        $groupCond = array();
        $usePkCond = true;
        foreach ($pkColumns as $column) {
            if (!in_array($column, $cols)) {
                $usePkCond = false;
            }
            if (false !== ($k = array_search($column, $fields))) {
                unset($fields[$k]);
            }
            $groupCond[] = sprintf('t1.%s = t2.%s', $column, $column);
        }
        if (!empty($groupCond) && $usePkCond) {
            $joinConditions[] = sprintf('(%s)', join(') AND (', $groupCond));
        }

        foreach ($this->getIndexList($table) as $indexData) {
            if ($indexData['INDEX_TYPE'] != self::INDEX_TYPE_UNIQUE) {
                continue;
            }
            // Obtain unique indexes fields
            $groupCond  = array();
            $useUnqCond = true;
            foreach($indexData['COLUMNS_LIST'] as $column) {
                if (!in_array($column, $cols)) {
                    $useUnqCond = false;
                }
                if (false !== ($k = array_search($column, $fields))) {
                    unset($fields[$k]);
                }
                $groupCond[] = sprintf('t1.%s = t2.%s', $column, $column);
            }
            if (!empty($groupCond) && $useUnqCond) {
                $joinConditions[] = sprintf('(%s)', implode(' AND ', $groupCond));
            }
        }

        if (empty($joinConditions)) {
            throw new Exception('Invalid primary or unique columns in merge data');
        }

        $updateArray = array();
        foreach ($fields as $field) {
            $updateArray[] = sprintf('t1.%s = t2.%s', $this->quoteIdentifier($field), $this->quoteIdentifier($field));
        }

        $insertCols = array_map(array($this, 'quoteIdentifier'), $cols);
        $insertVals = array();
        foreach ($insertCols as $col) {
            $insertVals[] = sprintf('t2.%s', $col);
        }

        $query = sprintf('MERGE INTO %s t1 USING (%s) t2 ON ( %s )',
            $table,
            implode(' UNION ALL ', $values),
            implode(' OR ', $joinConditions));

        if ($updateArray) {
            $query = sprintf('%s WHEN MATCHED THEN UPDATE SET %s',
                $query,
                implode(', ', $updateArray));
        }

        $query = sprintf('%s WHEN NOT MATCHED THEN INSERT (%s) VALUES (%s)',
            $query,
            implode(', ', $insertCols),
            implode(', ', $insertVals)
        );

        $this->query($query, $bind);
    }




    /**
     * Inserts a table row with specified data. compatible with Oracle 8
     *
     * @param mixed $table The table to insert data into.
     * @param array $data Column-value pairs or array of column-value pairs.
     * @param arrat $fields update fields pairs or values
     * @return int The number of affected rows.
     * @throws Zend_Db_Exception
     */
    public function insertOnDuplicateCompatible($table, array $data, array $fields = array())
    {
        // extract and quote col names from the array keys
        $row    = reset($data);     // get first elemnt from data array
        $bind   = array();          // SQL bind array
        $cols   = array();
        $values = array();

        $ddl = $this->describeTable($table);

        if (is_array($row)) { // Array of column-value pairs
            $cols = array_keys($row);
            $i    = 0;
            foreach ($data as $row) {
                $line = array();
                if (array_diff($cols, array_keys($row))) {
                    throw new Zend_Db_Exception('Invalid data for insert');
                }
                foreach ($row as $col => $val) {
                    if ($val instanceof Zend_Db_Expr) {
                        $line[] = $this->quoteColumnAs($val, $col);
                    } else {
                        $key    = ':vv' . $i++;
                        if ($ddl[$col]['DATA_TYPE'] == $this->_ddlColumnTypes[Varien_Db_Ddl_Table::TYPE_BLOB]) {
                            $line[] = "to_clob({$key}) AS {$col}";
                        } else {
                            $line[] = $this->quoteColumnAs($key, $col);
                        }
                        $bind[$key] = $val;
                    }
                }
                $values[] = sprintf('SELECT %s FROM dual', implode(', ', $line));
            }
            unset($row);
        } else { // Column-value pairs
            $cols = array_keys($data);
            $line = array();
            $i    = 0;
            foreach ($data as $col => $val) {
                if ($val instanceof Zend_Db_Expr) {
                    $line[] = $this->quoteColumnAs($val, $col);
                } else {
                    $key    = ':vv' . $i++;
                    if ($ddl[$col]['DATA_TYPE'] == $this->_ddlColumnTypes[Varien_Db_Ddl_Table::TYPE_BLOB]) {
                        $line[] = "to_clob({$key}) AS {$col}";
                    } else {
                        $line[] = $this->quoteColumnAs($key, $col);
                    }
                    $bind[$key] = $val;
                }
            }
            $values[] = sprintf('SELECT %s FROM dual', implode(', ', $line));
        }

        // update fields
        if (empty($fields)) {
            $fields = $cols;
        }

        $joinConditions = array();

        // Obtain primary key fields
        $pkColumns = $this->_getPrimaryKeyColumns($table);
        $groupCond = array();
        $usePkCond = true;
        foreach ($pkColumns as $column) {
            if (!in_array($column, $cols)) {
                $usePkCond = false;
            }
            if (false !== ($k = array_search($column, $fields))) {
                unset($fields[$k]);
            }
            $groupCond[] = sprintf('t1.%s = t2.%s', $column, $column);
        }
        if (!empty($groupCond) && $usePkCond) {
            $joinConditions[] = sprintf('(%s)', join(') AND (', $groupCond));
        }

        foreach ($this->getIndexList($table) as $indexData) {
            if ($indexData['INDEX_TYPE'] != self::INDEX_TYPE_UNIQUE) {
                continue;
            }
            // Obtain unique indexes fields
            $groupCond  = array();
            $useUnqCond = true;
            foreach($indexData['COLUMNS_LIST'] as $column) {
                if (!in_array($column, $cols)) {
                    $useUnqCond = false;
                }
                if (false !== ($k = array_search($column, $fields))) {
                    unset($fields[$k]);
                }
                $groupCond[] = sprintf('t1.%s = t2.%s', $column, $column);
            }
            if (!empty($groupCond) && $useUnqCond) {
                $joinConditions[] = sprintf('(%s)', join(' AND ', $groupCond));
            }
        }

        if (empty($joinConditions)) {
            throw new Zend_Db_Exception('Invalid primary or unique columns in merge data');
        }

        $insertCols = array_map(array($this, 'quoteIdentifier'), $cols);
        $insertVals = array();
        foreach ($insertCols as $col) {
            $insertVals[] = sprintf('t2.%s', $col);
        }

        $query = sprintf('INSERT INTO %s (%s) SELECT * FROM (%s) t2 WHERE NOT EXISTS ( SELECT 1 FROM %s t1 WHERE %s )',
            $table,
            implode(', ', $insertCols),
            implode(' UNION ALL ', $values),
            $table,
            implode(' OR ', $joinConditions));

        if ($fields) {
            $query = sprintf("BEGIN\n %s;\n %s;\n END;",
                $query,
                sprintf('UPDATE %s t1 SET (%s)= (SELECT %s FROM (%s) t2 WHERE %s) WHERE EXISTS ( SELECT 1 FROM (%s) t3 WHERE %s )',
                    $table,
                    implode(', ', $fields),
                    implode(', ', $fields),
                    implode(' UNION ALL ', $values),
                    implode(' OR ', $joinConditions),
                    implode(' UNION ALL ', $values),
                    str_replace('t2','t3', implode(' OR ', $joinConditions))
                )
            );
        }
        return $this->query($query, $bind);
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
        $inc  = 0;
        $vals = array();
        $bind = array();
        $ddl = $this->describeTable($table);
        $columnsCount = count($columns);
        foreach ($data as $row) {
            if ($columnsCount != count($row)) {
                throw new Zend_Db_Exception('Invalid data for insert');
            }
            $line = array();
            if ($columnsCount == 1) {
                if ($row instanceof Zend_Db_Expr) {
                    $line = $row->__toString();
                } elseif ($row === null) {
                    $line = 'NULL';
                } else {
                    $key  = ':vv' . ($inc ++);
                    if ($ddl[$columns[0]]['DATA_TYPE'] == $this->_ddlColumnTypes[Varien_Db_Ddl_Table::TYPE_BLOB]) {
                        $line = sprintf('to_clob(%s)', $key);
                    } else {
                        $line = $key;
                    }
                    $bind[$key] = $row;
                }
                $vals[] = $line;
            } else {
                foreach ($row as $col=>$value) {
                    if ($value instanceof Zend_Db_Expr) {
                        $line[] = $value->__toString();
                    } else if (is_null($value)) {
                        $line[] = 'NULL';
                    } else {
                        $key  = ':vv' . ($inc ++);
                        if (is_int($col) && isset($columns[$col])) {
                            $ddlKey = $columns[$col];
                        } else {
                            $ddlKey = $col;
                        }
                        if (isset($ddl[$ddlKey]) && ($ddl[$ddlKey]['DATA_TYPE'] == $this->_ddlColumnTypes[Varien_Db_Ddl_Table::TYPE_BLOB])) {
                            $line[] = sprintf('to_clob(%s)', $key);
                        } else {
                            $line[] = $key;
                        }
                        $bind[$key] = $value;
                    }
                }
                $vals[] = implode(', ', $line);
            }
        }
        // build the statement
        $columns = array_map(array($this, 'quoteIdentifier'), $columns);
        $sql = sprintf("INSERT INTO %s (%s) SELECT %s FROM dual",
            $this->quoteIdentifier($table, true),
            implode(', ', $columns), implode(' FROM dual UNION ALL SELECT ', $vals));

        // execute the statement and return the number of affected rows
        $stmt = $this->query($sql, $bind);
        $result = $stmt->rowCount();
        return $result;
    }

    /**
     * Executes a SQL statement(s)
     *
     * @param string $sql
     * @return Varien_Db_Adapter_Pdo_Mssql
     * @throws Exception
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
        $parts = preg_split('#(;|\'|"|\\\\|//|--|\n|/\*|\*/)#', $sql, null, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        $q     = false;
        $c     = false;
        $stmts = array();
        $s     = '';

        foreach ($parts as $i => $part) {
            // strings
            if (($part === "'" || $part === '"') && ($i === 0 || $parts[$i-1] !== '\\')) {
                if ($q === false) {
                    $q = $part;
                } elseif ($q === $part) {
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
            } elseif ($part === '*/' && $c === '/*') {
                $c = false;
            }

            // statements
            if ($part === ';' && $q === false && $c === false) {
                if (trim($s) !== '') {
                    $stmts[] = trim($s);
                    $s = '';
                }
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
        return new Zend_Db_Expr($this->quoteInto("TO_DATE(?,'YYYY-MM-DD HH24:MI:SS')", $date));
    }

    /**
     * Run additional environment before setup
     *
     * @return Varien_Db_Adapter_Oracle
     */
    public function startSetup()
    {
        return $this;
    }

    /**
     * Run additional environment after setup
     *
     * @return Varien_Db_Adapter_Oracle
     */
    public function endSetup()
    {
        return $this;
    }

    /**
     * Set cache adapter
     *
     * @param Zend_Cache_Backend_Interface $adapter
     * @return Varien_Db_Adapter_Oracle
     */
    public function setCacheAdapter($adapter)
    {
        $this->_cacheAdapter = $adapter;
        return $this;
    }

    /**
     * Allow DDL caching
     *
     * @return Varien_Db_Adapter_Oracle
     */
    public function allowDdlCache()
    {
        $this->_isDdlCacheAllowed = true;
        return $this;
    }

    /**
     * Disallow DDL caching
     *
     * @return Varien_Db_Adapter_Oracle
     */
    public function disallowDdlCache()
    {
        $this->_isDdlCacheAllowed = false;
        return $this;
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
     * Reset cached DDL data from cache
     * if table name is null - reset all cached DDL data
     *
     * @param string $tableName
     * @param string $schemaName OPTIONAL
     * @return Varien_Db_Adapter_Oracle
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
     * @return Varien_Db_Adapter_Oracle
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
            'finset'        => "FIND_IN_SET(?, {{fieldName}}) = 1",
            'regexp'        => "REGEXP_LIKE({{fieldName}}, ?)",
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
            $key = key(array_intersect_key($condition, $conditionKeyMap));;

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
            $result = $this->formatDate($condition[$key], false);
        }

        return $result;
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

        switch ($column['DATA_TYPE']) {
            case 'SMALLINT':
            case 'INT':
            case 'INTEGER':
            case 'BIGINT':
                if (is_null($value) && $column['NULLABLE']) {
                    return null;
                }
                $value = (int)$value;
                break;

            case 'DECIMAL':
            case 'NUMBER':
                if (is_null($value) && $column['NULLABLE']) {
                    return null;
                }
                $precision  = 10;
                $scale      = 0;
                if (!empty($column['SCALE'])) {
                    $scale = $column['SCALE'];
                }
                if (!empty($column['PRECISION'])) {
                    $precision = $column['PRECISION'];
                }
                $format = sprintf('%%%d.%dF', $precision - $scale, $scale);
                $value  = (float)sprintf($format, $value);
                break;

            case 'FLOAT':
                if (is_null($value) && $column['NULLABLE']) {
                    return null;
                }
                $value  = (float)sprintf('%F', $value);
                break;

            case 'DATE':
                $value  = $this->formatDate($value, false);
                break;
            case 'TIMESTAMP':
            case 'TIMESTAMP(6)': // Zend parse bug
                $value  = $this->formatDate($value);
                break;

            case 'VARCHAR':
            case 'VARCHAR2':
            case 'CLOB':
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
     * Returns valid IFNULL expression
     *
     * @param string $column
     * @param string $value OPTIONAL. Applies when $expression is NULL
     * @return Zend_Db_Expr
     */
    public function getIfNullSql($expression, $value = 0)
    {
        if ($expression instanceof Zend_Db_Expr || $expression instanceof Zend_Db_Select) {
            $expression = sprintf("NVL((%s), %s)", $expression, $value);
        } else {
            $expression = sprintf("NVL(%s, %s)", $expression, $value);
        }
        return new Zend_Db_Expr($expression);
    }

    /**
     * Generate fragment of SQL, that check value against multiple condition cases
     * and return different result depends on them
     *
     * @param string $valueName Name of value to check
     * @param array $casesResults Cases and results
     * @param string $defaultValue value to use if value doesnt conforme to any cases
     */
    public function getCaseSql($valueName, $casesResults, $defaultValue = null)
    {
        $expression = 'CASE ' . $valueName;
        foreach ($casesResults as $case => $result) {
            $expression .= ' WHEN ' . $case . ' THEN ' . $result;
        }
        if ($defaultValue !== null) {
            $expression .= ' ELSE ' . $defaultValue;
        }
        $expression .= ' END';
        return new Zend_Db_Expr($expression);
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
        $glue = empty($separator) ? ' || ' : " || '{$separator}' || ";
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

        return sprintf("INTERVAL '%d' %s", $interval, $this->_intervalUnits[$unit]);
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
        $expr = sprintf('( TO_DATE(%s) + %s)', $date, $this->_getIntervalUnitSql($interval, $unit));
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
        $expr = sprintf('(TO_DATE(%s) - %s)', $date, $this->_getIntervalUnitSql($interval, $unit));
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
        $convertMap = array(
            '%%'    => '%',
            '%H'    => 'HH24',
            '%i'    => 'MI',
            '%s'    => 'SS',
            '%d'    => 'DD',
            '%m'    => 'MM',
            '%Y'    => 'YYYY',
        );

        $format = strtr($format, $convertMap);
        $expr = sprintf("TO_CHAR(%s, '%s')", $date, $format);
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
        return new Zend_Db_Expr(sprintf('TRUNC(%s)', $date));
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
     * Truncate a table
     *
     * @param string $tableName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Oracle
     */
    public function truncateTable($tableName, $schemaName = null)
    {
        if (!$this->isTableExists($tableName, $schemaName)) {
            throw new Varien_Exception(sprintf('The table "%s" is not exists', $tableName));
        }

        $query = sprintf('TRUNCATE TABLE %s',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)));
        $this->query($query);

        return true;
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
     * @param Zend_Db_Statement_Interface $result
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
     * Retrieve trigger name for identity/everything
     *
     * @param string $tableName
     * @param string $fieldName
     * @return string
     */
    protected function _getTriggerName($tableName, $fieldName, $triggerType = self::TRIGGER_IDENTITY)
    {
        $hash = sprintf('trg_%s_%s_%s', $triggerType, $tableName, $fieldName);
        if (strlen($hash) > 30) {
            $short = Varien_Db_Helper::shortName($hash);
            if (strlen($short) > 30) {
                $hash = sprintf('trg%s', substr(md5($hash), 2, -3));
            } else {
                $hash = $short;
            }
        }
        return strtoupper($hash);
    }

    /**
     * Retrieve package name
     *
     * @param string $tableName
     * @param string $fieldName
     * @return string
     */
    protected function _getPackageName($tableName, $fieldName, $pkgType = self::PACKAGE_CASCADE_ACTION)
    {
        $hash = sprintf('pkg_%s_%s_%s', $pkgType, $tableName, $fieldName);
        if (strlen($hash) > 30) {
            $short = Varien_Db_Helper::shortName($hash);
            if (strlen($short) > 30) {
                $hash = sprintf('pkg%s', substr(md5($hash), 2, -3));
            } else {
                $hash = $short;
            }
        }
        return strtoupper($hash);
    }

    /**
     * Retrieve sequence name for table
     *
     * @param string $tableName
     * @return string
     */
    protected function _getSequenceName($tableName)
    {
        $hash = sprintf('sqc_%s', $tableName);
        if (strlen($hash) > 30) {
            $short = Varien_Db_Helper::shortName($hash);
            if (strlen($short) > 30) {
                $hash = sprintf('sq%s', substr(md5($hash), 2, -2));
            } else {
                $hash = $short;
            }
        }
        return strtoupper($hash);
    }

    /**
     * Retrieve function name hash for get sequence value
     *
     * @param string $tableName
     * @return string
     */
    protected function _getSequenceFunctionName($tableName)
    {
        $hash = sprintf('fncId_%s', $tableName);
        if (strlen($hash) > 30) {
            $short = Varien_Db_Helper::shortName($hash);
            if (strlen($short) > 30) {
                $hash = sprintf('fi%s', substr(md5($hash), 2, -2));
            } else {
                $hash = $short;
            }
        }

        return $hash;
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
        //$cIdentity  = false;

        // detect and validate column type
        if (is_null($ddlType) && isset($options['TYPE'])) {
            $ddlType = $options['TYPE'];
        } else if (is_null($ddlType) && isset($options['COLUMN_TYPE'])) {
            $ddlType = $options['COLUMN_TYPE'];
        }

        if (empty($ddlType) || !isset($this->_ddlColumnTypes[$ddlType])) {
            throw new Varien_Exception('Invalid column definition data');
        }

        if (array_key_exists('DEFAULT', $options)) {
            $cDefault = $options['DEFAULT'];
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
            case Varien_Db_Ddl_Table::TYPE_VARBINARY:
                if (empty($options['LENGTH'])) {
                    $options['LENGTH'] = Varien_Db_Ddl_Table::DEFAULT_TEXT_SIZE;
                } else {
                    $options['LENGTH'] = $this->_parseTextSize($options['LENGTH']);
                }
                if ($options['LENGTH'] <= 4000) {
                    $cType = 'VARCHAR2';
                    $cType = sprintf('%s(%d) ', $cType, $options['LENGTH']);
                } else {
                    $cType = 'CLOB';
                }
                break;
        }

        if (array_key_exists('NULLABLE', $options)) {
            $cNullable = (bool)$options['NULLABLE'];
        }

//        if (!empty($options['IDENTITY']) || !empty($options['AUTO_INCREMENT'])) {
//            $cIdentity = true;
//        }

        // prepare default value string
        if ($ddlType == Varien_Db_Ddl_Table::TYPE_TIMESTAMP) {
            if ($cDefault === null) {
                $cDefault = new Zend_Db_Expr('NULL');
            } elseif ($cDefault == Varien_Db_Ddl_Table::TIMESTAMP_INIT) {
                $cDefault = new Zend_Db_Expr('sysdate');
            } elseif ($cDefault == Varien_Db_Ddl_Table::TIMESTAMP_UPDATE) {
                $cDefault = new Zend_Db_Expr('/*0 ON UPDATE CURRENT_TIMESTAMP*/');
            } elseif ($cDefault == Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE) {
                $cDefault = new Zend_Db_Expr('sysdate /*CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP*/');
            } else {
                $cDefault = false;
            }
        } elseif (is_null($cDefault) && $cNullable) {
            $cDefault = new Zend_Db_Expr('NULL');
        }

        // Fix ORA-01451: column to be modified to NULL cannot be modified to NULL
        if (isset($options['TABLE_NAME'])) {
            $currentDdl = $this->describeTable($options['TABLE_NAME']);
            if ($cNullable != $currentDdl[$options['COLUMN_NAME']]['NULLABLE']){
                $cNullable = $cNullable ? ' NULL' : ' NOT NULL';
            } else {
                $cNullable = '';
            }
        } else {
            $cNullable = $cNullable ? ' NULL' : ' NOT NULL';
        }

        $colDef =  sprintf('%s%s%s',
            $cType,
            $cDefault !== false ? $this->quoteInto(' default ?', $cDefault) : '',
            $cNullable
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

        foreach ($columns as $columnData)
        {
            $columnDefinition = $this->_getColumnDefinition($columnData);

            if ($columnData['PRIMARY']) {
                $primary[$columnData['COLUMN_NAME']] = $columnData['PRIMARY_POSITION'];
            }

            $definition[] = sprintf('  %s %s',
                $this->quoteIdentifier($columnData['COLUMN_NAME']),
                $columnDefinition
            );
        }

        // CREATE PRIMARY KEY
        if (!empty($primary)) {
            asort($primary, SORT_NUMERIC);
            $primary = array_map(array($this, 'quoteIdentifier'), array_keys($primary));
            $definition[] = sprintf('  PRIMARY KEY (%s)', join(', ', $primary));
        }

        return $definition;
    }

    /**
     * Create foreign key update action
     *
     * @param Varien_Db_Ddl_Table $table
     * @return boolean
     */
    protected function _createForeignKeysActions(Varien_Db_Ddl_Table $table)
    {
        $foreignKeys = $table->getForeignKeys();
        $fkActions = array (Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_SET_NULL);

        if (!empty($foreignKeys)) {
            foreach ($foreignKeys as $fkData) {
                if (in_array($fkData['ON_UPDATE'], $fkActions)) {
                    $this->_addForeignKeyUpdateAction($table->getName(), $fkData['COLUMN_NAME'],
                            $fkData['REF_TABLE_NAME'], $fkData['REF_COLUMN_NAME'], $fkData['ON_UPDATE']);
                }
            }
        }
        return true;
    }
    /**
     * Create Sequence for table with range 0 to 4294967296
     *
     * @param string $tableName
     * @return Varien_Db_Adapter_Oracle
     */
    protected function _createSequence($tableName)
    {
        $sequenceName = $this->_getSequenceName($tableName);
        if (!$this->isSequenceExists($sequenceName)) {
            $query = 'CREATE SEQUENCE "%s" MINVALUE 0 MAXVALUE 4294967296 INCREMENT BY 1 START WITH 0 CACHE 20 NOORDER NOCYCLE';
            $this->query(sprintf($query, $this->quoteIdentifier($sequenceName)));
            $query = sprintf('SELECT "%s".NEXTVAL FROM dual', $this->quoteIdentifier($sequenceName));
            $this->query($query);
        }

        return $this;
    }

    /**
     * Create identity trigger
     *
     * @param string $tableName
     * @param string $columnName
     * @return Varien_Db_Adapter_Oracle
     */
    protected function _createIdentityTrigger($tableName, $columnName)
    {
        // create sequence
        $this->_createSequence($tableName);

        $sequenceName = $this->_getSequenceName($tableName);
        $triggerName  = $this->_getTriggerName($tableName, $columnName);
        $functionName = $this->_getSequenceFunctionName($tableName);

        $query = "CREATE OR REPLACE TRIGGER \"%s\"                         \n"
            . "BEFORE INSERT ON %s                                         \n"
            . "FOR EACH ROW                                                \n"
            . "DECLARE                                                     \n"
            . "PRAGMA AUTONOMOUS_TRANSACTION;                              \n"
            . "  l_currval   NUMBER;                                       \n"
            . "  l_increment NUMBER;                                       \n"
            . "BEGIN                                                       \n"
            . "  IF :new.%s IS NULL THEN                                   \n"
            . "    SELECT \"%s\".NEXTVAL                                   \n"
            . "    INTO :new.%s                                            \n"
            . "    FROM dual;                                              \n"
            . "  ELSE                                                      \n"
            . "    SELECT \"%s\".NEXTVAL                                   \n"
            . "    INTO l_currval                                          \n"
            . "    FROM dual;                                              \n"
            . "    IF l_currval < :new.%s THEN                             \n"
            . "      l_increment := :new.%s - l_currval - 1;               \n"
            . "      IF l_increment != 0 THEN                              \n"
            . "        EXECUTE IMMEDIATE                                   \n"
            . "          'ALTER SEQUENCE \"%s\" INCREMENT BY '             \n"
            . "            || TO_CHAR(l_increment);                        \n"
            . "        SELECT \"%s\".NEXTVAL                               \n"
            . "        INTO l_currval                                      \n"
            . "        FROM dual;                                          \n"
            . "        EXECUTE IMMEDIATE                                   \n"
            . "          'ALTER SEQUENCE \"%s\" INCREMENT BY 1';           \n"
            . "      END IF;                                               \n"
            . "    END IF;                                                 \n"
            . "  END IF;                                                   \n"
            . "END;                                                        \n";

        $quotedSequenceName = $this->quoteIdentifier($sequenceName);
        $query = sprintf($query,
            $this->quoteIdentifier($triggerName),
            $this->quoteIdentifier($tableName),
            $this->quoteIdentifier($columnName),
            $quotedSequenceName,
            $this->quoteIdentifier($columnName),
            $quotedSequenceName,
            $this->quoteIdentifier($columnName),
            $this->quoteIdentifier($columnName),
            $quotedSequenceName,
            $quotedSequenceName,
            $quotedSequenceName
        );

        $this->query($query);

        $query = "CREATE OR REPLACE FUNCTION %s RETURN NUMBER               \n"
            . "IS                                                           \n"
            . "  identity NUMBER;                                           \n"
            . "BEGIN                                                        \n"
            . "  SELECT \"%s\".CURRVAL INTO identity FROM dual;             \n"
            . "  RETURN identity;                                           \n"
            . "END;                                                         \n";

        $query = sprintf($query, $functionName, $quotedSequenceName);
        $this->query($query);

        return $this;
    }

    /**
     * Retrieve table indexes definition array for create table
     *
     * @param Varien_Db_Ddl_Table $table
     * @return array
     */
    protected function _createTimeUpdateTrigger($tableName, $columnName)
    {
        $triggerName  = $this->_getTriggerName($tableName, $columnName, self::TRIGGER_TIME_UPDATE);

        $query = "CREATE OR REPLACE TRIGGER \"%s\"                     \n"
            . "BEFORE UPDATE                                           \n"
            . "ON %s                                                   \n"
            . "FOR EACH ROW                                            \n"
            . "BEGIN                                                   \n"
            . "  IF :new.%s IS NULL THEN                               \n"
            . "    SELECT SYSDATE                                      \n"
            . "    INTO :new.%s                                        \n"
            . "    FROM dual;                                          \n"
            . "  END IF;                                               \n"
            . "END;                                                    \n";

        $query = sprintf($query,
            $this->quoteIdentifier($triggerName),
            $this->quoteIdentifier($tableName),
            $this->quoteIdentifier($columnName),
            $this->quoteIdentifier($columnName)
        );
        $this->query($query);

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
        $indexes = $table->getIndexes();
        if (!empty($indexes)) {
            foreach ($indexes as $indexData) {
                if (strtoupper($indexData['INDEX_NAME']) == 'PRIMARY') {
                    $indexType = Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY;
                } else {
                    $indexType = $indexData['TYPE'];
                }

                if ($indexType == Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE) {
                    continue;
                }

                $columns = array();
                foreach ($indexData['COLUMNS'] as $columnData) {
                    $columns[] = $columnData['NAME'];
                }

                $this->addIndex($table->getName(), $indexData['INDEX_NAME'], $columns, $indexType);
            }
        }
    }

    /**
     * Set comment on the table
     *
     * @param string $tableName
     * @param string $comment
     * @return Varien_Db_Adapter_Oracle
     */
    protected function _addTableComment($tableName, $comment)
    {
        $query = sprintf('COMMENT ON TABLE %s IS %s', $this->quoteIdentifier($tableName), $this->quote($comment));
        $this->query($query);

        return $this;
    }

    /**
     * Set comment on the column
     *
     * @param string $tableName
     * @param string $comment
     * @return Varien_Db_Adapter_Oracle
     */
    protected function _addColumnComment($tableName, $columnName, $comment)
    {
        $query = sprintf('COMMENT ON COLUMN %s.%s IS %s',
            $this->quoteIdentifier($tableName),
            $this->quoteIdentifier($columnName),
            $this->quote($comment));
        $this->query($query);

        return $this;
    }

    /**
     * Check is exists object comment
     *
     * @param string    $tableName
     * @param string    $columnName
     * @return boolean
     */
    protected function _checkCommentExists($tableName, $columnName = null, $schemaName = null)
    {
        if (!$schemaName){
            $schemaName = $this->_getSchemaName();
        }
        if (empty($columnName)) {
            $query = $this->select()
                ->from(array('tc' => 'all_tab_comments'), array())
                ->where('tc.table_name = ?', strtoupper($this->quoteIdentifier($tableName)))
                ->where('tc.owner = ?', $schemaName)
                ->columns(array('qty' => new Zend_Db_Expr('COUNT(1)')));
        } else {
            $query = $this->select()
                ->from(array('tc' => 'all_col_comments'), array())
                ->where('tc.table_name = ?', strtoupper($this->quoteIdentifier($tableName)))
                ->where('tc.column_name = ?', strtoupper($this->quoteIdentifier($columnName)))
                ->where('tc.owner = ?', $schemaName)
                ->columns(array('qty' => new Zend_Db_Expr('COUNT(1)')));
        }

        return ($this->raw_fetchRow($query, 'qty') != 0);
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
                if ($constraintData['TYPE'] != Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE) {
                    continue;
                }
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
        $onDeleteAction = '';

        $definition = array();
        $relations  = $table->getForeignKeys();
        if (!empty($relations)) {
            foreach ($relations as $fkData) {
                /**
                 * @todo update actions
                 */
                switch ($fkData['ON_DELETE']) {
                    case Varien_Db_Ddl_Table::ACTION_CASCADE:
                    case Varien_Db_Ddl_Table::ACTION_RESTRICT:
                    case Varien_Db_Ddl_Table::ACTION_SET_NULL:
                        $onDelete = $fkData['ON_DELETE'];
                        break;
                    default:
                        $onDelete = '';
                }
                if (!empty($onDelete)) {
                    $onDeleteAction = sprintf('ON DELETE %s', $onDelete);
                }


                $definition[] = sprintf('  CONSTRAINT "%s" FOREIGN KEY (%s) REFERENCES %s (%s) %s',
                    //ON UPDATE %s',
                    $this->quoteIdentifier($fkData['FK_NAME'])
                    ,$this->quoteIdentifier($fkData['COLUMN_NAME'])
                    ,$this->quoteIdentifier($fkData['REF_TABLE_NAME'])
                    ,$this->quoteIdentifier($fkData['REF_COLUMN_NAME'])
                    ,$onDeleteAction
                    //$onUpdate
                );
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

        $sqlFragment = array_merge(
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

        $tableName = $this->_getTableName($table->getName(), $table->getSchema());

        if ($table->getComment()) {
            $this->_addTableComment($tableName, $table->getComment());
        }

        foreach ($columns as $columnData) {
            if ($columnData['IDENTITY'] === true) {
                $this->_createIdentityTrigger($tableName, $columnData['COLUMN_NAME']);
            }
            if (array_key_exists('DEFAULT', $columnData)) {
                $cDefault = $columnData['DEFAULT'];

                if ($columnData['COLUMN_TYPE'] == Varien_Db_Ddl_Table::TYPE_TIMESTAMP) {
                    if ($cDefault == Varien_Db_Ddl_Table::TIMESTAMP_UPDATE |
                        $cDefault == Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE) {
                        $this->_createTimeUpdateTrigger($tableName, $columnData['COLUMN_NAME']);
                    }
                }
            }
            if (!empty($columnData['COMMENT'])) {
                $this->_addColumnComment($table->getName(), $columnData['COLUMN_NAME'], $columnData['COMMENT']);
            }
        }
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
     * Oracle does not support IDENTITY columns, so if the sequence is not
     * specified, this method returns null.
     *
     * @param string $tableName   OPTIONAL Name of table.
     * @param string $primaryKey  OPTIONAL Name of primary key column.
     * @return string
     */
    public function lastInsertId($tableName = null, $primaryKey = null)
    {
        if ($tableName !== null) {
            $sequenceName = $this->_getSequenceFunctionName($tableName);
            return $this->lastSequenceId($sequenceName);
        }

        // No support for IDENTITY columns; return null
        return null;
    }

    /**
     * Return the most recent value from the specified sequence in the database.
     * This is supported only on RDBMS brands that support sequences
     * (e.g. Oracle, PostgreSQL, DB2).  Other RDBMS brands return null.
     *
     * @param string $sequenceName
     * @return string
     */
    public function lastSequenceId($sequenceName)
    {
        $this->_connect();

        $query = sprintf('SELECT %s FROM dual', $this->quoteIdentifier($sequenceName));
        $value = $this->fetchOne($query);

        return $value;
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
     * Minus superfluous characters from hash.
     *
     * @param  $hash
     * @param  $prefix
     * @param  $maxCharacters
     * @return string
     */
     protected function _minusSuperfluous($hash, $prefix, $maxCharacters)
     {
         $diff        = strlen($hash) + strlen($prefix) -  $maxCharacters;
         $superfluous = $diff / 2;
         $odd         = $diff % 2;
         $hash        = substr($hash, $superfluous, -($superfluous+$odd));
         return $hash;
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
        $prefix = 'table_';
        if (strlen($tableName) > self::LENGTH_TABLE_NAME) {
            $shortName = Varien_Db_Helper::shortName($tableName);
            if (strlen($shortName) > self::LENGTH_TABLE_NAME) {
                $hash = md5($tableName);
                if (strlen($hash) + strlen($prefix) > self::LENGTH_TABLE_NAME) {
                    $hash = $this->_minusSuperfluous($hash, $prefix, self::LENGTH_TABLE_NAME);
                }
                $tableName = $prefix . $hash;
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
    public function getIndexName($tableName, $fields, $indexType = '')
    {
        if (is_array($fields)) {
            $fields = implode('_', $fields);
        }

        switch (strtolower($indexType)) {
            case Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE:
                $prefix = 'unq_';
                break;
            case Varien_Db_Adapter_Interface::INDEX_TYPE_FULLTEXT:
                $prefix = 'fti_';
                break;
            case Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX:
            default:
                $prefix = 'idx_';
        }

        $hash = $tableName . $fields;

        if (strlen($hash) + strlen($prefix) > self::LENGTH_INDEX_NAME) {
            $short = Varien_Db_Helper::shortName($hash);
            if (strlen($short) + strlen($prefix) > self::LENGTH_INDEX_NAME) {
                $hash = md5($hash);
                if (strlen($hash) + strlen($prefix) > self::LENGTH_INDEX_NAME) {
                    $hash = $this->_minusSuperfluous($hash, $prefix, self::LENGTH_INDEX_NAME);
                }
            } else {
                $hash = $short;
            }
        }

        return strtoupper($prefix . $hash);
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
        if (strlen($hash) + strlen($prefix) > self::LENGTH_FOREIGN_NAME) {
            $short = Varien_Db_Helper::shortName($hash);
            if (strlen($short) + strlen($prefix) > self::LENGTH_FOREIGN_NAME) {
                $hash = md5($hash);
                if (strlen($hash) + strlen($prefix) > self::LENGTH_FOREIGN_NAME) {
                    $hash = $this->_minusSuperfluous($hash, $prefix, self::LENGTH_FOREIGN_NAME);
                }
            } else {
                $hash = $short;
            }
        }

        return strtoupper($prefix.$hash);
    }

    /**
     * Create db objects for cascade update
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $refTableName
     * @param string $refColumnName
     * @return Varien_Db_Adapter_Pdo_Oracle
     */
    protected function _addForeignKeyUpdateAction($tableName, $columnName, $refTableName, $refColumnName, $fkAction)
    {
        $updateValue = ($fkAction == Varien_Db_Ddl_Table::ACTION_CASCADE ? "s_new(i)" : "NULL");
        /*
         * Create package
         */
        $pkgName                = $this->_getPackageName($tableName, $columnName);
        $beforeTabUpdTrgName    = $this->_getTriggerName($tableName, $columnName, self::TRIGGER_BEFORE_UPDATE);
        $beforeTabRowUpdTrgName = $this->_getTriggerName($tableName, $columnName, self::TRIGGER_BEFORE_UPDATE_ER);
        $afterTabUpdTrgName     = $this->_getTriggerName($tableName, $columnName, self::TRIGGER_AFTER_UPDATE);
        $pkgCascadeUpdateIntf =
            "CREATE OR REPLACE PACKAGE \"{$pkgName}\"                       \n"
            . "AS                                                           \n"
            . "  /*session inerface variables*/                             \n"
            . "  s_row_count NUMBER DEFAULT 0;                              \n"
            . "  s_inTrigger boolean default FALSE;                         \n"
            . "  TYPE l_column_type IS TABLE OF {$refTableName}.{$refColumnName}%type INDEX BY BINARY_INTEGER;  \n"
            . "  s_empty l_column_type;                                     \n"
            . "  s_old   l_column_type;                                     \n"
            . "  s_new   l_column_type;                                     \n"
            . "  /**/                                                       \n"
            . "  PROCEDURE reset;                                           \n"
            . "  PROCEDURE do_cascade;                                      \n"
            . "  PROCEDURE add_entry (                                      \n"
            . "    p_old IN     {$refTableName}.{$refColumnName}%type,      \n"
            . "    p_new IN OUT {$refTableName}.{$refColumnName}%type       \n"
            . "  );                                                         \n"
            . "END;                                                         \n";

            $pkgCascadeUpdateBody =
            "CREATE OR REPLACE PACKAGE BODY \"{$pkgName}\"                  \n"
            . "AS                                                           \n"
            . "  /* reset colection*/                                       \n"
            . "  PROCEDURE reset                                            \n"
            . "  IS                                                         \n"
            . "  BEGIN                                                      \n"
            . "    IF ( s_inTrigger ) THEN                                  \n"
            . "      RETURN;                                                \n"
            . "    END IF;                                                  \n"
            . "    s_row_count := 0;                                        \n"
            . "    s_old := s_empty;                                        \n"
            . "    s_old := s_empty;                                        \n"
            . "  END reset;                                                 \n"
            . "  /* Add entries into collection*/                           \n"
            . "  PROCEDURE add_entry (                                      \n"
            . "    p_old IN     {$refTableName}.{$refColumnName}%type,      \n"
            . "    p_new IN OUT {$refTableName}.{$refColumnName}%type       \n"
            . "  )                                                          \n"
            . "  IS                                                         \n"
            . "  BEGIN                                                      \n"
            . "    IF ( s_inTrigger ) THEN                                  \n"
            . "      RETURN;                                                \n"
            . "    END IF;                                                  \n"
            . "    IF ( p_old != p_new ) THEN                               \n"
            . "      s_row_count := s_row_count + 1;                        \n"
            . "      s_old(s_row_count) := p_old;                           \n"
            . "      s_new(s_row_count) := p_new;                           \n"
            . "      p_new := p_old;                                        \n"
            . "    END IF;                                                  \n"
            . "  END add_entry;                                             \n"
            . "  /*cascade update from collection*/                         \n"
            . "  PROCEDURE do_cascade                                       \n"
            . "  IS                                                         \n"
            . "  BEGIN                                                      \n"
            . "    IF ( s_inTrigger ) THEN                                  \n"
            . "      RETURN;                                                \n"
            . "    END IF;                                                  \n"
            . "    s_inTrigger := TRUE;                                     \n"
            . "    FOR i IN 1 .. s_row_count                                \n"
            . "    LOOP                                                     \n"
            . "    BEGIN                                                    \n"
            . "      UPDATE {$tableName}                                    \n"
            . "      SET {$columnName} = {$updateValue}                     \n"
            . "      WHERE {$columnName} = s_old(i);                        \n"
            . "    END;                                                     \n"
            . "    END LOOP;                                                \n"
            . "    s_inTrigger := FALSE;                                    \n"
            . "    reset;                                                   \n"
            . "  EXCEPTION                                                  \n"
            . "    WHEN OTHERS THEN                                         \n"
            . "      s_inTrigger := FALSE;                                  \n"
            . "      reset;                                                 \n"
            . "      raise;                                                 \n"
            . "  END do_cascade;                                            \n"
            . "END \"{$pkgName}\";                                          \n";
        /*
         * Create triggers
         */
        $beforeTabUpdTrg = "CREATE OR REPLACE TRIGGER \"{$beforeTabUpdTrgName}\" \n"
            . "BEFORE UPDATE OF                                             \n"
            . "  {$refColumnName}                                           \n"
            . "ON {$refTableName}                                           \n"
            . "BEGIN                                                        \n"
            . "  {$pkgName}.reset;                                          \n"
            . "END;                                                         \n";

        $beforeTabRowUpdTrg = "CREATE OR REPLACE TRIGGER \"{$beforeTabRowUpdTrgName}\" \n"
            . "BEFORE UPDATE OF                                             \n"
            . "  {$refColumnName}                                           \n"
            . "ON {$refTableName}                                           \n"
            . "FOR EACH ROW                                                 \n"
            . "BEGIN                                                        \n"
            . "  {$pkgName}.add_entry(                                      \n"
            . "    :old.{$refColumnName},                                   \n"
            . "    :new.{$refColumnName}                                    \n"
            . "  );                                                         \n"
            . "END;                                                         \n";

        $afterTabUpdTrg = "CREATE OR REPLACE TRIGGER \"{$afterTabUpdTrgName}\" \n"
            . "AFTER UPDATE OF                                              \n"
            . "  {$refColumnName}                                           \n"
            . "ON {$refTableName}                                           \n"
            . "BEGIN                                                        \n"
            . "  {$pkgName}.do_cascade;                                     \n"
            . "END;                                                         \n";

        $this->raw_query($pkgCascadeUpdateIntf);
        $this->raw_query($pkgCascadeUpdateBody);
        $this->raw_query($beforeTabUpdTrg);
        $this->raw_query($beforeTabRowUpdTrg);
        $this->raw_query($afterTabUpdTrg);

        return $this;
    }

    /**
     * Retrieve name of the default schema being used in the current schema
     *
     * @return string
     */
    protected function _getSchemaName()
    {
        if ($this->_schemaName === null) {
            $this->_schemaName = $this->fetchOne("SELECT sys_context('USERENV', 'CURRENT_SCHEMA') FROM dual");
        }

        return $this->_schemaName;
    }

    /**
     * Stop updating nonunique indexes
     *
     * @param string $tableName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Oracle
     */
    public function disableTableKeys($tableName, $schemaName = null)
    {
        $indexList = $this->getIndexList($tableName, $schemaName);
        foreach ($indexList as $indexProp) {
            if (strtolower($indexProp['INDEX_TYPE']) != Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX) {
                continue;
            }
            $query = sprintf('ALTER INDEX %s UNUSABLE', $this->quoteIdentifier($indexProp['KEY_NAME']));
            $this->query($query);
        }

        return $this;
    }

    /**
     * Re-create missing indexes
     *
     * @param string $tableName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Oracle
     */
    public function enableTableKeys($tableName, $schemaName = null)
    {
        $indexList = $this->getIndexList($tableName, $schemaName);
        foreach ($indexList as $indexProp) {
            if (strtolower($indexProp['INDEX_TYPE']) != Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX) {
                continue;
            }
            $query = sprintf('ALTER INDEX %s REBUILD', $this->quoteIdentifier($indexProp['KEY_NAME']));
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
            throw new Varien_Db_Exception('Wrong columns count in SELECT for INSERT');
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
            if ($indexData['INDEX_TYPE'] != self::INDEX_TYPE_UNIQUE) {
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

        $query = sprintf("MERGE INTO %s t3\nUSING (%s) t2\nON ( %s )",
            $this->quoteIdentifier($table),
            $select->assemble(),
            join(' OR ', $whereCond)
        );

        // UPDATE Section
        if ($mode == self::INSERT_ON_DUPLICATE && $updateCols) {
            $updateCond = array();
            foreach ($updateCols as $column) {
                $updateCond[] = sprintf('t3.%1$s = t2.%1$s', $this->quoteIdentifier($column));
            }
            $query = sprintf("%s\nWHEN MATCHED THEN UPDATE SET %s",
                $query,
                join(', ', $updateCond));
        }

        // INSERT SECTION
        // prepare insert columns condition and values
        $insertCond = array_map(array($this, 'quoteIdentifier'), $insertCols);
        $insertVals = array();
        foreach ($insertCols as $column) {
            $insertVals[] = sprintf('t2.%s', $this->quoteIdentifier($column));
        }
        $query = sprintf("%s\nWHEN NOT MATCHED THEN INSERT (%s) VALUES (%s)",
            $query,
            join(', ', $insertCond),
            join(', ', $insertVals)
        );

        return $query;
    }


    /**
     * Get insert from Select object query compatible with Oracle 8
     *
     * @param Varien_Db_Select $select
     * @param string $table     insert into table
     * @param array $fields
     * @param int $mode
     * @return string
     * @throws Zend_Db_Exception
     */
    public function insertFromSelectCompatible(Varien_Db_Select $select, $table, array $fields = array(), $mode = false)
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
            throw new Zend_Db_Exception('Wrong columns count in SELECT for INSERT');
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
            if ($indexData['INDEX_TYPE'] != self::INDEX_TYPE_UNIQUE) {
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

        if (empty($whereCond)) {
            throw new Varien_Db_Exception('Invalid primary or unique columns in merge data');
        }

        $iodSelect = $this->select()
            ->from(array('t3' => new Zend_Db_Expr('('.$select->assemble().')')), $fields);

        ;
        $whereCondSql = implode(' AND ', $whereCond);
        $query = $this->_getInsertFromSelectSql(
            $iodSelect->where(new Zend_Db_Expr("NOT EXISTS (SELECT 1 FROM {$table} t2 WHERE {$whereCondSql})")),
            $table, $fields);


        if ($mode == self::INSERT_ON_DUPLICATE && $updateCols) {
            $query = sprintf("BEGIN\n%s;\n%s;\nEND;",
                $query,
                $this->updateFromSelect(
                    $iodSelect->where(new Zend_Db_Expr("EXISTS (SELECT 1 FROM {$table} t2 WHERE {$whereCondSql})")),
                    $table, $fields)
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
        $query = sprintf('INSERT INTO %s ', $this->quoteIdentifier($table));
        if ($fields) {
            $columns = array_map(array($this, 'quoteIdentifier'), $fields);
            $query .= sprintf('(%s)', join(', ', $columns));
        }

        $query .= $select->assemble();

        return $query;
    }

    /**
     * Updates table rows with specified data based on a WHERE clause.
     *
     * @param  mixed        $table The table to update.
     * @param  array        $bind  Column-value pairs.
     * @param  mixed        $where UPDATE WHERE clause(s).
     * @return int          The number of affected rows.
     */
    public function update($table, array $bind, $where = '')
    {
        /**
         * Build "col = ?" pairs for the statement,
         * except for Zend_Db_Expr which is treated literally.
         */
        $set = array();
        $i = 0;
        foreach ($bind as $col => $val) {
            if ($val instanceof Zend_Db_Expr) {
                $val = $val->__toString();
                unset($bind[$col]);
            } else {
                if ($this->supportsParameters('positional')) {
                    $val = '?';
                } else {
                    if ($this->supportsParameters('named')) {
                        unset($bind[$col]);
                        $bind[':vv'.$i] = $val;
                        $val = ':vv'.$i;
                        $i++;
                    } else {
                        /** @see Zend_Db_Adapter_Exception */
                        #require_once 'Zend/Db/Adapter/Exception.php';
                        throw new Zend_Db_Adapter_Exception(get_class($this) ." doesn't support positional or named binding");
                    }
                }
            }
            $set[] = $this->quoteIdentifier($col, true) . ' = ' . $val;
        }

        $where = $this->_whereExpr($where);

        /**
         * Build the UPDATE statement
         */
        $sql = "UPDATE "
             . $this->quoteIdentifier($table, true)
             . ' SET ' . implode(', ', $set)
             . (($where) ? " WHERE $where" : '');

        /**
         * Execute the statement and return the number of affected rows
         */
        if ($this->supportsParameters('positional')) {
            $stmt = $this->query($sql, array_values($bind));
        } else {
            $stmt = $this->query($sql, $bind);
        }
        $result = $stmt->rowCount();
        return $result;
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

        $query = sprintf('UPDATE %s', $this->quoteTableAs($tableName, $tableAlias));

        // render UPDATE SET
        $setCols    = array();
        $selectCols = array();
        foreach ($select->getPart(Zend_Db_Select::COLUMNS) as $columnEntry) {
            list($correlationName, $column, $alias) = $columnEntry;
            if (empty($alias)) {
                $alias = $column;
            }
            if (!$column instanceof Zend_Db_Expr && !empty($correlationName)) {
                $column = $this->quoteIdentifier(array($correlationName, $column));
            }
            $setCols[]    = $this->quoteIdentifier($alias);
            $selectCols[] = $column;
        }

        // render WHERE
        $wherePart  = $select->getPart(Zend_Db_Select::WHERE);

        // render SELECT SQL part
        $fromConds  = array();
        foreach ($select->getPart(Zend_Db_Select::FROM) as $correlationName => $joinProp) {
            if (empty($fromConds)) {
                // render FROM part
                $joinType = strtoupper(Zend_Db_Select::FROM);
                if (!empty($joinProp['joinCondition'])) {
                    if ($wherePart) {
                        $wherePart[] = sprintf(' AND (%s)', $joinProp['joinCondition']);
                    } else {
                        $wherePart[] = sprintf('%s', $joinProp['joinCondition']);
                    }
                    $joinProp['joinCondition'] = null;
                }
            } else if ($joinProp['joinType'] == Zend_Db_Select::FROM) {
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

            $fromConds[] = $join;
        }

        if (!$fromConds) {
            throw new Varien_Db_Exception('Invalid SELECT object for update data from SELECT');
        }

        $selectFragment = sprintf('%s', implode("\n", $fromConds));
        if ($wherePart) {
            $selectFragment = sprintf("%s\nWHERE %s", $selectFragment, implode(' ', $wherePart));
        }

        $query = sprintf("%1\$s\nSET (%2\$s) =\n(SELECT %3\$s %4\$s)\nWHERE EXISTS (SELECT 1 %4\$s)",
            $query,
            implode(', ', $setCols),
            implode(', ', $selectCols),
            $selectFragment
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
        // clone select
        $select = clone $select;

        $tableName  = null;
        $tableAlias = null;
        $fromPart   = $select->getPart(Zend_Db_Select::FROM);
        foreach ($fromPart as $correlationName => $joinProp) {
            if ($correlationName == $table || $joinProp['tableName'] == $table) {
                $tableName  = $joinProp['tableName'];
                $tableAlias = $correlationName;
                break;
            }
        }

        if (!$tableName) {
            throw new Exception('Invalid table name or table alias in SELECT');
        }

        $select->reset(Zend_Db_Select::DISTINCT);
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->columns(new Zend_Db_Expr('1'))
            ->where(sprintf('table_delete.rowid = %s.rowid', $tableAlias));

        $query = sprintf("DELETE FROM %s\nWHERE EXISTS (%s)",
            $this->quoteTableAs($tableName, 'table_delete'),
            $select->assemble()
        );
        return $query;
    }

    /**
     *
     */
    public function getTablesChecksum($tableNames, $schemaName = null)
    {
        $result = array();
        if (!$schemaName) {
            $schemaName = $this->_getSchemaName();
        }
        if(!is_array($tableNames)){
            $tableNames = array($tableNames);
        }
        foreach($tableNames as $tableName){
            $query = sprintf("SELECT SUM(CHECKSUM('%s', '%s', ROWID)) AS CHECKSUM FROM %s",
                $schemaName,
                $tableName,
                $this->_getTableName($tableName, $schemaName)
            );
            $result[$tableName] = $this->fetchOne($query);
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
     * @return Varien_Db_Adapter_Oracle
     */
    public function orderRand(Varien_Db_Select $select, $field = null)
    {
        $spec       = new Zend_Db_Expr('mage_rand');
        $expression = new Zend_Db_Expr('dbms_random.value()');
        $select->columns(array('mage_rand' => $expression));
        $select->order($spec);

        return $this;
    }

    /**
     * Render SQL FOR UPDATE clause
     *
     * @param string $sql
     * @return string
     */
    public function forUpdate($sql)
    {
        return sprintf('%s %s', $sql, Varien_Db_Adapter_Oracle::SQL_FOR_UPDATE);
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
     * The Oracle drivers do it nice, no processing required.
     *
     * @mixed $value
     * @return mixed
     */
    public function decodeVarbinary($value)
    {
        return $value;
    }
}
