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

class UrlManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\UrlRewrite\Model\StorageInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storage;

    /**
     * @var \Magento\UrlRewrite\Service\V1\UrlManager
     */
    protected $manager;

    protected function setUp()
    {
        $this->storage = $this->getMock('Magento\UrlRewrite\Model\StorageInterface');
        
        $this->manager = (new ObjectManager($this))->getObject(
            'Magento\UrlRewrite\Service\V1\UrlManager',
            [
                'storage' => $this->storage,
            ]
        );
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

        $this->storage
            ->expects($this->once())
            ->method('replace')
            ->will($this->throwException(new DuplicateEntryException('Custom storage message')));

        $this->manager->replace($urlRewrites);
    }

    public function testReplace()
    {
        $this->storage
            ->expects($this->once())
            ->method('replace')
            ->with([['urlRewrite1'], ['urlRewrite2']]);

        $this->manager->replace([['urlRewrite1'], ['urlRewrite2']]);
    }

    public function testDeleteByData()
    {
        $this->storage
            ->expects($this->once())
            ->method('deleteByData')
            ->with(['filter-data']);

        $this->manager->deleteByData(['filter-data']);
    }

    public function testFindOneByData()
    {
        $this->storage
            ->expects($this->once())
            ->method('findOneByData')
            ->with(['filter-data']);

        $this->manager->findOneByData(['filter-data']);
    }

    public function testFindAllByData()
    {
        $this->storage
            ->expects($this->once())
            ->method('findAllByData')
            ->with(['filter-data']);

        $this->manager->findAllByData(['filter-data']);
    }
}
