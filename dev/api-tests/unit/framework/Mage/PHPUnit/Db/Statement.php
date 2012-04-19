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
 * Statement for local database adapter
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Db_Statement extends Zend_Db_Statement
{
    /**
     * Result array
     *
     * @var array
     */
    protected $_result = array();

    /**
     * Rows count in result
     *
     * @var int
     */
    protected $_rowCount = 0;

    /**
     * Empty method. We don't need it for this statement
     */
    public function closeCursor()
    {
    }

    /**
     * Empty method. We don't need it for this statement
     */
    public function columnCount()
    {
    }

    /**
     * Empty method. We don't need it for this statement
     */
    public function errorCode()
    {
    }

    /**
     * Empty method. We don't need it for this statement
     */
    public function errorInfo()
    {
    }

    /**
     * Returns next data from result array.
     *
     * @param int|null $style one of Zend_Db::FETCH_* constants
     * @param null $cursor isn't used
     * @param null $offset isn't used
     * @return array of field-value pairs
     */
    public function fetch($style = null, $cursor = null, $offset = null)
    {
        $value = current($this->_result);
        next($this->_result);
        return Mage_PHPUnit_Db_Statement_Fetcher_Factory::getFetcher($style)->fetch($value);
    }

    /**
     * Sets result array from local server (from Mage_PHPUnit_Db_FixtureConnection)
     *
     * @param array $result
     */
    public function setResult($result)
    {
        $this->_result = $result;
        $this->_rowCount = count($result);
        reset($this->_result);
    }

    /**
     * Empty method. We don't need it for this statement
     */
    public function nextRowset()
    {
    }

    /**
     * Returns row count in result
     *
     * @return int
     */
    public function rowCount()
    {
        return $this->_rowCount;
    }

    /**
     * Execute method's stub.
     *
     * @param array $params
     * @return array
     */
    protected function _execute($params)
    {
        return array();
    }
}
