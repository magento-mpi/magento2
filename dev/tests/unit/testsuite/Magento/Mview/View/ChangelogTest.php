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
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\DB\Adapter\Pdo\Mysql
     */
    protected $connectionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\App\Resource
     */
    protected $resourceMock;

    protected function setUp()
    {
        $this->connectionMock = $this->getMock('Magento\DB\Adapter\Pdo\Mysql', array(), array(), '', false);

        $this->resourceMock = $this->getMock(
            'Magento\App\Resource', array('getConnection', 'getTableName'), array(), '', false, false
        );
        $this->mockGetConnection($this->connectionMock);

        $this->model = new \Magento\Mview\View\Changelog($this->resourceMock);
    }

    public function testInstanceOf()
    {
        $resourceMock = $this->getMock('Magento\App\Resource', array('getConnection'), array(), '', false, false);
        $resourceMock->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue(true));
        $model = new \Magento\Mview\View\Changelog($resourceMock);
        $this->assertInstanceOf('\Magento\Mview\View\ChangelogInterface', $model);
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
        $model = new \Magento\Mview\View\Changelog($resourceMock);
        $model->setViewId('ViewIdTest');
        $this->assertNull($model);
    }

    public function testGetName()
    {
        $this->model->setViewId('ViewIdTest');
        $this->assertEquals('ViewIdTest' . '_' . \Magento\Mview\View\Changelog::NAME_SUFFIX, $this->model->getName());
    }

    public function testGetColumnName()
    {
        $this->assertEquals(\Magento\Mview\View\Changelog::COLUMN_NAME, $this->model->getColumnName());
    }

    public function testGetVersionWithException()
    {
        $changelogTableName = 'viewIdtest_cl';
        $this->mockIsTableExists($changelogTableName, false);
        $this->mockGetTableName();

        $this->setExpectedException('Exception', "Table {$changelogTableName} does not exist");
        $this->model->setViewId('viewIdtest');
        $this->model->getVersion();
    }

    public function testDropWithException()
    {
        $changelogTableName = 'viewIdtest_cl';
        $this->mockIsTableExists($changelogTableName, false);
        $this->mockGetTableName();

        $this->setExpectedException('Exception', "Table {$changelogTableName} does not exist");
        $this->model->setViewId('viewIdtest');
        $this->model->drop();
    }

    public function testCreateWithException()
    {
        $changelogTableName = 'viewIdtest_cl';
        $this->mockIsTableExists($changelogTableName, true);
        $this->mockGetTableName();

        $this->setExpectedException('Exception', "Table {$changelogTableName} already exist");
        $this->model->setViewId('viewIdtest');
        $this->model->create();
    }

    public function testGetListWithException()
    {
        $changelogTableName = 'viewIdtest_cl';
        $this->mockIsTableExists($changelogTableName, false);
        $this->mockGetTableName();

        $this->setExpectedException('Exception', "Table {$changelogTableName} does not exist");
        $this->model->setViewId('viewIdtest');
        $this->model->getList(mt_rand(1, 200), mt_rand(201, 400));
    }

    public function testClearWithException()
    {
        $changelogTableName = 'viewIdtest_cl';
        $this->mockIsTableExists($changelogTableName, false);
        $this->mockGetTableName();

        $this->setExpectedException('Exception', "Table {$changelogTableName} does not exist");
        $this->model->setViewId('viewIdtest');
        $this->model->clear(mt_rand(1, 200));
    }

    /**
     * @param $connection
     */
    protected function mockGetConnection($connection)
    {
        $this->resourceMock->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($connection));
    }

    protected function mockGetTableName()
    {
        $this->resourceMock->expects($this->once())
            ->method('getTableName')
            ->will($this->returnArgument(0));
    }

    protected function mockIsTableExists($changelogTableName, $result)
    {
        $this->connectionMock->expects($this->once())->method('isTableExists')
            ->with($this->equalTo($changelogTableName))
            ->will($this->returnValue($result));
    }
}
