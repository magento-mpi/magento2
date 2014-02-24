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
     * @var string
     */
    protected $absoluteFilePath;

    /**
     * @param string $relativePath
     * @param int $originalMtime
     */
    protected function createMock($relativePath, $originalMtime)
    {
        $filePath = 'someFile';
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

        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $lessFile = $this->getMock('Magento\Less\PreProcessor\File\Less', [], [], '', false);

        $lessFile->expects($this->any())
            ->method('getFilePath')
            ->will($this->returnValue($filePath));

        $lessFile->expects($this->any())
            ->method('getSourcePath')
            ->will($this->returnValue($this->absoluteFilePath));

        /** @var \Magento\Css\PreProcessor\Cache\Import\ImportEntity importEntity */
        $this->importEntity = $this->objectManagerHelper->getObject(
            'Magento\Css\PreProcessor\Cache\Import\ImportEntity',
            [
                'filesystem' => $this->filesystemMock,
                'viewFileSystem' => $this->fileSystemMock,
                'lessFile' => $lessFile
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

    /**
     * @param bool $isFile
     * @dataProvider isValidDataProvider
     */
    public function testIsValid($isFile)
    {
        $mtime = rand();
        $relativePath = '/some/relative/path/to/file3.less';
        $this->createMock($relativePath, $mtime);
        $this->rootDirectory->expects($this->once())
            ->method('isFile')
            ->with($this->equalTo($relativePath))
            ->will($this->returnValue($isFile));
        $this->assertEquals($isFile, $this->importEntity->isValid());
    }

    /**
     * @return array
     */
    public function isValidDataProvider()
    {
        return [
            [true],
            [false]
        ];
    }

    public function test__sleep()
    {
        $mtime = rand();
        $relativePath = '/some/relative/path/to/file3.less';
        $this->createMock($relativePath, $mtime);
        $this->assertEquals(['originalFile', 'originalMtime'], $this->importEntity->__sleep());
    }
}
