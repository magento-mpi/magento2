<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Data
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for Pdo MSSql adapter
 */
class Magento_DB_Adapter_Pdo_MssqlTest extends PHPUnit_Framework_TestCase
{
    /**
     * Fake table name
     *
     * @var string
     */
    protected $_tableName = 'test_table';

    /**
     * @covers Magento_DB_Adapter_Pdo_Mssql::_wrapEnableIdentityDataInsert
     */
    public function testWrapEnableIdentityDataInsert()
    {
        $identityInsertOn = sprintf(Magento_DB_Adapter_Pdo_Mssql::SET_IDENTITY_INSERT_ON, $this->_tableName);
        $identityInsertOff = sprintf(Magento_DB_Adapter_Pdo_Mssql::SET_IDENTITY_INSERT_OFF, $this->_tableName);

        // List of identity columns for test_table
        $identityColumns = array('id' => 'id');

        // insert data with identity fields
        $wrappedSql = $this->_wrapEnableIdentityDataInsert($identityColumns, array('id', 'column'));

        $this->assertContains($identityInsertOn, $wrappedSql, '', true);
        $this->assertContains($identityInsertOff, $wrappedSql, '', true);

        // insert data without identity fields
        $wrappedSql = $this->_wrapEnableIdentityDataInsert($identityColumns, array('column'));

        $this->assertNotContains($identityInsertOn, $wrappedSql, '', true);
        $this->assertNotContains($identityInsertOff, $wrappedSql, '', true);

        // insert data with empty update fields list
        $wrappedSql = $this->_wrapEnableIdentityDataInsert($identityColumns, array());

        $this->assertNotContains($identityInsertOn, $wrappedSql, '', true);
        $this->assertNotContains($identityInsertOff, $wrappedSql, '', true);

        // insert data in table which doesn't have identity fields
        $wrappedSql = $this->_wrapEnableIdentityDataInsert(array(), array('id', 'column'));

        $this->assertNotContains($identityInsertOn, $wrappedSql, '', true);
        $this->assertNotContains($identityInsertOff, $wrappedSql, '', true);
    }

    /**
     * @param array $identityColumns table identity columns
     * @param array $columns columns to update
     * @return string
     */
    protected function _wrapEnableIdentityDataInsert(array $identityColumns, array $columns)
    {
        $adapter = $this->getMock(
            'Magento_DB_Adapter_Pdo_Mssql',
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
}
