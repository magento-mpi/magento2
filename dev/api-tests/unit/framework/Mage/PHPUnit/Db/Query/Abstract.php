<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Local DB Server queries data container abstract class.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_PHPUnit_Db_Query_Abstract
{
    /**
     * Queries data
     *
     * @var array array of 'full query text' => result_object_of_query.
     *  Example: array('SELECT * FROM order' => Mage_PHPUnit_Db_Server_Query_ResultItem object, ...)
     */
    protected $_data = array();

    /**
     * Returns result object for result of one query
     *
     * @return Mage_PHPUnit_Db_Query_ResultItem
     */
    protected function _getResultItem()
    {
        return new Mage_PHPUnit_Db_Query_ResultItem();
    }

    /**
     * Throws exception if a result was marked as 'error' in xml
     *
     * @param string $sql
     * @param array $bind
     * @throws Mage_PHPUnit_Db_Query_Exception
     */
    protected function _throwErrorResult($sql, $bind = array())
    {
        $message = isset($this->_data[$sql]) ? $this->_data[$sql]->getErrorMessage() : null;
        if ($message) {
            throw new Mage_PHPUnit_Db_Query_Exception($message);
        }
    }

    /**
     * Processes SQL query and sets result into statement from Local Db Server
     *
     * @param Zend_Db_Statement_Interface $statement
     * @param Mage_PHPUnit_Db_FixtureConnection $connection
     * @param string|Zend_Db_Select $sql
     * @param array $bind
     */
    public function process($statement, $connection, $sql, $bind = array())
    {
        if ($connection->getLoadFromTable()) {
            //returns result from table only
            $result = $connection->selectFromTable($this->_getTableName($sql));
        } else {
            $result = $this->getResultByQuery($sql, $bind);
        }
        $statement->setResult($result);
    }

    /**
     * Returns result by query
     *
     * @param string|Zend_Db_Select $sql
     * @param array $bind
     * @return mixed
     */
    public function getResultByQuery($sql, $bind = array())
    {
        $sql = $this->_compressQuery($sql);
        $this->_throwErrorResult($sql, $bind);
        return $this->_getDataByQuery($sql, $bind);
    }

    /**
     * Transform sql to small one.
     *
     * @param string|Zend_Db_Select $sql
     * @return string
     */
    protected function _compressQuery($sql)
    {
        $sql = Mage_PHPUnit_Db_Helper_Query::compress($sql);
        return function_exists('mb_strtolower') ? mb_strtolower($sql) : strtolower($sql);
    }

    /**
     * Returns normal result by query
     *
     * @param string $sql
     * @param array $bind
     * @return mixed
     */
    abstract protected function _getDataByQuery($sql, $bind = array());

    /**
     * Returns table name by SQL query
     *
     * @param string $sql
     * @throws Mage_PHPUnit_Db_Exception
     * @return string
     */
    abstract protected function _getTableName($sql);
}
