<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Service\PreProcessing;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Service\PreProcessing\CacheStorage|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cacheStorage;

    /**
     * @var \Magento\Filesystem\Directory\ReadInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sourceDir;

    /**
     * @var \Magento\Filesystem\Directory\ReadInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $directory;

    /**
     * @var \Magento\View\Service\PreProcessing\Cache
     */
    private $object;

    protected function setUp()
    {
        $this->cacheStorage = $this->getMock(
            'Magento\View\Service\PreProcessing\CacheStorage', array(), array(), '', false
        );
        $this->sourceDir = $this->getMockForAbstractClass('Magento\Filesystem\Directory\ReadInterface');
        $this->sourceDir->expects($this->any())
            ->method('getRelativePath')
            ->will($this->returnArgument(0));
        $this->directory = $this->getMockForAbstractClass('\Magento\Filesystem\Directory\ReadInterface');

        $this->object = new \Magento\View\Service\PreProcessing\Cache(
            $this->cacheStorage, $this->sourceDir, ['%tmp%' => $this->directory]
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $directories must be a list of \Magento\Filesystem\Directory\ReadInterface
     */
    public function testConstructorException()
    {
        $directories = ['string'];
        new \Magento\View\Service\PreProcessing\Cache($this->cacheStorage, $this->sourceDir, $directories);
    }

    public function testGetProcessedFileFromCache()
    {
        $sourceFile = 'some/file';
        $expectedFile = '/root/tmp/some/file';
        $mtime = time();
        $this->sourceDir->expects($this->once())
            ->method('stat')
            ->will($this->returnValue(['mtime' => $mtime]));
        $expectedData = json_encode(['path' => '%tmp%/some/file', 'mtime' => $mtime]);
        $this->cacheStorage->expects($this->once())
            ->method('load')
            ->with($sourceFile)
            ->will($this->returnValue($expectedData));
        $this->directory->expects($this->once())
            ->method('getAbsolutePath')
            ->with('some/file')
            ->will($this->returnValue($expectedFile));

        $actualFile = $this->object->getProcessedFileFromCache($sourceFile);
        $this->assertSame($expectedFile, $actualFile);
    }

    public function testGetProcessedFileFromCacheOutdated()
    {
        $sourceFile = 'some/file';
        $mtime = time();
        $this->sourceDir->expects($this->once())
            ->method('stat')
            ->will($this->returnValue(['mtime' => $mtime]));
        $expectedData = json_encode(['path' => '%tmp%/some/file', 'mtime' => $mtime - 100]);
        $this->cacheStorage->expects($this->once())
            ->method('load')
            ->with($sourceFile)
            ->will($this->returnValue($expectedData));
        $this->directory->expects($this->never())
            ->method('getAbsolutePath');

        $actualFile = $this->object->getProcessedFileFromCache($sourceFile);
        $this->assertSame(false, $actualFile);
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Either 'path' or 'mtime' section is not found in cached data
     *
     * @dataProvider getProcessedFileFromCacheExceptionDataProvider
     */
    public function testGetProcessedFileFromCacheException($cachedData)
    {
        $sourceFile = 'some/file';
        $expectedData = json_encode($cachedData);
        $this->cacheStorage->expects($this->once())
            ->method('load')
            ->with($sourceFile)
            ->will($this->returnValue($expectedData));
        $this->object->getProcessedFileFromCache($sourceFile);
    }

    /**
     * @return array
     */
    public function getProcessedFileFromCacheExceptionDataProvider()
    {
        return [
            'no path'    => [['mtime' => time()]],
            'no mtime'   => [['path' => '%tmp%/some/file']],
        ];
    }

    public function testSaveProcessedFileToCache()
    {
        $sourceFile = 'some/file';
        $processedFile = '/root/tmp/some/file';
        $mtime = time();

        $this->directory->expects($this->once())
            ->method('getAbsolutePath')
            ->with()
            ->will($this->returnValue('/root/tmp'));
        $this->directory->expects($this->once())
            ->method('getRelativePath')
            ->with($processedFile)
            ->will($this->returnValue('some/file'));
        $this->sourceDir->expects($this->once())
            ->method('stat')
            ->will($this->returnValue(['mtime' => $mtime]));

        $expectedData = json_encode(['path' => '%tmp%/some/file', 'mtime' => $mtime]);
        $expectedCacheId = $sourceFile;

        $this->cacheStorage->expects($this->once())
            ->method('save')
            ->with($expectedData, $expectedCacheId);

        $this->object->saveProcessedFileToCache($processedFile, $sourceFile);
    }
}
