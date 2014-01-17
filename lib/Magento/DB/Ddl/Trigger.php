<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\DB\Ddl;

class Trigger
{
    /**#@+
     * Trigger times
     */
    const TIME_BEFORE = 'BEFORE';
    const TIME_AFTER  = 'AFTER';
    /**#@-*/

    /**#@+
     * Trigger events
     */
    const EVENT_INSERT = 'INSERT';
    const EVENT_UPDATE = 'UPDATE';
    const EVENT_DELETE = 'DELETE';
    /**#@-*/

    /**
     * List of times available for trigger
     *
     * @var array
     */
    protected static $_listOfTimes = array(
        self::TIME_BEFORE,
        self::TIME_AFTER,
    );

    /**
     * List of events available for trigger
     *
     * @var array
     */
    protected static $_listOfEvents = array(
        self::EVENT_INSERT,
        self::EVENT_UPDATE,
        self::EVENT_DELETE,
    );

    /**
     * Name of trigger
     *
     * @var string
     */
    protected $_triggerName;

    /**
     * Time of trigger
     *
     * @var string
     */
    protected $_time;

    /**
     * Time of trigger
     *
     * @var string
     */
    protected $_event;

    /**
     * Table name
     *
     * @var string
     */
    protected $_tableName;

    /**
     * List of statements for trigger body
     *
     * @var array
     */
    protected $_statementList = array();

    /**
     * Set trigger name
     *
     * @param string $name
     * @return \Magento\DB\Ddl\Trigger
     */
    public function setName($name = '')
    {
        $this->_triggerName = strtolower($name);
        return $this;
    }

    /**
     * Retrieve name of trigger
     *
     * @throws \Zend_Db_Exception
     * @return string
     */
    public function getName()
    {
        if (is_null($this->_triggerName)) {
            throw new \Zend_Db_Exception('Trigger name is not defined');
        }
        return $this->_triggerName;
    }

    /**
     * Set trigger time
     *
     * @param string $time
     * @throws \InvalidArgumentException
     * @return \Magento\DB\Ddl\Trigger
     */
    public function setTime($time = '')
    {
        if (in_array($time, self::$_listOfTimes)) {
            $this->_time = strtoupper($time);
        } else {
            throw new \InvalidArgumentException(__('Unsupported time type'));
        }
        return $this;
    }

    /**
     * Retrieve time of trigger
     *
     * @throws \Zend_Db_Exception
     * @return string
     */
    public function getTime()
    {
        if (is_null($this->_time)) {
            throw new \Zend_Db_Exception('Trigger time is not defined');
        }
        return $this->_time;
    }

    /**
     * Set trigger event
     *
     * @param string $event
     * @throws \InvalidArgumentException
     * @return \Magento\DB\Ddl\Trigger
     */
    public function setEvent($event = '')
    {
        if (in_array($event, self::$_listOfEvents)) {
            $this->_event = strtoupper($event);
        } else {
            throw new \InvalidArgumentException(__('Unsupported event type'));
        }
        return $this;
    }

    /**
     * Retrieve event of trigger
     *
     * @throws \Zend_Db_Exception
     * @return string
     */
    public function getEvent()
    {
        if (is_null($this->_event)) {
            throw new \Zend_Db_Exception('Trigger event is not defined');
        }
        return $this->_event;
    }

    /**
     * Set table name
     *
     * @param string $tableName
     * @throws \InvalidArgumentException
     * @return \Magento\DB\Ddl\Trigger
     */
    public function setTable($tableName = '')
    {
        if (empty($tableName)) {
            throw new \InvalidArgumentException(__('Table name is not defined'));
        }
        $this->_tableName = strtolower($tableName);
        return $this;
    }

    /**
     * Retrieve table name
     *
     * @throws \Zend_Db_Exception
     * @return string
     */
    public function getTable()
    {
        if (is_null($this->_tableName)) {
            throw new \Zend_Db_Exception('Table name is not defined');
        }
        return $this->_tableName;
    }

    /**
     * Add statement to trigger
     *
     * @param string $statement
     * @return \Magento\DB\Ddl\Trigger
     */
    public function addStatement($statement)
    {
        $this->_statementList[] = $statement;
        return $this;
    }

    /**
     * Retrieve list of statements of trigger
     *
     * @return array
     */
    public function getStatementList()
    {
        return $this->_statementList;
    }

    /**
     * Retrieve list of times available for trigger
     *
     * @return array
     */
    public static function getListOfTimes()
    {
        return self::$_listOfTimes;
    }

    /**
     * Retrieve list of events available for trigger
     *
     * @return array
     */
    public static function getListOfEvents()
    {
        return self::$_listOfEvents;
    }
}
