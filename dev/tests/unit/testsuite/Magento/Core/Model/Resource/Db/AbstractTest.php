<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Core_Model_Resource_Db_Abstract.
 */
class Magento_Core_Model_Resource_Db_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Resource_Db_Abstract|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * @var Magento_Core_Model_Resource|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;

    protected function setUp()
    {
        $this->_resource = $this->getMock('Magento_Core_Model_Resource',
            array('getConnection'), array(), '', false, false
        );
        $this->_model = $this->getMock(
            'Magento_Core_Model_Resource_Db_Abstract',
            array('_construct', '_getWriteAdapter'),
            array(
                $this->_resource
            )
        );
    }

    /**
     * Test that the model uses resource instance passed to the constructor
     */
    public function testConstructor()
    {
        /* Invariant: resource instance $this->_resource has been passed to the constructor in setUp() method */
        $this->_resource
            ->expects($this->atLeastOnce())
            ->method('getConnection')
            ->with('core_read')
        ;
        $this->_model->getReadConnection();
    }

    /**
     * Test that the model detects a connection when it becomes active
     */
    public function testGetConnectionInMemoryCaching()
    {
        $dir = $this->getMock('Magento_Core_Model_Dir', array(), array(), '', false);
        $connection = new Magento_DB_Adapter_Pdo_Mysql($dir, array(
            'dbname'   => 'test_dbname',
            'username' => 'test_username',
            'password' => 'test_password',
        ));
        $this->_resource
            ->expects($this->atLeastOnce())
            ->method('getConnection')
            ->with('core_read')
            ->will($this->onConsecutiveCalls(false/*inactive connection*/, $connection/*active connection*/, false))
        ;
        $this->assertFalse($this->_model->getReadConnection());
        $this->assertSame($connection, $this->_model->getReadConnection(), 'Inactive connection should not be cached');
        $this->assertSame($connection, $this->_model->getReadConnection(), 'Active connection should be cached');
    }
}
