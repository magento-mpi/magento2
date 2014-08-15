<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\UrlRewrite\Model\Resource;

class AbstractStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\UrlRewrite\Service\V1\Data\UrlRewriteBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlRewriteBuilder;

    /**
     * @var \Magento\UrlRewrite\Service\V1\Data\Filter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filter;

    /**
     * @var \Magento\UrlRewrite\Model\Storage\AbstractStorage|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storage;

    protected function setUp()
    {
        $this->urlRewriteBuilder = $this->getMockBuilder('Magento\UrlRewrite\Service\V1\Data\UrlRewriteBuilder')
            ->disableOriginalConstructor()->getMock();
        $this->filter = $this->getMock('Magento\UrlRewrite\Service\V1\Data\Filter');

        $this->storage = $this->getMockForAbstractClass(
            'Magento\UrlRewrite\Model\Storage\AbstractStorage',
            [$this->urlRewriteBuilder],
            '',
            true,
            true,
            true
        );
    }

    public function testFindAllByFilter()
    {
        $this->storage->expects($this->any())
            ->method('doFindAllByFilter')
            ->with($this->filter)
            ->will($this->returnValue([['row1']]));

        $this->urlRewriteBuilder->expects($this->any())
            ->method('populateWithArray')
            ->will($this->returnSelf());
        $this->urlRewriteBuilder->expects($this->any())
            ->method('create')
            ->will($this->returnValue(['urlRewrite1']));

        $this->assertEquals([['urlRewrite1']], $this->storage->findAllByFilter($this->filter));
    }

    public function testFindByFilterIfNotFound()
    {
        $this->storage->expects($this->any())
            ->method('doFindByFilter')
            ->with($this->filter)
            ->will($this->returnValue([]));

        $this->assertNull($this->storage->findByFilter($this->filter));
    }

    public function testFindByFilterIfFound()
    {
        $this->storage->expects($this->any())
            ->method('doFindByFilter')
            ->with($this->filter)
            ->will($this->returnValue(['row1']));

        $this->urlRewriteBuilder->expects($this->any())
            ->method('populateWithArray')
            ->will($this->returnSelf());
        $this->urlRewriteBuilder->expects($this->any())
            ->method('create')
            ->will($this->returnValue(['urlRewrite1']));

        $this->assertEquals(['urlRewrite1'], $this->storage->findByFilter($this->filter));
    }

    public function testAddMultiple()
    {
        $url1 = $this->getMock('Magento\UrlRewrite\Service\V1\Data\UrlRewrite', [], [], '', false);
        $url2 = $this->getMock('Magento\UrlRewrite\Service\V1\Data\UrlRewrite', [], [], '', false);
        $urlRewrites = [$url1, $url2];

        $url1->expects($this->any())->method('toArray')->will($this->returnValue(['row1']));
        $url2->expects($this->any())->method('toArray')->will($this->returnValue(['row2']));

        $this->storage->expects($this->any())
            ->method('doAddMultiple')
            ->with([['row1'], ['row2']]);

        $this->storage->addMultiple($urlRewrites);
    }
}
