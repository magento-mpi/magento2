<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Resource_SessionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test session ID
     */
    const SESSION_ID = 'session_id_value';

    /**#@+
     * Session keys
     */
    const SESSION_NEW    = 'session_new';
    const SESSION_EXISTS = 'session_exists';
    /**#@-*/

    /**#@+
     * Table column names
     */
    const COLUMN_SESSION_ID      = 'session_id';
    const COLUMN_SESSION_DATA    = 'session_data';
    const COLUMN_SESSION_EXPIRES = 'session_expires';
    /**#@-*/

    /**
     * Test session data
     *
     * @var array
     */
    protected $_sourceData = array(
        self::SESSION_NEW    => array('new key'      => 'new value'),
        self::SESSION_EXISTS => array('existing key' => 'existing value'),
    );

    /**
     * Data as objects for serialization
     *
     * @var array
     */
    protected $_sessionData;

    /**
     * @var Magento_Test_ObjectManager
     */
    protected $_objectManager;

    /**
     * Model under test
     *
     * @var Magento_Core_Model_Resource_Session
     */
    protected $_model;

    /**
     * Write connection adapter
     *
     * @var Magento_DB_Adapter_Interface
     */
    protected $_connection;

    /**
     * Session table name
     *
     * @var string
     */
    protected $_sessionTable;

    public function setUp()
    {
        $this->_objectManager = Mage::getObjectManager();
        $this->_model         = $this->_objectManager->get('Magento_Core_Model_Resource_Session');

        /** @var $resource Magento_Core_Model_Resource */
        $resource            = $this->_objectManager->get('Magento_Core_Model_Resource');
        $this->_connection   = $resource->getConnection('core_write');
        $this->_sessionTable = $resource->getTableName('core_session');

        // session stores serialized objects with protected properties
        // we need to test this case to ensure that DB adapter successfully processes "\0" symbols in serialized data
        foreach ($this->_sourceData as $key => $data) {
            $this->_sessionData[$key] = new Magento_Object($data);
        }
    }

    public function testHasConnection()
    {
        $this->assertTrue($this->_model->hasConnection());
    }

    public function testOpenAndClose()
    {
        $this->assertTrue($this->_model->open('', 'test'));
        $this->assertTrue($this->_model->close());
    }

    public function testWriteReadDestroy()
    {
        $data = serialize($this->_sessionData[self::SESSION_NEW]);
        $this->_model->write(self::SESSION_ID, $data);
        $this->assertEquals($data, $this->_model->read(self::SESSION_ID));

        $data = serialize($this->_sessionData[self::SESSION_EXISTS]);
        $this->_model->write(self::SESSION_ID, $data);
        $this->assertEquals($data, $this->_model->read(self::SESSION_ID));

        $this->_model->destroy(self::SESSION_ID);
        $this->assertEmpty($this->_model->read(self::SESSION_ID));
    }

    public function testGc()
    {
        $this->_model->write('test', 'test');
        $this->assertEquals('test', $this->_model->read('test'));
        $this->_model->gc(-1);
        $this->assertEmpty($this->_model->read('test'));
    }

    /**
     * Assert that session data writes to DB in base64 encoding
     */
    public function testWriteEncoded()
    {
        $data = serialize($this->_sessionData[self::SESSION_NEW]);
        $this->_model->write(self::SESSION_ID, $data);

        $select = $this->_connection->select()
            ->from($this->_sessionTable)
            ->where(self::COLUMN_SESSION_ID . ' = :' . self::COLUMN_SESSION_ID);
        $bind = array(self::COLUMN_SESSION_ID => self::SESSION_ID);
        $session = $this->_connection->fetchRow($select, $bind);

        $this->assertEquals(self::SESSION_ID, $session[self::COLUMN_SESSION_ID]);
        $this->assertTrue(
            ctype_digit((string)$session[self::COLUMN_SESSION_EXPIRES]),
            'Value of session expire field must have integer type'
        );
        $this->assertEquals($data, base64_decode($session[self::COLUMN_SESSION_DATA]));
    }

    /**
     * Data provider for testReadEncoded
     *
     * @return array
     */
    public function readEncodedDataProvider()
    {
        // we can't use object data as a fixture because not encoded serialized object
        // might cause DB adapter fatal error, so we have to use array as a fixture
        $sessionData = serialize($this->_sourceData[self::SESSION_NEW]);
        return array(
            'session_encoded'     => array('$sessionData' => base64_encode($sessionData)),
            'session_not_encoded' => array('$sessionData' => $sessionData),
        );
    }

    /**
     * Assert that session data reads from DB correctly regardless of encoding
     *
     * @param string $sessionData
     *
     * @dataProvider readEncodedDataProvider
     */
    public function testReadEncoded($sessionData)
    {
        $sessionRecord = array(
            self::COLUMN_SESSION_ID   => self::SESSION_ID,
            self::COLUMN_SESSION_DATA => $sessionData,
        );
        $this->_connection->insertOnDuplicate($this->_sessionTable, $sessionRecord, array(self::COLUMN_SESSION_DATA));

        $sessionData = $this->_model->read(self::SESSION_ID);
        $this->assertEquals($this->_sourceData[self::SESSION_NEW], unserialize($sessionData));
    }
}
