<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Asset\MergeStrategy;

use Magento\Framework\Filesystem\Directory\Write;

class DirectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\View\Asset\MergeStrategy\Direct
     */
    protected $object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\View\Url\CssResolver
     */
    protected $cssUrlResolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $writeDir;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\View\Asset\LocalInterface
     */
    protected $resultAsset;

    protected function setUp()
    {
        $this->cssUrlResolver = $this->getMock('\Magento\Framework\View\Url\CssResolver');
        $filesystem = $this->getMock('\Magento\Framework\App\Filesystem', array(), array(), '', false);
        $this->writeDir = $this->getMockForAbstractClass('\Magento\Framework\Filesystem\Directory\WriteInterface');
        $filesystem->expects($this->any())
            ->method('getDirectoryWrite')
            ->with(\Magento\Framework\App\Filesystem::STATIC_VIEW_DIR)
            ->will($this->returnValue($this->writeDir))
        ;
        $this->resultAsset = $this->getMock('\Magento\Framework\View\Asset\File', array(), array(), '', false);
        $this->object = new Direct($filesystem, $this->cssUrlResolver);
    }

    public function testMergeNoAssets()
    {
        $this->resultAsset->expects($this->once())->method('getPath')->will($this->returnValue('foo/result'));
        $this->writeDir->expects($this->once())->method('writeFile')->with('foo/result', '');
        $this->object->merge(array(), $this->resultAsset);
    }

    public function testMergeGeneric()
    {
        $this->resultAsset->expects($this->once())->method('getPath')->will($this->returnValue('foo/result'));
        $assets = $this->prepareAssetsToMerge(array(' one', 'two')); // note leading space intentionally
        $this->writeDir->expects($this->once())->method('writeFile')->with('foo/result', 'onetwo');
        $this->object->merge($assets, $this->resultAsset);
    }

    public function testMergeCss()
    {
        $this->resultAsset->expects($this->exactly(3))
            ->method('getPath')
            ->will($this->returnValue('foo/result'))
        ;
        $this->resultAsset->expects($this->exactly(3))->method('getContentType')->will($this->returnValue('css'));
        $assets = $this->prepareAssetsToMerge(array('one', 'two'));
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
     * Prepare a few assets for merging with specified content
     *
     * @param array $data
     * @return array
     */
    private function prepareAssetsToMerge(array $data)
    {
        $result = array();
        foreach ($data as $content) {
            $asset = $this->getMockForAbstractClass('Magento\Framework\View\Asset\LocalInterface');
            $asset->expects($this->once())->method('getContent')->will($this->returnValue($content));
            $result[] = $asset;
        }
        return $result;
    }
}
