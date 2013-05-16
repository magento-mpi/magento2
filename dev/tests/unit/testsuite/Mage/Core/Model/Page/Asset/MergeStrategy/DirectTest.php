<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Page_Asset_MergeStrategy_DirectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Page_Asset_MergeStrategy_Direct
     */
    protected $_object;

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
    protected $_cssHelper;

    protected function setUp()
    {
        $this->_cssHelper = $this->getMock('Mage_Core_Helper_Css', array(), array(), '', false);
        $this->_filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $this->_dirs = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false);

        $this->_object = new Mage_Core_Model_Page_Asset_MergeStrategy_Direct(
            $this->_filesystem, $this->_dirs, $this->_cssHelper
        );
    }

    /**
     * @expectedException Magento_Exception
     * @expectedExceptionMessage Unable to locate file 'no_file.js' for merging.
     */
    public function testMergeFilesNoFilesException()
    {
        $this->_object->mergeFiles(array('no_file.js'), 'some_file.js');
    }

    /**
     * Tests mergeFiles() and setIsCss(true)
     */
    public function testMergeFilesCssTrue()
    {
        $this->_object->setIsCss(true);
        $this->_cssHelper
            ->expects($this->exactly(2))
            ->method('replaceCssRelativeUrls')
            ->will($this->returnArgument(0));
        $this->_testMergeFiles();
    }

    /**
     * Tests mergeFiles() and setIsCss(false)
     */
    public function testMergeFilesCssFalse()
    {
        $this->_object->setIsCss(false);
        $this->_cssHelper
            ->expects($this->never())
            ->method('replaceCssRelativeUrls');
        $this->_testMergeFiles();
    }

    /**
     * Test mergeFiles itself
     */
    protected function _testMergeFiles()
    {
        $mergedFile = '/merged_file.js';

        $this->_filesystem->expects($this->exactly(2))
            ->method('has')
            ->will($this->returnValueMap(
                array(
                    array('/pub/script_one.js', null, true),
                    array('/pub/script_two.js', null, true),
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

        $this->_filesystem
            ->expects($this->once())
            ->method('setIsAllowCreateDirectories')
            ->with(true);

        $this->_filesystem->expects($this->once())
            ->method('write')
            ->with($mergedFile, 'script1script2')
        ;

        $this->_object->mergeFiles(array('/pub/script_one.js', '/pub/script_two.js'), $mergedFile);
    }
}
