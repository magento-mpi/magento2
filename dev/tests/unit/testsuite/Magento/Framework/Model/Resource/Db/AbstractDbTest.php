<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Model\Resource\Db;

/**
 * Test class for \Magento\Framework\Model\Resource\Db\AbstractDb.
 */
class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Model\Resource\Db\AbstractDb|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * @var \Magento\Framework\App\Resource|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;

    protected function setUp()
    {
        $this->_resource = $this->getMockBuilder('Magento\Framework\App\Resource')
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getConnection',
                    'getTableName'
                ]
            )
            ->getMock();
        $this->_model = $this->getMock(
            'Magento\Framework\Model\Resource\Db\AbstractDb',
            array('_construct', '_getWriteAdapter'),
            array($this->_resource)
        );
    }

    /**
     * Test that the model uses resource instance passed to the constructor
     */
    public function testConstructor()
    {
        /* Invariant: resource instance $this->_resource has been passed to the constructor in setUp() method */
        $this->_resource->expects($this->atLeastOnce())->method('getConnection')->with('core_read');
        $this->_model->getReadConnection();
    }

    /**
     * Test that the model detects a connection when it becomes active
     */
    public function testGetConnectionInMemoryCaching()
    {
        $filesystem = $this->getMock('Magento\Framework\App\Filesystem', array(), array(), '', false);
        $string = $this->getMock('Magento\Framework\Stdlib\String', array(), array(), '', false);
        $dateTime = $this->getMock('Magento\Framework\Stdlib\DateTime', null, array(), '', true);
        $connection = new \Magento\Framework\DB\Adapter\Pdo\Mysql(
            $filesystem,
            $string,
            $dateTime,
            array('dbname' => 'test_dbname', 'username' => 'test_username', 'password' => 'test_password')
        );
        $this->_resource->expects(
            $this->atLeastOnce()
        )->method(
            'getConnection'
        )->with(
            'core_read'
        )->will(
            $this->onConsecutiveCalls(false/*inactive connection*/, $connection/*active connection*/, false)
        );
        $this->assertFalse($this->_model->getReadConnection());
        $this->assertSame($connection, $this->_model->getReadConnection(), 'Inactive connection should not be cached');
        $this->assertSame($connection, $this->_model->getReadConnection(), 'Active connection should be cached');
    }

    /**
     * @expectedException \Magento\Framework\Model\Exception
     */
    public function testGetMainTableNoInit()
    {
        $this->assertEquals($this->_model->getMainTable());
    }

    /**
     * @expectedException \Magento\Framework\Model\Exception
     */
    public function testGetIdFieldNameNoInit()
    {
        $this->assertEquals($this->_model->getIdFieldName());
    }
    
        // self::callProtectedMethod($this->_model, '_init', 'test_table');

    public static function callProtectedMethod($object, $method, array $args = [])
    {
        $class = new ReflectionClass(get_class($object));
        $method = $class->getMethod($method);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $args);
    }
}
