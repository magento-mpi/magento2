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
     * @var \Magento\View\Asset\Merged
     */
    protected $_object;

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

    protected function setUp()
    {
        $this->_assetJsOne = $this->getMockForAbstractClass('Magento\View\Asset\MergeableInterface');
        $this->_assetJsOne->expects($this->any())->method('getContentType')->will($this->returnValue('js'));
        $this->_assetJsOne->expects($this->any())->method('getSourceFile')
            ->will($this->returnValue('pub/lib/script_one.js'));

        $this->_assetJsTwo = $this->getMockForAbstractClass('Magento\View\Asset\MergeableInterface');
        $this->_assetJsTwo->expects($this->any())->method('getContentType')->will($this->returnValue('js'));
        $this->_assetJsTwo->expects($this->any())->method('getSourceFile')
            ->will($this->returnValue('pub/static/script_two.js'));

        $this->_logger = $this->getMock('Magento\Logger', array('logException'), array(), '', false);

        $this->_mergeStrategy = $this->getMock('Magento\View\Asset\MergeStrategyInterface');

        $this->_objectManager = $this->getMockForAbstractClass(
            'Magento\ObjectManager', array(), '', true, true, true, array('create', 'get')
        );

        $this->_filesystem = $this->getMock(
            'Magento\Filesystem', array(), array(), '', false);
        $this->_objectManager->expects($this->any())
            ->method('get')
            ->will($this->returnValue($this->_filesystem));

        $this->_object = new \Magento\View\Asset\Merged(
            $this->_objectManager, $this->_logger, $this->_mergeStrategy,
            array($this->_assetJsOne, $this->_assetJsTwo)
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage At least one asset has to be passed for merging.
     */
    public function testConstructorNothingToMerge()
    {
        $this->_object = new \Magento\View\Asset\Merged(
            $this->_objectManager, $this->_logger, $this->_mergeStrategy, array()
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Asset has to implement \Magento\View\Asset\MergeableInterface.
     */
    public function testConstructorRequireMergeInterface()
    {
        $assetUrl = new \Magento\View\Asset\Remote('http://example.com/style.css', 'css');
        $this->_object = new \Magento\View\Asset\Merged(
            $this->_objectManager, $this->_logger, $this->_mergeStrategy,
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
        $assetCss->expects($this->any())->method('getSourceFile')->will($this->returnValue('style.css'));

        $this->_object = new \Magento\View\Asset\Merged(
            $this->_objectManager, $this->_logger, $this->_mergeStrategy,
            array($this->_assetJsOne, $assetCss)
        );
    }

    public function testIteratorInterfaceMerge()
    {
        $hash = md5(implode('|', array('script_one.js', 'script_two.js')));
        $mergedFile = 'pub/cache/_merged/' . $hash . '.js';

        $this->_logger->expects($this->never())->method('logException');

        $publicFiles = array(
            'pub/lib/script_one.js' => 'pub/lib/script_one.js',
            'pub/static/script_two.js' => 'pub/static/script_two.js'
        );

        $this->_filesystem->expects($this->at(0))
            ->method('getPath')
            ->with($this->equalTo(\Magento\Filesystem::PUB_LIB))
            ->will($this->returnValue('pub/lib'));
        $this->_filesystem->expects($this->at(1))
            ->method('getPath')
            ->with($this->equalTo(\Magento\Filesystem::STATIC_VIEW))
            ->will($this->returnValue('pub/static'));
        $readDirectoryMock = $this->getMockBuilder('\Magento\Filesystem\Directory\Read')
            ->disableOriginalConstructor()
            ->getMock();
        $merged = $this->_object;
        $readDirectoryMock->expects($this->once())
            ->method('getAbsolutePath')
            ->with($this->equalTo($merged::PUBLIC_MERGE_DIR))
            ->will($this->returnValue('pub/cache/_merged'));

        $this->_filesystem->expects($this->any())
            ->method('getDirectoryRead')
            ->with($this->equalTo(\Magento\Filesystem::PUB_VIEW_CACHE))
            ->will($this->returnValue($readDirectoryMock));

        $this->_mergeStrategy
            ->expects($this->once())
            ->method('mergeFiles')
            ->with($publicFiles, $mergedFile, 'js')
            ->will($this->returnValue(null));

        $mergedAsset = $this->getMockForAbstractClass('Magento\View\Asset\MergeableInterface');
        $this->_objectManager
            ->expects($this->once())
            ->method('create')
            ->with('Magento\View\Asset\PublicFile', array('file' => $mergedFile, 'contentType' => 'js'))
            ->will($this->returnValue($mergedAsset))
        ;

        $expectedResult = array($mergedAsset);

        $this->_assertIteratorEquals($expectedResult, $this->_object);
        $this->_assertIteratorEquals($expectedResult, $this->_object); // ensure merging happens only once
    }

    public function testIteratorInterfaceMergeFailure()
    {
        $mergeError = new \Exception('File not found');
        $assetBroken = $this->getMockForAbstractClass('Magento\View\Asset\MergeableInterface');
        $assetBroken->expects($this->any())->method('getContentType')->will($this->returnValue('js'));
        $assetBroken->expects($this->any())->method('getSourceFile')
            ->will($this->throwException($mergeError));

        $this->_object = new \Magento\View\Asset\Merged(
            $this->_objectManager, $this->_logger, $this->_mergeStrategy,
            array($this->_assetJsOne, $this->_assetJsTwo, $assetBroken)
        );


        $this->_objectManager->expects($this->never())->method('create');
        $this->_logger->expects($this->once())->method('logException')->with($this->identicalTo($mergeError));

        $expectedResult = array($this->_assetJsOne, $this->_assetJsTwo, $assetBroken);
        $this->_assertIteratorEquals($expectedResult, $this->_object);
        $this->_assertIteratorEquals($expectedResult, $this->_object); // ensure merging attempt happens only once
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
