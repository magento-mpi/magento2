<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset\MergeStrategy;

class FileExistsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\View\Asset\MergeStrategyInterface
     */
    private $mergerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Filesystem\Directory\WriteInterface
     */
    private $dirMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\View\Asset\File
     */
    private $resultAsset;

    /**
     * @var \Magento\View\Asset\MergeStrategy\FileExists
     */
    private $fileExists;

    protected function setUp()
    {
        $this->mergerMock = $this->getMockForAbstractClass('\Magento\View\Asset\MergeStrategyInterface');
        $this->dirMock = $this->getMockForAbstractClass('\Magento\Filesystem\Directory\ReadInterface');
        $filesystem = $this->getMock('\Magento\App\Filesystem', array(), array(), '', false);
        $filesystem->expects($this->once())
            ->method('getDirectoryRead')
            ->with(\Magento\App\Filesystem::STATIC_VIEW_DIR)
            ->will($this->returnValue($this->dirMock))
        ;
        $this->fileExists = new FileExists($this->mergerMock, $filesystem);
        $this->resultAsset = $this->getMock('\Magento\View\Asset\File', array(), array(), '', false);
        $this->resultAsset->expects($this->once())->method('getRelativePath')->will($this->returnValue('foo/file'));
    }

    public function testMergeExists()
    {
        $this->dirMock->expects($this->once())->method('isExist')->with('foo/file')->will($this->returnValue(true));
        $this->mergerMock->expects($this->never())->method('merge');
        $this->fileExists->merge(array(), $this->resultAsset);
    }

    public function testMergeNotExists()
    {
        $this->dirMock->expects($this->once())->method('isExist')->with('foo/file')->will($this->returnValue(false));
        $this->mergerMock->expects($this->once())->method('merge')->with(array(), $this->resultAsset);
        $this->fileExists->merge(array(), $this->resultAsset);
    }
}
