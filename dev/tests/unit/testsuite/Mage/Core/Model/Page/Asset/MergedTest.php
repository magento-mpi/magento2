<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Page_Asset_MergedTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Page_Asset_Merged
     */
    protected $_object;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_designPackage;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_logger;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cssHelper;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystem;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirs;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_assetJsOne;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_assetJsTwo;

    protected function setUp()
    {
        $this->_assetJsOne = $this->getMockForAbstractClass('Mage_Core_Model_Page_Asset_MergeableInterface');
        $this->_assetJsOne->expects($this->any())->method('getContentType')->will($this->returnValue('js'));
        $this->_assetJsOne->expects($this->any())->method('getSourceFile')->will($this->returnValue('script_one.js'));

        $this->_assetJsTwo = $this->getMockForAbstractClass('Mage_Core_Model_Page_Asset_MergeableInterface');
        $this->_assetJsTwo->expects($this->any())->method('getContentType')->will($this->returnValue('js'));
        $this->_assetJsTwo->expects($this->any())->method('getSourceFile')->will($this->returnValue('script_two.js'));

        $this->_designPackage = $this->getMock('Mage_Core_Model_Design_PackageInterface');

        $this->_logger = $this->getMock('Mage_Core_Model_Logger', array('logException'), array(), '', false);

        $this->_cssHelper = $this->getMock('Mage_Core_Helper_Css_Processing', array(), array(), '', false);

        $this->_filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);

        $this->_dirs = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false);

        $this->_objectManager = $this->getMockForAbstractClass(
            'Magento_ObjectManager', array(), '', true, true, true, array('create')
        );

        $this->_object = new Mage_Core_Model_Page_Asset_Merged(
            $this->_objectManager, $this->_designPackage, $this->_logger, $this->_cssHelper, $this->_filesystem,
            $this->_dirs, array($this->_assetJsOne, $this->_assetJsTwo)
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage At least one asset has to be passed for merging.
     */
    public function testConstructorNothingToMerge()
    {
        $this->_object = new Mage_Core_Model_Page_Asset_Merged(
            $this->_objectManager, $this->_designPackage, $this->_logger, $this->_cssHelper, $this->_filesystem,
            $this->_dirs, array()
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Asset has to implement Mage_Core_Model_Page_Asset_MergeableInterface.
     */
    public function testConstructorRequireMergeInterface()
    {
        $assetUrl = new Mage_Core_Model_Page_Asset_Remote('http://example.com/style.css', 'css');
        $this->_object = new Mage_Core_Model_Page_Asset_Merged(
            $this->_objectManager, $this->_designPackage, $this->_logger, $this->_cssHelper, $this->_filesystem,
            $this->_dirs, array($this->_assetJsOne, $assetUrl)
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Content type 'css' cannot be merged with 'js'.
     */
    public function testConstructorIncompatibleContentTypes()
    {
        $assetCss = $this->getMockForAbstractClass('Mage_Core_Model_Page_Asset_MergeableInterface');
        $assetCss->expects($this->any())->method('getContentType')->will($this->returnValue('css'));
        $assetCss->expects($this->any())->method('getSourceFile')->will($this->returnValue('style.css'));

        $this->_object = new Mage_Core_Model_Page_Asset_Merged(
            $this->_objectManager, $this->_designPackage, $this->_logger, $this->_cssHelper, $this->_filesystem,
            $this->_dirs, array($this->_assetJsOne, $assetCss)
        );
    }

    /**
     * Verify usual merger scenario - source files are found, merged and written to the resulting file and meta
     * data files
     */
    public function testIteratorInterfaceMerge()
    {
        $mergedFile = '/_merged/19b2d7c942efeb2327eadbcf04635b02.js';

        $this->_designPackage
            ->expects($this->at(0))
            ->method('getViewFilePublicPath')
            ->with('script_one.js')
            ->will($this->returnValue('/pub/script_one.js'))
        ;
        $this->_designPackage
            ->expects($this->at(1))
            ->method('getViewFilePublicPath')
            ->with('script_two.js')
            ->will($this->returnValue('/pub/script_two.js'))
        ;

        $this->_filesystem->expects($this->any())
            ->method('has')
            ->will($this->returnValueMap(
                array(
                    array('/pub/script_one.js', null, true),
                    array('/pub/script_two.js', null, true),
                )
            ));
        $this->_filesystem->expects($this->exactly(2))
            ->method('getMTime')
            ->will($this->returnValueMap(
                array(
                    array('/pub/script_one.js', null, '123'),
                    array('/pub/script_two.js', null, '456'),
                )
            ));
        $this->_filesystem->expects($this->exactly(2))
            ->method('read')
            ->will($this->returnValueMap(
                array(
                    array('/pub/script_one.js', null, 'script1'),
                    array('/pub/script_two.js', null, 'script2'),
                )
            ));

        $writtenData = array();
        $writeCallback = function ($file, $content) use (&$writtenData) {
            $writtenData[] = array($file, $content);
        };
        $this->_filesystem->expects($this->exactly(2))
            ->method('write')
            ->will($this->returnCallback($writeCallback));

        $this->_logger->expects($this->never())->method('logException');

        $mergedAsset = $this->getMockForAbstractClass('Mage_Core_Model_Page_Asset_MergeableInterface');
        $this->_objectManager
            ->expects($this->once())
            ->method('create')
            ->with('Mage_Core_Model_Page_Asset_PublicFile', array('file' => $mergedFile, 'contentType' => 'js'))
            ->will($this->returnValue($mergedAsset))
        ;

        // Check
        $expectedResult = array($mergedAsset);
        $this->_assertIteratorEquals($expectedResult, $this->_object);
        $this->_assertIteratorEquals($expectedResult, $this->_object); // ensure merging happens only once

        $expectedWrittenData = array(
            array($mergedFile, 'script1script2'),
            array($mergedFile . '.dat', '123456'),
        );
        $this->assertEquals($expectedWrittenData, $writtenData);
    }

    /**
     * Verify standard merger scenario - files are already merged, resulting file and meta file already exist
     */
    public function testIteratorInterfaceMergeWithCachedFile()
    {
        $mergedFile = '/_merged/19b2d7c942efeb2327eadbcf04635b02.js';
        $mergedMetaFile = $mergedFile . '.dat';

        $this->_designPackage
            ->expects($this->at(0))
            ->method('getViewFilePublicPath')
            ->with('script_one.js')
            ->will($this->returnValue('/pub/script_one.js'))
        ;
        $this->_designPackage
            ->expects($this->at(1))
            ->method('getViewFilePublicPath')
            ->with('script_two.js')
            ->will($this->returnValue('/pub/script_two.js'))
        ;

        $this->_filesystem->expects($this->exactly(2))
            ->method('getMTime')
            ->will($this->returnValueMap(
                array(
                    array('/pub/script_one.js', null, '123'),
                    array('/pub/script_two.js', null, '456'),
                )
            ));
        $this->_filesystem->expects($this->any())
            ->method('has')
            ->will($this->returnValueMap(
                array(
                    array($mergedFile, null, true),
                    array($mergedMetaFile, null, true),
                )
            ));
        $this->_filesystem->expects($this->exactly(1))
            ->method('read')
            ->with($mergedMetaFile)
            ->will($this->returnValue('123456'));
        $this->_filesystem->expects($this->never())
            ->method('write');

        $this->_logger->expects($this->never())->method('logException');

        $mergedAsset = $this->getMockForAbstractClass('Mage_Core_Model_Page_Asset_MergeableInterface');
        $this->_objectManager
            ->expects($this->once())
            ->method('create')
            ->with('Mage_Core_Model_Page_Asset_PublicFile', array('file' => $mergedFile, 'contentType' => 'js'))
            ->will($this->returnValue($mergedAsset))
        ;

        $expectedResult = array($mergedAsset);
        $this->_assertIteratorEquals($expectedResult, $this->_object);
        $this->_assertIteratorEquals($expectedResult, $this->_object); // ensure merging happens only once
    }

    public function testIteratorInterfaceMergeFailure()
    {
        $mergeError = new Exception('Merge has failed');
        $this->_designPackage->expects($this->once())
            ->method('getViewFilePublicPath')
            ->with('script_one.js', array())
            ->will($this->throwException($mergeError))
        ;
        $this->_objectManager->expects($this->never())->method('create');
        $this->_logger->expects($this->once())->method('logException')->with($this->identicalTo($mergeError));

        $expectedResult = array($this->_assetJsOne, $this->_assetJsTwo);
        $this->_assertIteratorEquals($expectedResult, $this->_object);
        $this->_assertIteratorEquals($expectedResult, $this->_object); // ensure merging attempt happens only once
    }

    /**
     * Assert that iterator items equal to expected ones
     *
     * @param array $expectedItems
     * @param Iterator $actual
     */
    protected function _assertIteratorEquals(array $expectedItems, Iterator $actual)
    {
        $actualItems = array();
        foreach ($actual as $actualItem) {
            $actualItems[] = $actualItem;
        }
        $this->assertEquals($expectedItems, $actualItems);
    }
}
