<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor\Cache\Import;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class ImportEntityTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Css\PreProcessor\Cache\Import\ImportEntity */
    protected $importEntity;

    /**
     * @var \Magento\Filesystem\Directory\ReadInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rootDirectory;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Filesystem|\PHPUnit_Framework_MockObject_MockObject */
    protected $filesystemMock;

    /** @var \Magento\View\FileSystem|\PHPUnit_Framework_MockObject_MockObject */
    protected $fileSystemMock;

    /**
     * @var string
     */
    protected $absolutePath;

    /**
     * @param string $relativePath
     * @param int $originalMtime
     */
    protected function createMock($relativePath, $originalMtime)
    {
        $filePath = 'someFile';
        $params = ['some', 'params'];
        $this->absoluteFilePath = 'some_absolute_path';

        $this->rootDirectory = $this->getMock('Magento\Filesystem\Directory\ReadInterface', [], [], '', false);
        $this->rootDirectory->expects($this->once())
            ->method('getRelativePath')
            ->with($this->equalTo($this->absoluteFilePath))
            ->will($this->returnValue($relativePath));

        $this->rootDirectory->expects($this->atLeastOnce())
            ->method('stat')
            ->with($this->equalTo($relativePath))
            ->will($this->returnValue(['mtime' => $originalMtime]));

        $this->filesystemMock = $this->getMock('Magento\Filesystem', [], [], '', false);
        $this->filesystemMock->expects($this->once())
            ->method('getDirectoryRead')
            ->with($this->equalTo(\Magento\App\Filesystem::ROOT_DIR))
            ->will($this->returnValue($this->rootDirectory));

        $this->fileSystemMock = $this->getMock('Magento\View\FileSystem', [], [], '', false);
        $this->fileSystemMock->expects($this->once())
            ->method('getViewFile')
            ->with($this->equalTo($filePath), $this->equalTo($params))
            ->will($this->returnValue($this->absoluteFilePath));

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        /** @var \Magento\Css\PreProcessor\Cache\Import\ImportEntity importEntity */
        $this->importEntity = $this->objectManagerHelper->getObject(
            'Magento\Css\PreProcessor\Cache\Import\ImportEntity',
            [
                'filesystem' => $this->filesystemMock,
                'viewFileSystem' => $this->fileSystemMock,
                'filePath' => $filePath,
                'params' => $params
            ]
        );
        $rootDirectoryProperty = new \ReflectionProperty($this->importEntity, 'rootDirectory');
        $rootDirectoryProperty->setAccessible(true);
        $this->assertEquals($this->rootDirectory, $rootDirectoryProperty->getValue($this->importEntity));
    }

    public function testGetOriginalFile()
    {
        $mtime = rand();
        $relativePath = '/some/relative/path/to/file.less';
        $this->createMock($relativePath, $mtime);
        $this->assertEquals($relativePath, $this->importEntity->getOriginalFile());
    }

    public function testGetOriginalMtime()
    {
        $mtime = rand();
        $relativePath = '/some/relative/path/to/file2.less';
        $this->createMock($relativePath, $mtime);
        $this->assertEquals($mtime, $this->importEntity->getOriginalMtime());
    }

    public function testIsValid()
    {
        $mtime = rand();
        $relativePath = '/some/relative/path/to/file3.less';
        $this->createMock($relativePath, $mtime);
        $this->rootDirectory->expects($this->once())
            ->method('isFile')
            ->with($this->equalTo($relativePath))
            ->will($this->returnValue(true));
        $this->assertEquals(true, $this->importEntity->isValid());
    }

    public function test__sleep()
    {
        $mtime = rand();
        $relativePath = '/some/relative/path/to/file3.less';
        $this->createMock($relativePath, $mtime);
        $this->assertEquals(['originalFile', 'originalMtime'], $this->importEntity->__sleep());
    }
}
