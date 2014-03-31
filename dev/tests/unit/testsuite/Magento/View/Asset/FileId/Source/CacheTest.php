<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset\FileId\Source;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Asset\FileId\Source\CacheType|\PHPUnit_Framework_MockObject_MockObject
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
     * @var \Magento\View\Asset\FileId\Source\Cache
     */
    private $object;

    protected function setUp()
    {
        $this->cacheStorage = $this->getMock(
            'Magento\View\Asset\FileId\Source\CacheType', array(), array(), '', false
        );
        $this->sourceDir = $this->getMockForAbstractClass('Magento\Filesystem\Directory\ReadInterface');
        $this->sourceDir->expects($this->any())
            ->method('getRelativePath')
            ->will($this->returnArgument(0));
        $this->directory = $this->getMockForAbstractClass('\Magento\Filesystem\Directory\ReadInterface');

        $this->object = new Cache(
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
        new Cache($this->cacheStorage, $this->sourceDir, $directories);
    }

    public function testGetProcessedFileFromCache()
    {
        $sourceFile = 'some/file';
        $expectedFile = '/root/tmp/some/file';
        $expectedData = '%tmp%/some/file';
        $this->cacheStorage->expects($this->once())
            ->method('load')
            ->with($sourceFile)
            ->will($this->returnValue($expectedData));
        $this->directory->expects($this->once())
            ->method('isExist')
            ->with($sourceFile)
            ->will($this->returnValue(true));
        $this->directory->expects($this->once())
            ->method('getAbsolutePath')
            ->with('some/file')
            ->will($this->returnValue($expectedFile));

        $actualFile = $this->object->getProcessedFileFromCache($sourceFile);
        $this->assertSame($expectedFile, $actualFile);
    }

    public function testGetProcessedFileFromCacheNonexistent()
    {
        $sourceFile = 'some/file';
        $this->directory->expects($this->once())
            ->method('isExist')
            ->with($sourceFile)
            ->will($this->returnValue(false));
        $expectedData = '%tmp%/some/file';
        $this->cacheStorage->expects($this->once())
            ->method('load')
            ->with($sourceFile)
            ->will($this->returnValue($expectedData));
        $this->directory->expects($this->never())
            ->method('getAbsolutePath');

        $actualFile = $this->object->getProcessedFileFromCache($sourceFile);
        $this->assertSame(false, $actualFile);
    }

    public function testSaveProcessedFileToCache()
    {
        $sourceFile = 'some/file';
        $processedFile = '/root/tmp/some/file';

        $this->directory->expects($this->once())
            ->method('getAbsolutePath')
            ->with()
            ->will($this->returnValue('/root/tmp'));
        $this->directory->expects($this->once())
            ->method('getRelativePath')
            ->with($processedFile)
            ->will($this->returnValue('some/file'));

        $this->cacheStorage->expects($this->once())
            ->method('save')
            ->with('%tmp%/some/file', 'some/file');

        $this->object->saveProcessedFileToCache($processedFile, $sourceFile);
    }
}
