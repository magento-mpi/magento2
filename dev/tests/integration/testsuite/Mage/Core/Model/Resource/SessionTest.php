<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Resource_SessionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Resource_Session
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Mage_Core_Model_Resource_Session();
    }

    protected function tearDown()
    {
        $this->_model = null;
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
        $sessionId = 'my_test_id';
        $data = serialize(array('test key'=>'test value'));

        $this->_model->write($sessionId, $data);
        $this->assertEquals($data, $this->_model->read($sessionId));

        $data   = serialize(array('new key'=>'new value'));
        $this->_model->write($sessionId, $data);
        $this->assertEquals($data, $this->_model->read($sessionId));

        $this->_model->destroy($sessionId);
        $this->assertEmpty($this->_model->read($sessionId));
    }

    public function testGc()
    {
        $this->_model->write('test', 'test');
        $this->assertEquals('test', $this->_model->read('test'));
        $this->_model->gc(-1);
        $this->assertEmpty($this->_model->read('test'));
    }
}
