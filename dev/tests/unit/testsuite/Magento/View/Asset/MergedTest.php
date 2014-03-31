<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

class MergedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_logger;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_mergeStrategy;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_assetJsOne;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_assetJsTwo;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystem;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_assetRepo;

    protected function setUp()
    {
        $this->_assetJsOne = $this->getMockForAbstractClass('Magento\View\Asset\MergeableInterface');
        $this->_assetJsOne->expects($this->any())->method('getContentType')->will($this->returnValue('js'));
        $this->_assetJsOne->expects($this->any())->method('getRelativePath')
            ->will($this->returnValue('script_one.js'));

        $this->_assetJsTwo = $this->getMockForAbstractClass('Magento\View\Asset\MergeableInterface');
        $this->_assetJsTwo->expects($this->any())->method('getContentType')->will($this->returnValue('js'));
        $this->_assetJsTwo->expects($this->any())->method('getRelativePath')
            ->will($this->returnValue('script_two.js'));

        $this->_logger = $this->getMock('Magento\Logger', array('logException'), array(), '', false);

        $this->_mergeStrategy = $this->getMock('Magento\View\Asset\MergeStrategyInterface');

        $this->_filesystem = $this->getMock('Magento\App\Filesystem', array(), array(), '', false);
        $this->_assetRepo = $this->getMock(
            '\Magento\View\Asset\Repository', array(), array(), '', false
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage At least one asset has to be passed for merging.
     */
    public function testConstructorNothingToMerge()
    {
        new \Magento\View\Asset\Merged(
            $this->_logger, $this->_mergeStrategy, $this->_filesystem, $this->_assetRepo, array()
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Asset has to implement \Magento\View\Asset\MergeableInterface.
     */
    public function testConstructorRequireMergeInterface()
    {
        $assetUrl = new \Magento\View\Asset\Remote('http://example.com/style.css', 'css');
        new \Magento\View\Asset\Merged(
            $this->_logger, $this->_mergeStrategy, $this->_filesystem, $this->_assetRepo,
            array($this->_assetJsOne, $assetUrl)
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Content type 'css' cannot be merged with 'js'.
     */
    public function testConstructorIncompatibleContentTypes()
    {
        $assetCss = $this->getMockForAbstractClass('Magento\View\Asset\MergeableInterface');
        $assetCss->expects($this->any())->method('getContentType')->will($this->returnValue('css'));
        new \Magento\View\Asset\Merged(
            $this->_logger, $this->_mergeStrategy, $this->_filesystem, $this->_assetRepo,
            array($this->_assetJsOne, $assetCss)
        );
    }

    public function testIteratorInterfaceMerge()
    {
        $assets = array($this->_assetJsOne, $this->_assetJsTwo);
        $this->_logger->expects($this->never())->method('logException');
        $this->_filesystem->expects($this->at(0))
            ->method('getPath')
            ->with($this->equalTo(\Magento\App\Filesystem::STATIC_VIEW_DIR))
            ->will($this->returnValue('pub/static'));
        $merged = new \Magento\View\Asset\Merged(
            $this->_logger, $this->_mergeStrategy, $this->_filesystem, $this->_assetRepo,
            $assets
        );
        $mergedAsset = $this->getMockForAbstractClass('Magento\View\Asset\MergeableInterface');
        $this->_mergeStrategy
            ->expects($this->once())
            ->method('merge')
            ->with($assets, $mergedAsset)
            ->will($this->returnValue(null));
        $this->_assetRepo->expects($this->once())->method('createFileAsset')->will($this->returnValue($mergedAsset));
        $expectedResult = array($mergedAsset);

        $this->_assertIteratorEquals($expectedResult, $merged);
        $this->_assertIteratorEquals($expectedResult, $merged); // ensure merging happens only once
    }

    public function testIteratorInterfaceMergeFailure()
    {
        $mergeError = new \Exception('File not found');
        $assetBroken = $this->getMockForAbstractClass('Magento\View\Asset\MergeableInterface');
        $assetBroken->expects($this->any())->method('getContentType')->will($this->returnValue('js'));
        $assetBroken->expects($this->any())->method('getRelativePath')
            ->will($this->throwException($mergeError));

        $merged = new \Magento\View\Asset\Merged(
            $this->_logger, $this->_mergeStrategy, $this->_filesystem, $this->_assetRepo,
            array($this->_assetJsOne, $this->_assetJsTwo, $assetBroken)
        );

        $this->_logger->expects($this->once())->method('logException')->with($this->identicalTo($mergeError));

        $expectedResult = array($this->_assetJsOne, $this->_assetJsTwo, $assetBroken);
        $this->_assertIteratorEquals($expectedResult, $merged);
        $this->_assertIteratorEquals($expectedResult, $merged); // ensure merging attempt happens only once
    }

    /**
     * Assert that iterator items equal to expected ones
     *
     * @param array $expectedItems
     * @param \Iterator $actual
     */
    protected function _assertIteratorEquals(array $expectedItems, \Iterator $actual)
    {
        $actualItems = array();
        foreach ($actual as $actualItem) {
            $actualItems[] = $actualItem;
        }
        $this->assertEquals($expectedItems, $actualItems);
    }
}
