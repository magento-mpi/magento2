<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Module\Setup;

class BaseTest extends \PHPUnit_Framework_TestCase
{
    const CONNECTION_NAME = 'connection';

    /**
     * @var \Magento\Framework\App\Resource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceModel;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $connection;

    /**
     * @var Base
     */
    private $object;

    protected function setUp()
    {
        $this->resourceModel = $this->getMock('\Magento\Framework\App\Resource', [], [], '', false);
        $this->connection = $this->getMockForAbstractClass('\Magento\Framework\DB\Adapter\AdapterInterface');
        $this->resourceModel->expects($this->any())
            ->method('getConnection')
            ->with(self::CONNECTION_NAME)
            ->will($this->returnValue($this->connection));
        $this->object = new Base($this->resourceModel, self::CONNECTION_NAME);
    }

    public function testGetConnection()
    {
        $this->assertSame($this->connection, $this->object->getConnection());
        // Check that new connection is not created every time
        $this->assertSame($this->connection, $this->object->getConnection());
    }

    public function testSetTableName()
    {
        $tableName = 'table';
        $expectedTableName = 'expected_table';

        $this->assertEmpty($this->object->getTable($tableName));
        $this->object->setTable($tableName, $expectedTableName);
        $this->assertSame($expectedTableName, $this->object->getTable($tableName));
    }

    public function testGetTable()
    {
        $tableName = 'table';
        $expectedTableName = 'expected_table';

        $this->resourceModel->expects($this->once())
            ->method('getTableName')
            ->with($tableName)
            ->will($this->returnValue($expectedTableName));

        $this->assertSame($expectedTableName, $this->object->getTable($tableName));
        // Check that table name is cached
        $this->assertSame($expectedTableName, $this->object->getTable($tableName));
    }

    public function testTableExists()
    {
        $tableName = 'table';
        $this->object->setTable($tableName, $tableName);
        $this->connection->expects($this->once())
            ->method('isTableExists')
            ->with($tableName)
            ->will($this->returnValue(true));
        $this->assertTrue($this->object->tableExists($tableName));
    }

    public function testRun()
    {
        $q = 'SELECT something';
        $this->connection->expects($this->once())
            ->method('multiQuery')
            ->with($q);
        $this->object->run($q);
    }

    public function testStartSetup()
    {
        $this->connection->expects($this->once())
            ->method('startSetup');
        $this->object->startSetup();
    }

    public function testEndSetup()
    {
        $this->connection->expects($this->once())
            ->method('endSetup');
        $this->object->endSetup();
    }
}
