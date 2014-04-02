<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset\MergeStrategy;

class DirectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Asset\MergeStrategy\Direct
     */
    protected $object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\View\Url\CssResolver
     */
    protected $cssUrlResolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Filesystem\Directory\WriteInterface
     */
    protected $writeDir;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Filesystem\Directory\ReadInterface
     */
    protected $readDir;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\View\Asset\LocalInterface
     */
    protected $resultAsset;

    protected function setUp()
    {
        $this->cssUrlResolver = $this->getMock('\Magento\View\Url\CssResolver');
        $filesystem = $this->getMock('\Magento\App\Filesystem', array(), array(), '', false);
        $this->writeDir = $this->getMockForAbstractClass('\Magento\Filesystem\Directory\WriteInterface');
        $filesystem->expects($this->any())
            ->method('getDirectoryWrite')
            ->with(\Magento\App\Filesystem::STATIC_VIEW_DIR)
            ->will($this->returnValue($this->writeDir))
        ;
        $this->readDir = $this->getMockForAbstractClass('\Magento\Filesystem\Directory\ReadInterface');
        $filesystem->expects($this->once())
            ->method('getDirectoryRead')
            ->with(\Magento\App\Filesystem::ROOT_DIR)
            ->will($this->returnValue($this->readDir))
        ;
        $this->resultAsset = $this->getMock('\Magento\View\Asset\File', array(), array(), '', false);
        $this->object = new Direct($filesystem, $this->cssUrlResolver);
    }

    public function testMergeNoAssets()
    {
        $this->resultAsset->expects($this->once())->method('getRelativePath')->will($this->returnValue('foo/result'));
        $this->writeDir->expects($this->once())->method('writeFile')->with('foo/result', '');
        $this->object->merge(array(), $this->resultAsset);
    }

    public function testMergeGeneric()
    {
        $this->resultAsset->expects($this->once())->method('getRelativePath')->will($this->returnValue('foo/result'));
        $assets = $this->prepareAssetsToMerge(array(' one', 'two')); // note leading space intentionally
        $this->readDir->expects($this->exactly(2))->method('isExist')->will($this->returnValue(true));
        $this->writeDir->expects($this->once())->method('writeFile')->with('foo/result', 'onetwo');
        $this->object->merge($assets, $this->resultAsset);
    }

    public function testMergeCss()
    {
        $this->resultAsset->expects($this->exactly(3))
            ->method('getRelativePath')
            ->will($this->returnValue('foo/result'))
        ;
        $this->resultAsset->expects($this->exactly(3))->method('getContentType')->will($this->returnValue('css'));
        $assets = $this->prepareAssetsToMerge(array('one', 'two'));
        $this->readDir->expects($this->exactly(2))->method('isExist')->will($this->returnValue(true));
        $this->cssUrlResolver->expects($this->exactly(2))
            ->method('relocateRelativeUrls')
            ->will($this->onConsecutiveCalls('1', '2'))
        ;
        $this->cssUrlResolver->expects($this->once())
            ->method('aggregateImportDirectives')
            ->with('12')
            ->will($this->returnValue('1020'))
        ;
        $this->writeDir->expects($this->once())->method('writeFile')->with('foo/result', '1020');
        $this->object->merge($assets, $this->resultAsset);
    }

    /**
     * @expectedException \Magento\Exception
     * @expectedExceptionMessage Unable to locate file 'foo' for merging.
     */
    public function testMergeFileNotExists()
    {
        $assets = $this->prepareAssetsToMerge(array('one', 'two'));
        $this->readDir->expects($this->once())
            ->method('getRelativePath')
            ->with('/absolute/path.ext')
            ->will($this->returnValue('foo'))
        ;
        $this->readDir->expects($this->once())->method('isExist')->will($this->returnValue(false));
        $this->writeDir->expects($this->never())->method('writeFile');
        $this->object->merge($assets, $this->resultAsset);
    }

    /**
     * Prepare a few assets for merging with specified content
     *
     * @param array $data
     * @return array
     */
    private function prepareAssetsToMerge(array $data)
    {
        $result = array();
        /** @var \Magento\View\Asset\File|\PHPUnit_Framework_MockObject_MockObject $fileMock */
        $fileMock = $this->getMock('Magento\View\Asset\File', array(), array(), '', false);
        $fileMock->expects($this->any())->method('getSourceFile')->will($this->returnValue('/absolute/path.ext'));
        $fileMock->expects($this->any())->method('getFilePath')->will($this->returnValue('path.ext'));
        for ($i = 0; $i < count($data); $i++) {
            $result[] = $fileMock;
        }
        $this->readDir->expects($this->any())
            ->method('readFile')
            ->will(call_user_func_array(array($this, 'onConsecutiveCalls'), $data))
        ;
        return $result;
    }
}
