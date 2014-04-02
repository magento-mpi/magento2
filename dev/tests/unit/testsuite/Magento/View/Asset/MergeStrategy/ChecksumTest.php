<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset\MergeStrategy;

class ChecksumTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\View\Asset\MergeStrategyInterface
     */
    private $mergerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Filesystem\Directory\ReadInterface
     */
    private $sourceDir;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Filesystem\Directory\WriteInterface
     */
    private $targetDir;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\View\Asset\File
     */
    private $resultAsset;

    /**
     * @var \Magento\View\Asset\MergeStrategy\Checksum
     */
    private $checksum;

    protected function setUp()
    {
        $this->mergerMock = $this->getMockForAbstractClass('\Magento\View\Asset\MergeStrategyInterface');
        $this->sourceDir = $this->getMockForAbstractClass('\Magento\Filesystem\Directory\ReadInterface');
        $this->targetDir = $this->getMockForAbstractClass('\Magento\Filesystem\Directory\WriteInterface');
        $filesystem = $this->getMock('\Magento\App\Filesystem', array(), array(), '', false);
        $filesystem->expects($this->once())
            ->method('getDirectoryRead')
            ->with(\Magento\App\Filesystem::ROOT_DIR)
            ->will($this->returnValue($this->sourceDir))
        ;
        $filesystem->expects($this->any())
            ->method('getDirectoryWrite')
            ->with(\Magento\App\Filesystem::STATIC_VIEW_DIR)
            ->will($this->returnValue($this->targetDir))
        ;
        $this->checksum = new Checksum($this->mergerMock, $filesystem);
        $this->resultAsset = $this->getMock('\Magento\View\Asset\File', array(), array(), '', false);
    }

    public function testMergeNoAssets()
    {
        $this->mergerMock->expects($this->never())->method('merge');
        $this->checksum->merge(array(), $this->resultAsset);
    }

    public function testMergeNoDatFile()
    {
        $this->targetDir->expects($this->once())
            ->method('isExist')
            ->with('merged/result.txt.dat')
            ->will($this->returnValue(false))
        ;
        $assets = $this->getAssetsToMerge();
        $this->mergerMock->expects($this->once())->method('merge')->with($assets, $this->resultAsset);
        $this->targetDir->expects($this->once())->method('writeFile')->with('merged/result.txt.dat', '11');
        $this->checksum->merge($assets, $this->resultAsset);
    }

    public function testMergeMtimeChanged()
    {
        $this->targetDir->expects($this->once())
            ->method('isExist')
            ->with('merged/result.txt.dat')
            ->will($this->returnValue(true))
        ;
        $this->targetDir->expects($this->once())
            ->method('readFile')
            ->with('merged/result.txt.dat')
            ->will($this->returnValue('10'))
        ;
        $assets = $this->getAssetsToMerge();
        $this->mergerMock->expects($this->once())->method('merge')->with($assets, $this->resultAsset);
        $this->targetDir->expects($this->once())->method('writeFile')->with('merged/result.txt.dat', '11');
        $this->checksum->merge($assets, $this->resultAsset);
    }

    public function testMergeMtimeUnchanged()
    {
        $this->targetDir->expects($this->once())
            ->method('isExist')
            ->with('merged/result.txt.dat')
            ->will($this->returnValue(true))
        ;
        $this->targetDir->expects($this->once())
            ->method('readFile')
            ->with('merged/result.txt.dat')
            ->will($this->returnValue('11'))
        ;
        $assets = $this->getAssetsToMerge();
        $this->mergerMock->expects($this->never())->method('merge');
        $this->targetDir->expects($this->never())->method('writeFile');
        $this->checksum->merge($assets, $this->resultAsset);
    }

    /**
     * Create mocks of assets to merge, as well as a few related necessary mocks
     *
     * @return array
     */
    private function getAssetsToMerge()
    {
        $one = $this->getMock('\Magento\View\Asset\File', array(), array(), '', false);
        $one->expects($this->once())->method('getSourceFile')->will($this->returnValue('/dir/file/one.txt'));
        $two = $this->getMock('\Magento\View\Asset\File',  array(), array(), '', false);
        $two->expects($this->once())->method('getSourceFile')->will($this->returnValue('/dir/file/two.txt'));
        $this->sourceDir->expects($this->exactly(2))
            ->method('getRelativePath')
            ->will($this->onConsecutiveCalls('file/one.txt', 'file/two.txt'))
        ;
        $this->sourceDir->expects($this->exactly(2))->method('stat')->will($this->returnValue(array('mtime' => '1')));
        $this->resultAsset->expects($this->once())
            ->method('getRelativePath')
            ->will($this->returnValue('merged/result.txt'))
        ;
        return array($one, $two);
    }
}
