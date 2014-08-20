<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\UrlRewrite\Model\Resource;

use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class AbstractStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\UrlRewrite\Service\V1\Data\UrlRewriteBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlRewriteBuilder;

    /**
     * @var \Magento\UrlRewrite\Model\Storage\AbstractStorage|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storage;

    protected function setUp()
    {
        $this->urlRewriteBuilder = $this->getMockBuilder('Magento\UrlRewrite\Service\V1\Data\UrlRewriteBuilder')
            ->disableOriginalConstructor()->getMock();

        $this->storage = $this->getMockForAbstractClass(
            'Magento\UrlRewrite\Model\Storage\AbstractStorage',
            [$this->urlRewriteBuilder],
            '',
            true,
            true,
            true
        );
    }

    public function testFindAllByData()
    {
        $data = [['field1' => 'value1']];
        $rows = [['row1'], ['row2']];
        $urlRewrites = [['urlRewrite1'], ['urlRewrite2']];

        $this->storage->expects($this->once())
            ->method('doFindAllByData')
            ->with($data)
            ->will($this->returnValue($rows));

        $this->urlRewriteBuilder->expects($this->at(0))
            ->method('populateWithArray')
            ->with($rows[0])
            ->will($this->returnSelf());

        $this->urlRewriteBuilder->expects($this->at(1))
            ->method('create')
            ->will($this->returnValue($urlRewrites[0]));

        $this->urlRewriteBuilder->expects($this->at(2))
            ->method('populateWithArray')
            ->with($rows[1])
            ->will($this->returnSelf());

        $this->urlRewriteBuilder->expects($this->at(3))
            ->method('create')
            ->will($this->returnValue($urlRewrites[1]));

        $this->assertEquals($urlRewrites, $this->storage->findAllByData($data));
    }

    public function testFindOneByDataIfNotFound()
    {
        $data = [['field1' => 'value1']];

        $this->storage->expects($this->once())
            ->method('doFindOneByData')
            ->with($data)
            ->will($this->returnValue(null));

        $this->assertNull($this->storage->findOneByData($data));
    }

    public function testFindOneByDataIfFound()
    {
        $data = [['field1' => 'value1']];
        $row = ['row1'];
        $urlRewrite = ['urlRewrite1'];

        $this->storage->expects($this->once())
            ->method('doFindOneByData')
            ->with($data)
            ->will($this->returnValue($row));

        $this->urlRewriteBuilder->expects($this->once())
            ->method('populateWithArray')
            ->with($row)
            ->will($this->returnSelf());

        $this->urlRewriteBuilder->expects($this->any())
            ->method('create')
            ->will($this->returnValue($urlRewrite));

        $this->assertEquals($urlRewrite, $this->storage->findOneByData($data));
    }

    public function testAddMultiple()
    {
        $urlFirst = $this->getMock('Magento\UrlRewrite\Service\V1\Data\UrlRewrite', [], [], '', false);
        $urlSecond = $this->getMock('Magento\UrlRewrite\Service\V1\Data\UrlRewrite', [], [], '', false);

        $urlFirst->expects($this->any())->method('toArray')->will($this->returnValue(['row1']));
        $urlSecond->expects($this->any())->method('toArray')->will($this->returnValue(['row2']));

        $this->storage->expects($this->once())
            ->method('doAddMultiple')
            ->with([['row1'], ['row2']]);

        $this->storage->addMultiple([$urlFirst, $urlSecond]);
    }

    public function testReplaceIfUrlsAreEmpty()
    {
        $this->storage->expects($this->never())->method('deleteByData');
        $this->storage->expects($this->never())->method('addMultiple');

        $this->storage->replace([]);
    }

    public function testReplace()
    {
        $data = [
            'request_path' => ['path_1', 'path_2'],
            'store_id' => ['store_id_1', 'store_id_2'],
        ];

        $urlRewriteOne = $this->getMock('Magento\UrlRewrite\Service\V1\Data\UrlRewrite', [], [], '', false);
        $urlRewriteSecond = $this->getMock('Magento\UrlRewrite\Service\V1\Data\UrlRewrite', [], [], '', false);

        $urlRewriteOne->expects($this->any())
            ->method('getByKey')
            ->will($this->returnValueMap([
                [UrlRewrite::REQUEST_PATH, 'path_1'],
                [UrlRewrite::STORE_ID, 'store_id_1'],
                [UrlRewrite::STORE_ID, 'store_id_1'],
            ]));

        $urlRewriteSecond->expects($this->any())
            ->method('getByKey')
            ->will($this->returnValueMap([
                [UrlRewrite::REQUEST_PATH, 'path_2'],
                [UrlRewrite::STORE_ID, 'store_id_2'],
            ]));

        // TODO: UrlRewrite Delete this mocks
        $this->storage
            ->expects($this->once())
            ->method('deleteByData')
            ->with($data);

        $this->storage
            ->expects($this->once())
            ->method('addMultiple')
            ->with([$urlRewriteOne, $urlRewriteSecond]);

        $this->storage->replace([$urlRewriteOne, $urlRewriteSecond]);
    }
}
