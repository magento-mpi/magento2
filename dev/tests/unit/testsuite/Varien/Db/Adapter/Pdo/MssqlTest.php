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
     * Adapter for test
     * @var Varien_Db_Adapter_Pdo_Mssql|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_adapter;

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
     * Setup
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_adapter = $this->getMock(
            'Varien_Db_Adapter_Pdo_Mssql',
            array('_getIdentityColumns'),
            array(), '', false
        );
    }

    protected function tearDown()
    {
        $this->_adapter = null;

        parent::tearDown();
    }


    /**
     * Test wrapEnableIdentityDataInsert() method
     * in case when updating columns with identity are provided
     *
     * @covers Varien_Db_Adapter_Pdo_Mssql::wrapEnableIdentityDataInsert()
     */
    public function testWrapEnableIdentityDataInsertWithIdentityColumns()
    {
        $this->_adapter->expects($this->once())
             ->method('_getIdentityColumns')
             ->with($this->equalTo($this->_tableName), null)
             ->will($this->returnValue($this->_identityColumns));

        $setIdentityInsertOn = "SET IDENTITY_INSERT %s ON";
        $setIdentityInsertOff = "SET IDENTITY_INSERT %s OFF";

        $sql = 'sql';

        $columns = array('id', 'column');
        $wrappedSql = $this->_adapter->wrapEnableIdentityDataInsert($sql, $this->_tableName, $columns);

        $this->assertContains(sprintf($setIdentityInsertOn, $this->_tableName), $wrappedSql, '', true);
        $this->assertContains(sprintf($setIdentityInsertOff, $this->_tableName), $wrappedSql, '', true);
    }

    /**
     * Test wrapEnableIdentityDataInsert() method
     * in case when updating columns without identity column are provided
     *
     * @covers Varien_Db_Adapter_Pdo_Mssql::wrapEnableIdentityDataInsert()
     */
    public function testWrapEnableIdentityDataInsertWithoutIdentityColumns()
    {
        $this->_adapter->expects($this->once())
             ->method('_getIdentityColumns')
             ->with($this->equalTo($this->_tableName), null)
             ->will($this->returnValue($this->_identityColumns));

        $setIdentityInsertOn = "SET IDENTITY_INSERT %s ON";
        $setIdentityInsertOff = "SET IDENTITY_INSERT %s OFF";

        $sql = 'sql';

        $columns = array('column');
        $wrappedSql = $this->_adapter->wrapEnableIdentityDataInsert($sql, $this->_tableName, $columns);

        $this->assertNotContains(sprintf($setIdentityInsertOn, $this->_tableName), $wrappedSql, '', true);
        $this->assertNotContains(sprintf($setIdentityInsertOff, $this->_tableName), $wrappedSql, '', true);
    }

    /**
     * Test wrapEnableIdentityDataInsert() method in case when updating columns are NOT provided
     *
     * @covers Varien_Db_Adapter_Pdo_Mssql::wrapEnableIdentityDataInsert()
     */
    public function testWrapEnableIdentityDataInsertWithoutColumns()
    {
        $this->_adapter->expects($this->never())
             ->method('_getIdentityColumns');

        $setIdentityInsertOn = "SET IDENTITY_INSERT %s ON";
        $setIdentityInsertOff = "SET IDENTITY_INSERT %s OFF";

        $sql = 'sql';
        $columns = array();
        $wrappedSql = $this->_adapter->wrapEnableIdentityDataInsert($sql, $this->_tableName, $columns);

        $this->assertNotContains(sprintf($setIdentityInsertOn, $this->_tableName), $wrappedSql, '', true);
        $this->assertNotContains(sprintf($setIdentityInsertOff, $this->_tableName), $wrappedSql, '', true);
    }

    /**
     * Test wrapEnableIdentityDataInsert() method in case when there are no identity columns in table
     *
     * @covers Varien_Db_Adapter_Pdo_Mssql::wrapEnableIdentityDataInsert()
     */
    public function testWrapEnableIdentityDataInsertNoIdentityInTable()
    {
        $this->_adapter->expects($this->once())
             ->method('_getIdentityColumns')
             ->will($this->returnValue(array()));

        $setIdentityInsertOn = "SET IDENTITY_INSERT %s ON";
        $setIdentityInsertOff = "SET IDENTITY_INSERT %s OFF";

        $sql = 'sql';
        $columns = array('id', 'column');
        $wrappedSql = $this->_adapter->wrapEnableIdentityDataInsert($sql, $this->_tableName, $columns);

        $this->assertNotContains(sprintf($setIdentityInsertOn, $this->_tableName), $wrappedSql, '', true);
        $this->assertNotContains(sprintf($setIdentityInsertOff, $this->_tableName), $wrappedSql, '', true);
    }
}
