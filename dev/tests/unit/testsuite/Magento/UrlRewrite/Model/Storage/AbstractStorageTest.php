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
     * @var \Magento\UrlRewrite\Service\V1\Data\UrlRewrite\Converter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $converter;

    /**
     * @var \Magento\UrlRewrite\Service\V1\Data\FilterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filter;

    /**
     * @var \Magento\UrlRewrite\Model\Storage\AbstractStorage|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storage;

    protected function setUp()
    {
        $this->converter = $this->getMock('Magento\UrlRewrite\Service\V1\Data\UrlRewrite\Converter', [], [], '', false);
        $this->filter = $this->getMock('Magento\UrlRewrite\Service\V1\Data\FilterInterface');

        $this->storage = $this->getMockForAbstractClass(
            'Magento\UrlRewrite\Model\Storage\AbstractStorage',
            [$this->converter],
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
            ->will($this->returnValue([['row1'], ['row2']]));

        $this->converter->expects($this->any())
            ->method('convertArrayToObject')
            ->will($this->returnValueMap([
                [['row1'], ['urlRewrite1']],
                [['row2'], ['urlRewrite2']],
            ]));

        $this->assertEquals([['urlRewrite1'], ['urlRewrite2']], $this->storage->findAllByFilter($this->filter));
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

        $this->converter->expects($this->any())
            ->method('convertArrayToObject')
            ->with(['row1'])
            ->will($this->returnValue(['urlRewrite1']));

        $this->assertEquals(['urlRewrite1'], $this->storage->findByFilter($this->filter));
    }

    public function testAddMultiple()
    {
        $urlRewrites = [
            $this->getMock('Magento\UrlRewrite\Service\V1\Data\UrlRewrite', [], [], '', false),
            $this->getMock('Magento\UrlRewrite\Service\V1\Data\UrlRewrite', [], [], '', false),
        ];

        $this->converter->expects($this->any())
            ->method('convertObjectToArray')
            ->will($this->returnValueMap([
                [$urlRewrites[0], ['row1']],
                [$urlRewrites[1], ['row2']],
            ]));

        $this->storage->expects($this->any())
            ->method('doAddMultiple')
            ->with([['row1'], ['row2']]);

        $this->storage->addMultiple($urlRewrites);
    }
}
