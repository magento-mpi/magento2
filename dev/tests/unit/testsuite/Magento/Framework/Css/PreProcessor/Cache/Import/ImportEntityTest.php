<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Css\PreProcessor\Cache\Import;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class ImportEntityTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Framework\Css\PreProcessor\Cache\Import\ImportEntity */
    protected $importEntity;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rootDirectory;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Framework\Filesystem|\PHPUnit_Framework_MockObject_MockObject */
    protected $filesystemMock;

    /** @var \Magento\Framework\View\FileSystem|\PHPUnit_Framework_MockObject_MockObject */
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

        $this->rootDirectory = $this->getMock(
            'Magento\Framework\Filesystem\Directory\ReadInterface',
            array(),
            array(),
            '',
            false
        );
        $this->rootDirectory->expects(
            $this->once()
        )->method(
            'getRelativePath'
        )->with(
            $this->equalTo($this->absoluteFilePath)
        )->will(
            $this->returnValue($relativePath)
        );

        $this->rootDirectory->expects(
            $this->atLeastOnce()
        )->method(
            'stat'
        )->with(
            $this->equalTo($relativePath)
        )->will(
            $this->returnValue(array('mtime' => $originalMtime))
        );

        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $lessFile = $this->getMock('Magento\Framework\Less\PreProcessor\File\Less', array(), array(), '', false);
        $lessFile->expects($this->any())->method('getFilePath')->will($this->returnValue($filePath));
        $lessFile->expects($this->any())->method('getSourcePath')->will($this->returnValue($this->absoluteFilePath));
        $lessFile->expects($this->any())->method('getDirectoryRead')->will($this->returnValue($this->rootDirectory));

        /** @var \Magento\Framework\Css\PreProcessor\Cache\Import\ImportEntity importEntity */
        $this->importEntity = $this->objectManagerHelper->getObject(
            'Magento\Framework\Css\PreProcessor\Cache\Import\ImportEntity',
            array('lessFile' => $lessFile)
        );
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
}
