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
 * Test class for \Magento\Core\Model\Resource\Db\AbstractDb.
 */
namespace Magento\Core\Model\Resource\Db;

class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Resource\Db\AbstractDb|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * @var \Magento\App\Resource|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;

    protected function setUp()
    {
        $this->_resource = $this->getMock('Magento\App\Resource',
            array('getConnection'), array(), '', false, false
        );
        $this->_model = $this->getMock(
            'Magento\Core\Model\Resource\Db\AbstractDb',
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
        $filesystem = $this->getMock('Magento\Filesystem', array(), array(), '', false);
        $string = $this->getMock('Magento\Stdlib\String', array(), array(), '', false);
        $dateTime = $this->getMock('Magento\Stdlib\DateTime', null, array(), '', true);
        $connection = new \Magento\DB\Adapter\Pdo\Mysql($filesystem, $string, $dateTime, array(
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
