<?php
/**
 * {license_notice}
 *
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Mview\View;
/**
 * Test Class for \Magento\Mview\View\Changelog
 */
class ChangelogTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\App\Resource
     */
    protected $resource;

    /**
     * Write connection adapter
     *
     * @var \Magento\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var \Magento\Mview\View\Changelog
     */
    protected $model;

    public function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->resource = $this->objectManager->get('Magento\App\Resource');
        $this->connection   = $this->resource->getConnection('core_write');

        $this->model = $this->objectManager->create('Magento\Mview\View\Changelog', array(
            'resource' => $this->resource,
            'viewId' => 'test_view_id_1'));
        $this->model->create();
    }

    public function tearDown()
    {
        $this->model->drop();
    }

    /**
     * Test for create() and drop() methods
     */
    public function testCreateAndDrop()
    {
        $model = $this->objectManager->create('Magento\Mview\View\Changelog', array(
            'resource' => $this->resource,
            'viewId' => 'test_view_id_2'));
        $changelogName = $this->connection->getTableName($model->getName());
        $this->assertFalse($this->connection->isTableExists($changelogName));
        $this->assertTrue($model->create());
        $this->assertTrue($this->connection->isTableExists($changelogName));
        $this->assertTrue($model->drop());
        $this->assertFalse($this->connection->isTableExists($changelogName));
    }

    /**
     * Test for getVersion() method
     */
    public function testGetVersion()
    {
        $model = $this->objectManager->create('Magento\Mview\View\Changelog', array(
            'resource' => $this->resource,
            'viewId' => 'test_view_id_2'));
        $this->assertTrue($model->create());
        $this->assertEquals(0, $model->getVersion());
        $changelogName = $this->connection->getTableName($model->getName());
        $this->connection->insert(
            $changelogName,
            array($model->getColumnName() => mt_rand(1, 200))
        );
        $this->assertEquals($this->connection->lastInsertId($changelogName, 'version_id'), $model->getVersion());
        $this->assertTrue($model->drop());
    }

    /**
     * Test for clear() method
     */
    public function testClear()
    {
        $this->assertEquals(0, $this->model->getVersion());//the same that a table is empty
        $changelogName = $this->connection->getTableName($this->model->getName());
        $this->connection->insert(
            $changelogName,
            array('version_id' => 1, 'entity_id' => 1)
        );
        $this->assertEquals(1, $this->model->getVersion());
        $this->model->clear(1);
        $this->assertEquals(0, $this->model->getVersion());//the same that a table is empty
    }

    /**
     * Test for getList() method
     */
    public function testGetList()
    {
        $this->assertEquals(0, $this->model->getVersion());//the same that a table is empty
        $changelogName = $this->connection->getTableName($this->model->getName());
        $testChengelogData =  array(
            array('version_id' => 1, 'entity_id' => 1),
            array('version_id' => 2, 'entity_id' => 1),
            array('version_id' => 3, 'entity_id' => 2),
            array('version_id' => 4, 'entity_id' => 3),
            array('version_id' => 5, 'entity_id' => 1)
        );
        foreach ($testChengelogData as $data) {
            $this->connection->insert(
                $changelogName,
                $data
            );
        }
        $this->assertEquals(5, $this->model->getVersion());
        $this->assertEquals(3, count($this->model->getList(0, 5)));//distinct entity_ids
        $this->assertEquals(3, count($this->model->getList(2, 5)));//distinct entity_ids
        $this->assertEquals(2, count($this->model->getList(0, 3)));//distinct entity_ids
        $this->assertEquals(1, count($this->model->getList(0, 2)));//distinct entity_ids
        $this->assertEquals(1, count($this->model->getList(2, 3)));//distinct entity_ids
        $this->assertEquals(0, count($this->model->getList(4, 3)));//because fromVersionId > toVersionId
    }
}
