<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Mview\View;

class ChangelogTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Mview\View\Changelog
     */
    protected $model;

    /**
     * Mysql PDO DB adapter mock
     *
     * @var \Magento\DB\Adapter\Pdo\Mysql
     */
    protected $connectionMock;

    /**
     * @var \Magento\App\Resource
     */
    protected $resourceMock;

    protected function setUp()
    {
        $this->connectionMock = $this->getMock('Magento\DB\Adapter\Pdo\Mysql', array(), array(), '', false);

        $this->resourceMock = $this->getMock('Magento\App\Resource', array('getConnection'), array(), '', false, false);
        $this->resourceMock->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($this->connectionMock));

        $this->model = new \Magento\Mview\View\Changelog($this->resourceMock, 'ViewIdTest');
    }

    public function testInstanceOf()
    {

        $this->assertInstanceOf('\Magento\Mview\View\ChangelogInterface', $this->model);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Write DB connection is not available
     */

    public function testCheckConnectionException()
    {
        $resourceMock = $this->getMock('Magento\App\Resource', array('getConnection'), array(), '', false, false);
        $resourceMock->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue(null));
        $model = new \Magento\Mview\View\Changelog($resourceMock, 'ViewIdTest');
        $this->assertNull($model);
    }

    public function testGetName()
    {
        $this->assertEquals('ViewIdTest' . '_' . \Magento\Mview\View\Changelog::SUFFIX_NAME, $this->model->getName());
    }

    public function testGetColumnName()
    {
        $this->assertEquals(\Magento\Mview\View\Changelog::COLUMN_NAME, $this->model->getColumnName());
    }

    public function testGetVersionWithException()
    {
        $changelogTableName = 'viewIdtest_cl';
        $connection = $this->getMock('Magento\DB\Adapter\Pdo\Mysql', array('isTableExists'), array(), '', false);
        $connection->expects($this->once())->method('isTableExists')
            ->with($this->equalTo($changelogTableName))
            ->will($this->returnValue(false));

        $resource = $this->getMock('Magento\App\Resource', array('getConnection'), array(), '', false, false);
        $resource->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($connection));
        $model = new \Magento\Mview\View\Changelog($resource, 'viewIdtest');
        $this->setExpectedException('Exception', "Table {$changelogTableName} does not exist");
        $model->getVersion();
    }

    public function testRemoveWithException()
    {
        $changelogTableName = 'viewIdtest_cl';
        $connection = $this->getMock('Magento\DB\Adapter\Pdo\Mysql', array('isTableExists'), array(), '', false);
        $connection->expects($this->once())->method('isTableExists')
            ->with($this->equalTo($changelogTableName))
            ->will($this->returnValue(false));

        $resource = $this->getMock('Magento\App\Resource', array('getConnection'), array(), '', false, false);
        $resource->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($connection));
        $model = new \Magento\Mview\View\Changelog($resource, 'viewIdtest');
        $this->setExpectedException('Exception', "Table {$changelogTableName} does not exist");
        $model->remove();
    }

    public function testCreateWithException()
    {
        $changelogTableName = 'viewIdtest_cl';
        $connection = $this->getMock('Magento\DB\Adapter\Pdo\Mysql', array('isTableExists'), array(), '', false);
        $connection->expects($this->once())->method('isTableExists')
            ->with($this->equalTo($changelogTableName))
            ->will($this->returnValue(true));

        $resource = $this->getMock('Magento\App\Resource', array('getConnection'), array(), '', false, false);
        $resource->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($connection));
        $model = new \Magento\Mview\View\Changelog($resource, 'viewIdtest');
        $this->setExpectedException('Exception', "Table {$changelogTableName} already exist");
        $model->create();
    }

    public function testGetListWithException()
    {
        $changelogTableName = 'viewIdtest_cl';
        $connection = $this->getMock('Magento\DB\Adapter\Pdo\Mysql', array('isTableExists'), array(), '', false);
        $connection->expects($this->once())->method('isTableExists')
            ->with($this->equalTo($changelogTableName))
            ->will($this->returnValue(false));

        $resource = $this->getMock('Magento\App\Resource', array('getConnection'), array(), '', false, false);
        $resource->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($connection));
        $model = new \Magento\Mview\View\Changelog($resource, 'viewIdtest');
        $this->setExpectedException('Exception', "Table {$changelogTableName} does not exist");
        $model->getList(mt_rand(1, 200));
    }

    public function testClearWithException()
    {
        $changelogTableName = 'viewIdtest_cl';
        $connection = $this->getMock('Magento\DB\Adapter\Pdo\Mysql', array('isTableExists'), array(), '', false);
        $connection->expects($this->once())->method('isTableExists')
            ->with($this->equalTo($changelogTableName))
            ->will($this->returnValue(false));

        $resource = $this->getMock('Magento\App\Resource', array('getConnection'), array(), '', false, false);
        $resource->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($connection));
        $model = new \Magento\Mview\View\Changelog($resource, 'viewIdtest');
        $this->setExpectedException('Exception', "Table {$changelogTableName} does not exist");
        $model->clear(mt_rand(1, 200));
    }
}
