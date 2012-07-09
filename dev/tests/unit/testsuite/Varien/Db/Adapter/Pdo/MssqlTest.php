<?php
/**
 * {license_notice}
 *
 * @category    Varien
 * @package     Varien_Data
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for Pdo MSSql adapter
 */
class Varien_Db_Adapter_Pdo_MssqlTest extends PHPUnit_Framework_TestCase
{
    /**
     * List of identity columns for test_table
     *
     * @var array
     */
    protected $_identityColumns = array('id' => 'id');

    /**
     * Fake table name
     *
     * @var string
     */
    protected $_tableName = 'test_table';

    /**
     * @var string
     */
    protected $_identityInsertOn = Varien_Db_Adapter_Pdo_Mssql::SET_IDENTITY_INSERT_ON;

    /**
     * @var string
     */
    protected $_identityInsertOff = Varien_Db_Adapter_Pdo_Mssql::SET_IDENTITY_INSERT_OFF;

    /**
     * @param array $identityColumns table identity columns
     * @param array $columns columns to update
     * @return string
     */
    protected function _wrapEnableIdentityDataInsert(array $identityColumns, array $columns)
    {
        $adapter = $this->getMock(
            'Varien_Db_Adapter_Pdo_Mssql',
            array('_getIdentityColumns'),
            array(), '', false
        );

        $adapter->expects($this->any())
             ->method('_getIdentityColumns')
             ->with($this->equalTo($this->_tableName), null)
             ->will($this->returnValue($identityColumns));

        $method = new ReflectionMethod($adapter, '_wrapEnableIdentityDataInsert');
        $method->setAccessible(true);

        return $method->invoke($adapter, '', $this->_tableName, $columns);
    }

    /**
     * @covers Varien_Db_Adapter_Pdo_Mssql::_wrapEnableIdentityDataInsert
     */
    public function testWrapEnableIdentityDataInsert()
    {
        // insert data with identity fields
        $columns = array('id', 'column');
        $wrappedSql = $this->_wrapEnableIdentityDataInsert($this->_identityColumns, $columns);

        $this->assertContains(sprintf($this->_identityInsertOn, $this->_tableName), $wrappedSql, '', true);
        $this->assertContains(sprintf($this->_identityInsertOff, $this->_tableName), $wrappedSql, '', true);

        // insert data without identity fields
        $columns = array('column');
        $wrappedSql = $this->_wrapEnableIdentityDataInsert($this->_identityColumns, $columns);

        $this->assertNotContains(sprintf($this->_identityInsertOn, $this->_tableName), $wrappedSql, '', true);
        $this->assertNotContains(sprintf($this->_identityInsertOff, $this->_tableName), $wrappedSql, '', true);

        // insert data with empty update fields list
        $columns = array();
        $wrappedSql = $this->_wrapEnableIdentityDataInsert($this->_identityColumns, $columns);

        $this->assertNotContains(sprintf($this->_identityInsertOn, $this->_tableName), $wrappedSql, '', true);
        $this->assertNotContains(sprintf($this->_identityInsertOff, $this->_tableName), $wrappedSql, '', true);

        // insert data in table which doesn't have identity fields
        $columns = array('id', 'column');
        $wrappedSql = $this->_wrapEnableIdentityDataInsert(array(), $columns);

        $this->assertNotContains(sprintf($this->_identityInsertOn, $this->_tableName), $wrappedSql, '', true);
        $this->assertNotContains(sprintf($this->_identityInsertOff, $this->_tableName), $wrappedSql, '', true);
    }
}
