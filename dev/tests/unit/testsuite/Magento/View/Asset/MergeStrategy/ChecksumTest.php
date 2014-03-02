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
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Filesystem\Directory\WriteInterface
     */
    private $dirMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\View\Asset\LocalInterface
     */
    private $resultMock;

    /**
     * @var \Magento\View\Asset\MergeStrategy\Checksum
     */
    private $checksum;

    protected function setUp()
    {
        $this->mergerMock = $this->getMockForAbstractClass('\Magento\View\Asset\MergeStrategyInterface');
        $this->dirMock = $this->getMockForAbstractClass('\Magento\Filesystem\Directory\WriteInterface');
        $filesystem = $this->getMock('\Magento\App\Filesystem', array(), array(), '', false);
        $filesystem->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(\Magento\App\Filesystem::ROOT_DIR)
            ->will($this->returnValue($this->dirMock))
        ;
        $this->checksum = new Checksum($this->mergerMock, $filesystem);
        $this->resultMock = $this->getMockForAbstractClass('\Magento\View\Asset\LocalInterface');
    }

    public function testMergeNoAssets()
    {
        $this->mergerMock->expects($this->never())->method('merge');
        $this->checksum->merge(array(), $this->resultMock);
    }

    public function testMergeNoDatFile()
    {
        $this->dirMock->expects($this->once())
            ->method('isExist')
            ->with('merged/result.txt.dat')
            ->will($this->returnValue(false))
        ;
        $assets = $this->getAssetsToMerge();
        $this->mergerMock->expects($this->once())->method('merge')->with($assets, $this->resultMock);
        $this->dirMock->expects($this->once())->method('writeFile')->with('merged/result.txt.dat', '11');
        $this->checksum->merge($assets, $this->resultMock);
    }

    public function testMergeMtimeChanged()
    {
        $this->dirMock->expects($this->once())
            ->method('isExist')
            ->with('merged/result.txt.dat')
            ->will($this->returnValue(true))
        ;
        $this->dirMock->expects($this->once())
            ->method('readFile')
            ->with('merged/result.txt.dat')
            ->will($this->returnValue('10'))
        ;
        $assets = $this->getAssetsToMerge();
        $this->mergerMock->expects($this->once())->method('merge')->with($assets, $this->resultMock);
        $this->dirMock->expects($this->once())->method('writeFile')->with('merged/result.txt.dat', '11');
        $this->checksum->merge($assets, $this->resultMock);
    }

    public function testMergeMtimeUnchanged()
    {
        $this->dirMock->expects($this->once())
            ->method('isExist')
            ->with('merged/result.txt.dat')
            ->will($this->returnValue(true))
        ;
        $this->dirMock->expects($this->once())
            ->method('readFile')
            ->with('merged/result.txt.dat')
            ->will($this->returnValue('11'))
        ;
        $assets = $this->getAssetsToMerge();
        $this->mergerMock->expects($this->never())->method('merge');
        $this->dirMock->expects($this->never())->method('writeFile');
        $this->checksum->merge($assets, $this->resultMock);
    }

    /**
     * Create mocks of assets to merge, as well as a few related necessary mocks
     *
     * @return array
     */
    private function getAssetsToMerge()
    {
        $one = $this->getMockForAbstractClass('\Magento\View\Asset\LocalInterface');
        $one->expects($this->once())->method('getSourceFile')->will($this->returnValue('/dir/file/one.txt'));
        $two = $this->getMockForAbstractClass('\Magento\View\Asset\LocalInterface');
        $two->expects($this->once())->method('getSourceFile')->will($this->returnValue('/dir/file/two.txt'));
        $this->dirMock->expects($this->exactly(3))
            ->method('getRelativePath')
            ->will($this->onConsecutiveCalls('file/one.txt', 'file/two.txt', 'merged/result.txt.dat'))
        ;
        $this->dirMock->expects($this->exactly(2))->method('stat')->will($this->returnValue(array('mtime' => '1')));
        $this->resultMock->expects($this->once())
            ->method('getSourceFile')
            ->will($this->returnValue('/dir/merged/result.txt'))
        ;
        return array($one, $two);
    }
}
