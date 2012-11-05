<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Resource_SessionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Session table name
     */
    const SESSION_TABLE = 'session_table_name';

    /**#@+
     * Table column names
     */
    const COLUMN_SESSION_ID      = 'session_id';
    const COLUMN_SESSION_DATA    = 'session_data';
    const COLUMN_SESSION_EXPIRES = 'session_expires';
    /**#@-*/

    /**
     * Test select object
     */
    const SELECT_OBJECT = 'select_object';

    /**#@+
     * Test session data
     */
    const SESSION_ID   = 'custom_session_id';
    const SESSION_DATA = 'custom_session_data';
    /**#@-*/

    /**
     * Model under test
     *
     * @var Mage_Core_Model_Resource_Session
     */
    protected $_model;

    protected function tearDown()
    {
        unset($this->_model);
    }

    public function testRead()
    {
        $this->_prepareMockForRead();
        $result = $this->_model->read(self::SESSION_ID);
        $this->assertEquals(self::SESSION_DATA, $result);
    }

    /**
     * Prepares mock for test model with specified connections
     *
     * @param PHPUnit_Framework_MockObject_MockObject $readConnection
     * @param PHPUnit_Framework_MockObject_MockObject $writeConnection
     */
    protected function _prepareResourceMock($readConnection = null, $writeConnection = null)
    {
        $resource = $this->getMock('Mage_Core_Model_Resource', array('getTableName', 'getConnection'));
        $resource->expects($this->once())
            ->method('getTableName')
            ->will($this->returnValue(self::SESSION_TABLE));
        $resource->expects($this->at(1))
            ->method('getConnection')
            ->will($this->returnValue($readConnection));
        $resource->expects($this->at(2))
            ->method('getConnection')
            ->will($this->returnValue($writeConnection));

        $this->_model = new Mage_Core_Model_Resource_Session($resource);
    }

    /**
     * Prepare mocks for testRead
     */
    protected function _prepareMockForRead()
    {
        $readConnection = $this->getMock('stdClass', array('select', 'from', 'where', 'fetchOne'));
        $readConnection->expects($this->once())
            ->method('select')
            ->will($this->returnSelf());
        $readConnection->expects($this->once())
            ->method('from')
            ->with(self::SESSION_TABLE, array(self::COLUMN_SESSION_DATA))
            ->will($this->returnSelf());
        $readConnection->expects($this->once())
            ->method('where')
            ->with(self::COLUMN_SESSION_ID . ' = :' . self::COLUMN_SESSION_ID)
            ->will($this->returnValue(self::SELECT_OBJECT));
        $readConnection->expects($this->once())
            ->method('fetchOne')
            ->with(self::SELECT_OBJECT, array(self::COLUMN_SESSION_ID => self::SESSION_ID))
            ->will($this->returnValue(base64_encode(self::SESSION_DATA)));

        $this->_prepareResourceMock($readConnection);
    }

    /**
     * Data provider for testWrite
     *
     * @return array
     */
    public function writeDataProvider()
    {
        return array(
            'session_exists'     => array('$sessionExists' => true),
            'session_not_exists' => array('$sessionExists' => false),
        );
    }

    /**
     * @param bool $sessionExists
     *
     * @dataProvider writeDataProvider
     */
    public function testWrite($sessionExists)
    {
        $this->_prepareMockForWrite($sessionExists);
        $this->assertTrue($this->_model->write(self::SESSION_ID, self::SESSION_DATA));
    }

    /**
     * Prepare mocks for testWrite
     *
     * @param bool $sessionExists
     */
    protected function _prepareMockForWrite($sessionExists)
    {
        $readConnection = $this->getMock('stdClass', array('select', 'from', 'where', 'fetchOne'));
        $readConnection->expects($this->once())
            ->method('select')
            ->will($this->returnSelf());
        $readConnection->expects($this->once())
            ->method('from')
            ->with(self::SESSION_TABLE)
            ->will($this->returnSelf());
        $readConnection->expects($this->once())
            ->method('where')
            ->with(self::COLUMN_SESSION_ID . ' = :' . self::COLUMN_SESSION_ID)
            ->will($this->returnValue(self::SELECT_OBJECT));
        $readConnection->expects($this->once())
            ->method('fetchOne')
            ->with(self::SELECT_OBJECT, array(self::COLUMN_SESSION_ID => self::SESSION_ID))
            ->will($this->returnValue($sessionExists));

        $writeConnection = $this->getMock('stdClass', array('update', 'insert'));
        if ($sessionExists) {
            $writeConnection->expects($this->never())
                ->method('insert');
            $writeConnection->expects($this->once())
                ->method('update')
                ->will($this->returnCallback(array($this, 'verifyUpdate')));
        } else {
            $writeConnection->expects($this->once())
                ->method('insert')
                ->will($this->returnCallback(array($this, 'verifyInsert')));
            $writeConnection->expects($this->never())
                ->method('update');
        }

        $this->_prepareResourceMock($readConnection, $writeConnection);
    }

    /**
     * Verify arguments of insert method
     *
     * @param string $table
     * @param array $bind
     */
    public function verifyInsert($table, array $bind)
    {
        $this->assertEquals(self::SESSION_TABLE, $table);

        $this->assertInternalType('int', $bind[self::COLUMN_SESSION_EXPIRES]);
        $this->assertEquals(base64_encode(self::SESSION_DATA), $bind[self::COLUMN_SESSION_DATA]);
        $this->assertEquals(self::SESSION_ID, $bind[self::COLUMN_SESSION_ID]);
    }

    /**
     * Verify arguments of update method
     *
     * @param string $table
     * @param array $bind
     * @param array $where
     */
    public function verifyUpdate($table, array $bind, array $where)
    {
        $this->assertEquals(self::SESSION_TABLE, $table);

        $this->assertInternalType('int', $bind[self::COLUMN_SESSION_EXPIRES]);
        $this->assertEquals(base64_encode(self::SESSION_DATA), $bind[self::COLUMN_SESSION_DATA]);

        $this->assertEquals(array(self::COLUMN_SESSION_ID . '=?' => self::SESSION_ID), $where);
    }
}
