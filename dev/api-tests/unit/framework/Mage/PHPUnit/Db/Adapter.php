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
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Local DB adapter.
 * Needed to implement abstract methods from parent abstract class.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Db_Adapter extends Zend_Db_Adapter_Abstract
{
    /**
     * Default class name for a DB statement.
     *
     * @var string
     */
    protected $_defaultStmtClass = 'Mage_PHPUnit_Db_Statement';

    /**
     * Temporary Stub. Empty method.
     *
     * @param string $tableName
     * @param string|null $schemaName
     */
    public function describeTable($tableName, $schemaName = null)
    {

    }

    /**
     * Temporary Stub. Empty method.
     */
    public function listTables()
    {
        // TODO Auto-generated method stub
    }

    /**
     * Initializes connection instance.
     */
    protected function _connect()
    {
        $this->_connection = Mage_PHPUnit_Db_FixtureConnection::getInstance();
    }

    /**
     * Is adapter connected.
     *
     * @return bool
     */
    public function isConnected()
    {
        return !is_null($this->_connection);
    }

    /**
     * Closes connection.
     */
    public function closeConnection()
    {
        $this->_connection->reset();
        $this->_connection = null;
    }

    /**
     * Prepares statement
     *
     * @param mixed $sql
     * @return Zend_Db_Statement
     */
    public function prepare($sql)
    {
        $class = $this->getStatementClass();
        return new $class($this, $sql);
    }

    /**
     * Runs SQL query. Implemented only for SELECT queries
     *
     * @param mixed $sql
     * @param array $bind temporary isn't used
     * @return Zend_Db_Statement
     * @todo Implement for other queries like INSERT or DELETE, etc.
     */
    public function query($sql, $bind = array())
    {
        $this->_connect();
        $statement = $this->prepare($sql);
        $this->getConnection()->query($statement, $sql, $bind);
        return $statement;
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
     * Temporary Stub. Empty method.
     *
     * @param string|null $tableName
     * @param string|null $primaryKey
     */
    public function lastInsertId($tableName = null, $primaryKey = null)
    {
        // TODO Auto-generated method stub
    }

    /**
     * Temporary Stub. Empty method.
     */
    protected function _beginTransaction()
    {
        // TODO Auto-generated method stub
    }

    /**
     * Temporary Stub. Empty method.
     */
    protected function _commit()
    {
        // TODO Auto-generated method stub
    }

    /**
     * Temporary Stub. Empty method.
     */
    protected function _rollBack()
    {
        // TODO Auto-generated method stub
    }

    /**
     * Temporary Stub. Empty method.
     *
     * @param string $mode
     */
    public function setFetchMode($mode)
    {
        // TODO Auto-generated method stub
    }

    /**
     * Limit method. Adds limit string like "LIMIT 5 OFFSET 2".
     *
     * @param string $sql
     * @param int $count
     * @param int $offset
     * @return string
     */
    public function limit($sql, $count, $offset = 0)
    {
        $count = intval($count);
        if ($count <= 0) {
            /** @see Zend_Db_Adapter_Exception */
            throw new Zend_Db_Adapter_Exception("LIMIT argument count=$count is not valid");
        }

        $offset = intval($offset);
        if ($offset < 0) {
            /** @see Zend_Db_Adapter_Exception */
            throw new Zend_Db_Adapter_Exception("LIMIT argument offset=$offset is not valid");
        }

        $sql .= " LIMIT $count";
        if ($offset > 0) {
            $sql .= " OFFSET $offset";
        }

        return $sql;
    }

    /**
     * Temporary Stub. Empty method.
     *
     * @param string $type
     */
    public function supportsParameters($type)
    {
        // TODO Auto-generated method stub
    }

    /**
     * Returns server version
     *
     * @return string
     */
    public function getServerVersion()
    {
        // TODO Auto-generated method stub
        return '2.0';
    }
}
