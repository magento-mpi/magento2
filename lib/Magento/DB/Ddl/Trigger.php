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
    protected static $listOfTimes = array(
        self::TIME_BEFORE,
        self::TIME_AFTER,
    );

    /**
     * List of events available for trigger
     *
     * @var array
     */
    protected static $listOfEvents = array(
        self::EVENT_INSERT,
        self::EVENT_UPDATE,
        self::EVENT_DELETE,
    );

    /**
     * Name of trigger
     *
     * @var string
     */
    protected $name;

    /**
     * Time of trigger
     *
     * @var string
     */
    protected $time;

    /**
     * Time of trigger
     *
     * @var string
     */
    protected $event;

    /**
     * Table name
     *
     * @var string
     */
    protected $tableName;

    /**
     * List of statements for trigger body
     *
     * @var array
     */
    protected $statements = array();

    /**
     * Set trigger name
     *
     * @param string $name
     * @throws \InvalidArgumentException
     * @return \Magento\DB\Ddl\Trigger
     */
    public function setName($name)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException(__('Trigger name is not defined'));
        }

        $this->name = strtolower($name);
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
        if (is_null($this->name)) {
            throw new \Zend_Db_Exception(__('Trigger name is not defined'));
        }
        return $this->name;
    }

    /**
     * Set trigger time
     *
     * @param string $time
     * @throws \InvalidArgumentException
     * @return \Magento\DB\Ddl\Trigger
     */
    public function setTime($time)
    {
        if (in_array($time, self::$listOfTimes)) {
            $this->time = strtoupper($time);
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
        if (is_null($this->time)) {
            throw new \Zend_Db_Exception(__('Trigger time is not defined'));
        }
        return $this->time;
    }

    /**
     * Set trigger event
     *
     * @param string $event
     * @throws \InvalidArgumentException
     * @return \Magento\DB\Ddl\Trigger
     */
    public function setEvent($event)
    {
        if (in_array($event, self::$listOfEvents)) {
            $this->event = strtoupper($event);
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
        if (is_null($this->event)) {
            throw new \Zend_Db_Exception(__('Trigger event is not defined'));
        }
        return $this->event;
    }

    /**
     * Set table name
     *
     * @param string $name
     * @throws \InvalidArgumentException
     * @return \Magento\DB\Ddl\Trigger
     */
    public function setTable($name)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException(__('Table name is not defined'));
        }
        $this->tableName = strtolower($name);
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
        if (is_null($this->tableName)) {
            throw new \Zend_Db_Exception(__('Table name is not defined'));
        }
        return $this->tableName;
    }

    /**
     * Add statement to trigger
     *
     * @param string $statement
     * @throws \InvalidArgumentException
     * @return \Magento\DB\Ddl\Trigger
     */
    public function addStatement($statement)
    {
        if (empty($statement)) {
            throw new \InvalidArgumentException(__('Statement is not defined'));
        }

        $statement = trim($statement);
        if ((strrpos($statement, ';') + 1) <> strlen($statement)) {
            $statement = ';';
        }

        $this->statements[] = $statement;

        return $this;
    }

    /**
     * Retrieve list of statements of trigger
     *
     * @return array
     */
    public function getStatements()
    {
        return $this->statements;
    }

    /**
     * Retrieve list of times available for trigger
     *
     * @return array
     */
    public static function getListOfTimes()
    {
        return self::$listOfTimes;
    }

    /**
     * Retrieve list of events available for trigger
     *
     * @return array
     */
    public static function getListOfEvents()
    {
        return self::$listOfEvents;
    }
}
