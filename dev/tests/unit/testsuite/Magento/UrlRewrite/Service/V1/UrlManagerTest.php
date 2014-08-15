<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\UrlRewrite\Service\V1;

use Magento\TestFramework\Helper\ObjectManager;
use Magento\UrlRewrite\Model\Storage\DuplicateEntryException;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class UrlManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\UrlRewrite\Model\StorageInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storage;

    /**
     * @var \Magento\UrlRewrite\Service\V1\Data\FilterFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filterFactory;

    /**
     * @var \Magento\UrlRewrite\Service\V1\Data\Filter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filter;

    /**
     * @var \Magento\UrlRewrite\Service\V1\UrlManager
     */
    protected $manager;

    protected function setUp()
    {
        $this->storage = $this->getMock('Magento\UrlRewrite\Model\StorageInterface');
        $this->filterFactory = $this->getMock('Magento\UrlRewrite\Service\V1\Data\FilterFactory', ['create'], [], '',
            false);
        $this->filter = $this->getMock('Magento\UrlRewrite\Service\V1\Data\Filter');

        $this->manager = (new ObjectManager($this))->getObject(
            'Magento\UrlRewrite\Service\V1\UrlManager',
            [
                'storage' => $this->storage,
                'filterFactory' => $this->filterFactory,
            ]
        );
    }

    public function testReplaceIfUrlsAreEmpty()
    {
        $this->storage->expects($this->never())->method('deleteByFilter');
        $this->storage->expects($this->never())->method('addMultiple');

        $this->manager->replace([]);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage URL key for specified store already exists.
     */
    public function testReplaceIfStorageThrewDuplicateEntryException()
    {
        $urlRewrites = [
            $this->getMock('Magento\UrlRewrite\Service\V1\Data\UrlRewrite', [], [], '', false),
        ];

        $this->filterFactory
            ->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->filter));

        $this->storage
            ->expects($this->once())
            ->method('addMultiple')
            ->will($this->throwException(new DuplicateEntryException()));

        $this->manager->replace($urlRewrites);
    }

    public function testReplace()
    {
        $urlRewriteOne = $this->getMock('Magento\UrlRewrite\Service\V1\Data\UrlRewrite', [], [], '', false);
        $urlRewriteSecond = $this->getMock('Magento\UrlRewrite\Service\V1\Data\UrlRewrite', [], [], '', false);

        $urlRewriteOne->expects($this->any())
            ->method('getByKey')
            ->will($this->returnValueMap([
                [UrlRewrite::ENTITY_ID, 'id_1'],
                [UrlRewrite::ENTITY_TYPE, 'type_1'],
                [UrlRewrite::STORE_ID, 'store_id_1'],
                [UrlRewrite::STORE_ID, 'store_id_1'],
            ]));

        $urlRewriteSecond->expects($this->any())
            ->method('getByKey')
            ->will($this->returnValueMap([
                [UrlRewrite::ENTITY_ID, 'id_2'],
                [UrlRewrite::ENTITY_TYPE, 'type_2'],
                [UrlRewrite::STORE_ID, 'store_id_2'],
            ]));

        $this->filterFactory
            ->expects($this->any())
            ->method('create')
            ->with([
                'filterData' => [
                    'entity_id' => ['id_1', 'id_2'],
                    'entity_type' => ['type_1', 'type_2'],
                    'store_id' => ['store_id_1', 'store_id_2'],
                ],
            ])
            ->will($this->returnValue($this->filter));

        $this->storage
            ->expects($this->once())
            ->method('deleteByFilter')
            ->with($this->filter);

        $this->storage
            ->expects($this->once())
            ->method('addMultiple')
            ->with([$urlRewriteOne, $urlRewriteSecond]);

        $this->manager->replace([$urlRewriteOne, $urlRewriteSecond]);
    }

    public function testDeleteByEntityData()
    {
        $filterData = ['data-for-filter'];

        $this->filterFactory
            ->expects($this->once())
            ->method('create')
            ->with(['filterData' => $filterData])
            ->will($this->returnValue($this->filter));

        $this->storage
            ->expects($this->once())
            ->method('deleteByFilter')
            ->with($this->filter);

        $this->manager->deleteByEntityData($filterData);
    }

    public function testMatch()
    {
        $this->markTestIncomplete('MAGETWO-26965');
    }

    public function testFindByEntity()
    {
        $this->markTestIncomplete('MAGETWO-26965');
    }

    public function testFindByFilter()
    {
        $this->storage
            ->expects($this->once())
            ->method('findByFilter')
            ->with($this->filter);

        $this->manager->findByFilter($this->filter);
    }

    public function testFindAllByFilter()
    {
        $this->storage
            ->expects($this->once())
            ->method('findAllByFilter')
            ->with($this->filter);

        $this->manager->findAllByFilter($this->filter);
    }
}
