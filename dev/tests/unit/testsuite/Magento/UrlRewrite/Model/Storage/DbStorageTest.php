<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\UrlRewrite\Model\Resource;

use Magento\Framework\App\Resource;
use Magento\TestFramework\Helper\ObjectManager;
use Magento\UrlRewrite\Model\Storage\DbStorage;

class DbStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\UrlRewrite\Service\V1\Data\UrlRewriteBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlRewriteBuilder;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $adapter;

    /**
     * @var \Magento\Framework\DB\Select|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $select;

    /**
     * @var \Magento\Framework\App\Resource|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resource;

    /**
     * @var \Magento\UrlRewrite\Model\Storage\DbStorage
     */
    protected $storage;

    protected function setUp()
    {
        $this->urlRewriteBuilder = $this->getMock('Magento\UrlRewrite\Service\V1\Data\UrlRewriteBuilder', [], [], '',
            false);
        $this->adapter = $this->getMock('Magento\Framework\DB\Adapter\AdapterInterface');
        $this->select = $this->getMock('Magento\Framework\DB\Select', ['from', 'where', 'deleteFromSelect'], [], '',
            false);
        $this->resource = $this->getMock('Magento\Framework\App\Resource', [], [], '', false);

        $this->resource->expects($this->any())
            ->method('getConnection')
            ->with(Resource::DEFAULT_WRITE_RESOURCE)
            ->will($this->returnValue($this->adapter));
        $this->adapter->expects($this->any())
            ->method('select')
            ->will($this->returnValue($this->select));

        $this->storage = (new ObjectManager($this))->getObject(
            'Magento\UrlRewrite\Model\Storage\DbStorage',
            [
                'urlRewriteBuilder' => $this->urlRewriteBuilder,
                'resource' => $this->resource,
            ]
        );
    }

    public function testFindAllByData()
    {
        $data = ['col1' => 'val1', 'col2' => 'val2'];

        $this->select->expects($this->at(1))
            ->method('where')
            ->with('col1 IN (?)', 'val1');

        $this->select->expects($this->at(2))
            ->method('where')
            ->with('col2 IN (?)', 'val2');

        $this->adapter->expects($this->any())
            ->method('quoteIdentifier')
            ->will($this->returnArgument(0));

        $this->adapter->expects($this->once())
            ->method('fetchAll')
            ->with($this->select)
            ->will($this->returnValue([['row1'], ['row2']]));

        $this->urlRewriteBuilder->expects($this->at(0))
            ->method('populateWithArray')
            ->with(['row1'])
            ->will($this->returnSelf());

        $this->urlRewriteBuilder->expects($this->at(1))
            ->method('create')
            ->will($this->returnValue(['urlRewrite1']));

        $this->urlRewriteBuilder->expects($this->at(2))
            ->method('populateWithArray')
            ->with(['row2'])
            ->will($this->returnSelf());

        $this->urlRewriteBuilder->expects($this->at(3))
            ->method('create')
            ->will($this->returnValue(['urlRewrite2']));

        $this->assertEquals([['urlRewrite1'], ['urlRewrite2']], $this->storage->findAllByData($data));
    }

    public function testFindOneByFilter()
    {
        $data = ['col1' => 'val1', 'col2' => 'val2'];

        $this->select->expects($this->at(1))
            ->method('where')
            ->with('col1 IN (?)', 'val1');

        $this->select->expects($this->at(2))
            ->method('where')
            ->with('col2 IN (?)', 'val2');

        $this->adapter->expects($this->any())
            ->method('quoteIdentifier')
            ->will($this->returnArgument(0));

        $this->adapter->expects($this->once())
            ->method('fetchRow')
            ->with($this->select)
            ->will($this->returnValue(['row1']));

        $this->urlRewriteBuilder->expects($this->at(0))
            ->method('populateWithArray')
            ->with(['row1'])
            ->will($this->returnSelf());

        $this->urlRewriteBuilder->expects($this->at(1))
            ->method('create')
            ->will($this->returnValue(['urlRewrite1']));

        $this->assertEquals(['urlRewrite1'], $this->storage->findOneByData($data));
    }

    public function testAddMultiple()
    {
        $urlFirst = $this->getMock('Magento\UrlRewrite\Service\V1\Data\UrlRewrite', [], [], '', false);
        $urlSecond = $this->getMock('Magento\UrlRewrite\Service\V1\Data\UrlRewrite', [], [], '', false);

        $urlFirst->expects($this->any())
            ->method('toArray')
            ->will($this->returnValue(['row1']));
        $urlSecond->expects($this->any())
            ->method('toArray')
            ->will($this->returnValue(['row2']));

        $this->resource->expects($this->any())
            ->method('getTableName')
            ->with(DbStorage::TABLE_NAME)
            ->will($this->returnValue('table_name'));

        $this->adapter->expects($this->once())
            ->method('insertMultiple')
            ->with('table_name', [['row1'], ['row2']]);

        $this->storage->addMultiple([$urlFirst, $urlSecond]);
    }

    /**
     * @expectedException \Magento\UrlRewrite\Model\Storage\DuplicateEntryException
     */
    public function testAddMultipleIfThrewExceptionWithDuplicateEntryCode()
    {
        $url = $this->getMock('Magento\UrlRewrite\Service\V1\Data\UrlRewrite', [], [], '', false);

        $url->expects($this->any())
            ->method('toArray')
            ->will($this->returnValue(['row1']));

        $this->adapter->expects($this->once())
            ->method('insertMultiple')
            ->will(
                $this->throwException(
                    new \Exception('SQLSTATE[23000]: test: 1062 test', DbStorage::ERROR_CODE_DUPLICATE_ENTRY)
                )
            );

        $this->storage->addMultiple([$url]);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddMultipleIfThrewCustomException()
    {
        $url = $this->getMock('Magento\UrlRewrite\Service\V1\Data\UrlRewrite', [], [], '', false);

        $url->expects($this->any())
            ->method('toArray')
            ->will($this->returnValue(['row1']));

        $this->adapter->expects($this->once())
            ->method('insertMultiple')
            ->will($this->throwException(new \RuntimeException()));

        $this->storage->addMultiple([$url]);
    }

    public function testDeleteByFilter()
    {
        $data = ['col1' => 'val1', 'col2' => 'val2'];

        $this->adapter->expects($this->any())
            ->method('quoteIdentifier')
            ->will($this->returnArgument(0));

        $this->select->expects($this->at(1))
            ->method('where')
            ->with('col1 IN (?)', 'val1');

        $this->select->expects($this->at(2))
            ->method('where')
            ->with('col2 IN (?)', 'val2');

        $this->select->expects($this->at(3))
            ->method('deleteFromSelect')
            ->with('table_name')
            ->will($this->returnValue('sql delete query'));

        $this->resource->expects($this->any())
            ->method('getTableName')
            ->with(DbStorage::TABLE_NAME)
            ->will($this->returnValue('table_name'));

        $this->adapter->expects($this->once())
            ->method('query')
            ->with('sql delete query');

        $this->storage->deleteByData($data);
    }
}
